<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Webkul\Lead\Repositories\LeadRepository;
use Webkul\Lead\Repositories\PipelineRepository;
use Webkul\Lead\Repositories\SourceRepository;
use Webkul\Lead\Repositories\TypeRepository;

beforeEach(function () {
    $this->admin = getDefaultAdmin();

    $this->leadRepository = app(LeadRepository::class);
    $this->pipelineRepository = app(PipelineRepository::class);
    $this->sourceRepository = app(SourceRepository::class);
    $this->typeRepository = app(TypeRepository::class);

    $this->pipeline = $this->pipelineRepository->getDefaultPipeline();
    $this->stage = $this->pipeline->stages()->orderBy('sort_order')->first();
    $this->alternateStage = $this->pipeline->stages()->orderBy('sort_order')->skip(1)->first() ?? $this->stage;
    $this->source = $this->sourceRepository->first();
    $this->type = $this->typeRepository->first();

    expect($this->pipeline)->not->toBeNull();
    expect($this->stage)->not->toBeNull();
    expect($this->source)->not->toBeNull();
    expect($this->type)->not->toBeNull();
});

it('creates an opportunity with a primary contact', function () {
    $lead = $this->leadRepository->create([
        'entity_type'            => 'leads',
        'title'                  => 'QA Opportunity',
        'description'            => 'Created during automated QA.',
        'user_id'                => $this->admin->id,
        'lead_value'             => 10000,
        'status'                 => 1,
        'lead_source_id'         => $this->source->id,
        'lead_type_id'           => $this->type->id,
        'lead_pipeline_id'       => $this->pipeline->id,
        'lead_pipeline_stage_id' => $this->stage->id,
        'person'                 => [
            'entity_type'      => 'persons',
            'name'             => 'Automation Contact',
            'emails'           => [
                ['value' => 'qa-contact@example.com', 'label' => 'work', 'primary' => 1],
            ],
            'contact_numbers'  => [
                ['value' => '1234567890', 'label' => 'mobile', 'primary' => 1],
            ],
            'organization_id'  => null,
            'user_id'          => $this->admin->id,
        ],
    ]);

    expect($lead->exists())->toBeTrue();
    expect($lead->person)->not->toBeNull();
    expect($lead->title)->toBe('QA Opportunity');
});

it('updates an existing opportunity and tracks stage changes', function () {
    $lead = $this->leadRepository->create([
        'entity_type'            => 'leads',
        'title'                  => 'Stage Test Opportunity',
        'user_id'                => $this->admin->id,
        'lead_value'             => 2500,
        'status'                 => 1,
        'lead_source_id'         => $this->source->id,
        'lead_type_id'           => $this->type->id,
        'lead_pipeline_id'       => $this->pipeline->id,
        'lead_pipeline_stage_id' => $this->stage->id,
        'person'                 => [
            'entity_type' => 'persons',
            'name'        => 'Stage Contact',
            'emails'      => [
                ['value' => 'stage-contact@example.com', 'label' => 'work', 'primary' => 1],
            ],
        ],
    ]);

    $updated = $this->leadRepository->update([
        'entity_type'            => 'leads',
        'title'                  => 'Stage Test Opportunity - Updated',
        'lead_value'             => 3500,
        'lead_pipeline_id'       => $this->pipeline->id,
        'lead_pipeline_stage_id' => $this->alternateStage->id,
        'lead_source_id'         => $this->source->id,
        'lead_type_id'           => $this->type->id,
        'user_id'                => $this->admin->id,
        'person'                 => [
            'entity_type' => 'persons',
            'id'          => $lead->person_id,
            'name'        => 'Stage Contact Updated',
            'emails'      => [
                ['value' => 'stage-contact@example.com', 'label' => 'work', 'primary' => 1],
            ],
        ],
    ], $lead->id);

    expect($updated->title)->toBe('Stage Test Opportunity - Updated');
    expect($updated->lead_pipeline_stage_id)->toBe($this->alternateStage->id);
    expect($updated->person->name)->toBe('Stage Contact Updated');
});

it('soft deletes an opportunity cleanly', function () {
    $lead = $this->leadRepository->create([
        'entity_type'            => 'leads',
        'title'                  => 'Deletable Opportunity',
        'user_id'                => $this->admin->id,
        'lead_value'             => 500,
        'status'                 => 1,
        'lead_source_id'         => $this->source->id,
        'lead_type_id'           => $this->type->id,
        'lead_pipeline_id'       => $this->pipeline->id,
        'lead_pipeline_stage_id' => $this->stage->id,
        'person'                 => [
            'entity_type' => 'persons',
            'name'        => 'Delete Contact',
            'emails'      => [
                ['value' => 'delete-contact@example.com', 'label' => 'work', 'primary' => 1],
            ],
        ],
    ]);

    $this->leadRepository->delete($lead->id);

    expect($this->leadRepository->find($lead->id))->toBeNull();
});
