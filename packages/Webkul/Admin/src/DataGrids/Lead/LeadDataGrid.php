<?php

namespace Webkul\Admin\DataGrids\Lead;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Webkul\Attribute\Models\AttributeValue;
use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\DataGrid\DataGrid;
use Webkul\Lead\Repositories\PipelineRepository;
use Webkul\Lead\Repositories\SourceRepository;
use Webkul\Lead\Repositories\StageRepository;
use Webkul\Lead\Repositories\TypeRepository;
use Webkul\Tag\Repositories\TagRepository;
use Webkul\User\Repositories\UserRepository;

class LeadDataGrid extends DataGrid
{
    /**
     * Pipeline instance.
     *
     * @var \Webkul\Contract\Repositories\Pipeline
     */
    protected $pipeline;

    /**
     * Cached attribute column metadata.
     */
    protected array $attributeColumns = [];

    /**
     * Indicates if leads table contains lead_type_id column.
     */
    protected bool $hasLeadTypeColumn = false;

    /**
     * Create data grid instance.
     *
     * @return void
     */
    public function __construct(
        protected PipelineRepository $pipelineRepository,
        protected StageRepository $stageRepository,
        protected SourceRepository $sourceRepository,
        protected TypeRepository $typeRepository,
        protected UserRepository $userRepository,
        protected AttributeRepository $attributeRepository,
        protected TagRepository $tagRepository,
    ) {
        if (request('pipeline_id')) {
            $this->pipeline = $this->pipelineRepository->find(request('pipeline_id'));
        } else {
            $this->pipeline = $this->pipelineRepository->getDefaultPipeline();
        }

        $this->hasLeadTypeColumn = Schema::hasColumn('leads', 'lead_type_id');
        $this->attributeColumns = $this->initializeAttributeColumns();
    }

    /**
     * Prepare metadata for attribute-backed columns that should appear in the table view.
     */
    protected function initializeAttributeColumns(): array
    {
        $definitions = [
            'quals_due_date' => [
                'alias'        => 'quals_due_date',
                'label'        => trans('admin::app.leads.index.datagrid.quals-due-date'),
                'display_type' => 'date',
                'format'       => 'd M Y',
            ],
            'proposal_due_date' => [
                'alias'        => 'proposal_due_date',
                'label'        => trans('admin::app.leads.index.datagrid.proposal-due-date'),
                'display_type' => 'date',
                'format'       => 'd M Y',
            ],
            'presentation_datetime' => [
                'alias'        => 'presentation_datetime',
                'label'        => trans('admin::app.leads.index.datagrid.presentation-datetime'),
                'display_type' => 'datetime',
                'format'       => 'd M Y h:i A',
            ],
            'total_estimated_fee' => [
                'alias'        => 'total_estimated_fee',
                'label'        => trans('admin::app.leads.index.datagrid.total-estimated-fee'),
                'display_type' => 'currency',
            ],
            'firm_estimated_fee' => [
                'alias'        => 'firm_estimated_fee',
                'label'        => trans('admin::app.leads.index.datagrid.firm-estimated-fee'),
                'display_type' => 'currency',
            ],
            'win_probability' => [
                'alias'        => 'win_probability',
                'label'        => trans('admin::app.leads.index.datagrid.win-probability'),
                'display_type' => 'percentage',
            ],
        ];

        $columns = [];

        foreach ($definitions as $code => $meta) {
            $attribute = $this->attributeRepository->getAttributeByCode($code);

            if (! $attribute || $attribute->entity_type !== 'leads') {
                continue;
            }

            $storageColumn = AttributeValue::$attributeTypeFields[$attribute->type] ?? null;

            if (! $storageColumn) {
                continue;
            }

            $columns[$code] = array_merge($meta, [
                'attribute'      => $attribute,
                'storage_column' => $storageColumn,
            ]);
        }

        return $columns;
    }

    /**
     * Prepare query builder.
     */
    public function prepareQueryBuilder(): Builder
    {
        $tablePrefix = DB::getTablePrefix();

        $queryBuilder = DB::table('leads')
            ->addSelect(
                'leads.id',
                'leads.title',
                'leads.status',
                'leads.lead_value',
                'leads.client_company',
                'leads.owner_company',
                'leads.opportunity_number',
                'leads.proposal_manager',
                'leads.description',
                'leads.created_at',
                DB::raw('leads.staff_representative as staff_representative'),
                'lead_pipeline_stages.name as stage',
                'lead_tags.tag_id as tag_id',
                'persons.id as person_id',
                'persons.name as person_name',
                'tags.name as tag_name',
                'lead_pipelines.rotten_days as pipeline_rotten_days',
                'lead_pipeline_stages.code as stage_code',
                DB::raw('CASE WHEN DATEDIFF(NOW(),'.$tablePrefix.'leads.created_at) >='.$tablePrefix.'lead_pipelines.rotten_days THEN 1 ELSE 0 END as rotten_lead'),
            )
            ->leftJoin('persons', 'leads.person_id', '=', 'persons.id')
            ->leftJoin('lead_pipeline_stages', 'leads.lead_pipeline_stage_id', '=', 'lead_pipeline_stages.id')
            ->leftJoin('lead_pipelines', 'leads.lead_pipeline_id', '=', 'lead_pipelines.id')
            ->leftJoin('lead_tags', 'leads.id', '=', 'lead_tags.lead_id')
            ->leftJoin('tags', 'tags.id', '=', 'lead_tags.tag_id')
            ->groupBy('leads.id')
            ->where('leads.lead_pipeline_id', $this->pipeline->id);

        if ($this->hasLeadTypeColumn) {
            $queryBuilder->addSelect('lead_types.name as lead_type_name')
                ->leftJoin('lead_types', 'leads.lead_type_id', '=', 'lead_types.id');
        }

        if (! empty($this->attributeColumns)) {
            $queryBuilder->leftJoin('attribute_values as lead_attribute_values', function ($join) {
                $join->on('leads.id', '=', 'lead_attribute_values.entity_id')
                    ->where('lead_attribute_values.entity_type', '=', 'leads');
            });

            foreach ($this->attributeColumns as $column) {
                $queryBuilder->addSelect(DB::raw(
                    "MAX(CASE WHEN lead_attribute_values.attribute_id = {$column['attribute']->id} THEN lead_attribute_values.{$column['storage_column']} END) as {$column['alias']}"
                ));
            }
        }

        if ($userIds = bouncer()->getAuthorizedUserIds()) {
            $queryBuilder->whereIn('leads.user_id', $userIds);
        }

        if (! is_null(request()->input('rotten_lead.in'))) {
            $queryBuilder->havingRaw($tablePrefix.'rotten_lead = '.request()->input('rotten_lead.in'));
        }

        $this->addFilter('id', 'leads.id');
        $this->addFilter('person_name', 'persons.name');
        $this->addFilter('type', 'lead_pipeline_stages.code');
        $this->addFilter('stage', 'lead_pipeline_stages.id');
        $this->addFilter('tag_name', 'tags.name');
        $this->addFilter('created_at', 'leads.created_at');
        $this->addFilter('rotten_lead', DB::raw('DATEDIFF(NOW(), '.$tablePrefix.'leads.created_at) >= '.$tablePrefix.'lead_pipelines.rotten_days'));
        $this->addFilter('opportunity_number', 'leads.opportunity_number');
        $this->addFilter('client_company', 'leads.client_company');
        $this->addFilter('owner_company', 'leads.owner_company');
        if ($this->hasLeadTypeColumn) {
            $this->addFilter('lead_type_name', 'lead_types.id');
        }
        $this->addFilter('proposal_manager', 'leads.proposal_manager');

        return $queryBuilder;
    }

    /**
     * Prepare columns.
     */
    public function prepareColumns(): void
    {
        $this->addColumn([
            'index'      => 'opportunity_number',
            'label'      => trans('admin::app.leads.index.datagrid.proposal-number'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'title',
            'label'      => trans('admin::app.leads.index.datagrid.project-name'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'client_company',
            'label'      => trans('admin::app.leads.index.datagrid.client-company'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'owner_company',
            'label'      => trans('admin::app.leads.index.datagrid.owner-company'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
        ]);

        if ($this->hasLeadTypeColumn) {
            $this->addColumn([
                'index'              => 'lead_type_name',
                'label'              => trans('admin::app.leads.index.datagrid.lead-role'),
                'type'               => 'string',
                'searchable'         => false,
                'sortable'           => true,
                'filterable'         => true,
                'filterable_type'    => 'dropdown',
                'filterable_options' => $this->typeRepository->all()
                    ->map(fn ($type) => ['value' => $type->id, 'label' => $type->name])
                    ->values()
                    ->toArray(),
                'closure'            => fn ($row) => $row->lead_type_name ?? '--',
            ]);
        }

        $this->addColumn([
            'index'      => 'proposal_manager',
            'label'      => trans('admin::app.leads.index.datagrid.proposal-manager'),
            'type'       => 'string',
            'searchable' => false,
            'sortable'   => false,
            'filterable' => false,
            'closure'    => fn ($row) => $row->proposal_manager ?? '--',
        ]);

        $this->addColumn([
            'index'      => 'staff_representative',
            'label'      => trans('admin::app.leads.index.datagrid.staff-representative'),
            'type'       => 'string',
            'searchable' => false,
            'sortable'   => false,
            'filterable' => false,
            'closure'    => fn ($row) => $row->staff_representative ?? '--',
        ]);

        foreach ($this->attributeColumns as $column) {
            $this->addColumn([
                'index'      => $column['alias'],
                'label'      => $column['label'],
                'type'       => in_array($column['display_type'], ['date', 'datetime'], true) ? 'date' : 'string',
                'searchable' => false,
                'sortable'   => false,
                'filterable' => false,
                'closure'    => function ($row) use ($column) {
                    $value = $row->{$column['alias']} ?? null;

                    if (! $value) {
                        return '--';
                    }

                    return match ($column['display_type']) {
                        'date'      => core()->formatDate($value, $column['format'] ?? 'd M Y'),
                        'datetime'  => core()->formatDate($value, $column['format'] ?? 'd M Y h:i A'),
                        'currency'  => core()->formatBasePrice((float) $value),
                        'percentage'=> (is_numeric($value)
                            ? rtrim(rtrim(number_format((float) $value, 2, '.', ''), '0'), '.')
                            : $value).'%',
                        default     => $value,
                    };
                },
            ]);
        }

        $this->addColumn([
            'index'      => 'lead_value',
            'label'      => __('Opportunity Value'),
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => false,
            'filterable' => true,
            'closure'    => fn ($row) => core()->formatBasePrice($row->lead_value, 2),
        ]);

        $this->addColumn([
            'index'      => 'description',
            'label'      => trans('admin::app.leads.index.datagrid.comments'),
            'type'       => 'string',
            'searchable' => false,
            'sortable'   => false,
            'filterable' => false,
            'closure'    => fn ($row) => $row->description
                ? Str::limit(strip_tags($row->description), 80)
                : '--',
        ]);

        $this->addColumn([
            'index'              => 'stage',
            'label'              => trans('admin::app.leads.index.datagrid.stage'),
            'type'               => 'string',
            'searchable'         => false,
            'sortable'           => true,
            'filterable'         => true,
            'filterable_type'    => 'dropdown',
            'filterable_options' => $this->pipeline->stages->pluck('name', 'id')
                ->map(function ($name, $id) {
                    return ['value' => $id, 'label' => $name];
                })
                ->values()
                ->all(),
        ]);

        $this->addColumn([
            'index'              => 'person_name',
            'label'              => trans('admin::app.leads.index.datagrid.contact-person'),
            'type'               => 'string',
            'searchable'         => false,
            'sortable'           => true,
            'filterable'         => true,
            'filterable_type'    => 'searchable_dropdown',
            'filterable_options' => [
                'repository' => \Webkul\Contact\Repositories\PersonRepository::class,
                'column'     => [
                    'label' => 'name',
                    'value' => 'name',
                ],
            ],
            'closure'            => function ($row) {
                if (!$row->person_id || !$row->person_name) {
                    return '--';
                }

                $route = route('admin.contacts.persons.view', $row->person_id);

                return "<a class=\"text-brandColor transition-all hover:underline\" href='".$route."'>".$row->person_name.'</a>';
            },
        ]);

    }

    /**
     * Prepare actions.
     */
    public function prepareActions(): void
    {
        if (bouncer()->hasPermission('leads.view')) {
            $this->addAction([
                'icon'   => 'icon-eye',
                'title'  => trans('admin::app.leads.index.datagrid.view'),
                'method' => 'GET',
                'url'    => fn ($row) => route('admin.leads.view', $row->id),
            ]);
        }

        if (bouncer()->hasPermission('leads.delete')) {
            $this->addAction([
                'icon'   => 'icon-delete',
                'title'  => trans('admin::app.leads.index.datagrid.delete'),
                'method' => 'delete',
                'url'    => fn ($row) => route('admin.leads.delete', $row->id),
            ]);
        }
    }

    /**
     * Prepare mass actions.
     */
    public function prepareMassActions(): void
    {
        $this->addMassAction([
            'icon'   => 'icon-delete',
            'title'  => trans('admin::app.leads.index.datagrid.mass-delete'),
            'method' => 'POST',
            'url'    => route('admin.leads.mass_delete'),
        ]);

        $this->addMassAction([
            'title'   => trans('admin::app.leads.index.datagrid.mass-update'),
            'url'     => route('admin.leads.mass_update'),
            'method'  => 'POST',
            'options' => $this->pipeline->stages->map(fn ($stage) => [
                'label' => $stage->name,
                'value' => $stage->id,
            ])->toArray(),
        ]);
    }
}







