
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Attribute code to leads column map.
     */
    protected array $columnMap = [
        'client_company'     => 'client_company',
        'owner_company'      => 'owner_company',
        'opportunity_number' => 'opportunity_number',
    ];

    /**
     * Attribute type to attribute_values column map.
     */
    protected array $valueColumnMap = [
        'text'        => 'text_value',
        'textarea'    => 'text_value',
        'price'       => 'float_value',
        'boolean'     => 'boolean_value',
        'select'      => 'integer_value',
        'multiselect' => 'text_value',
        'checkbox'    => 'text_value',
        'email'       => 'json_value',
        'address'     => 'json_value',
        'phone'       => 'json_value',
        'lookup'      => 'integer_value',
        'datetime'    => 'datetime_value',
        'date'        => 'date_value',
        'file'        => 'text_value',
        'image'       => 'text_value',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $attributes = DB::table('attributes')
            ->where('entity_type', 'leads')
            ->whereIn('code', array_keys($this->columnMap))
            ->get();

        foreach ($attributes as $attribute) {
            $valueColumn = $this->valueColumnMap[$attribute->type] ?? 'text_value';
            $column = $this->columnMap[$attribute->code] ?? null;

            if (! $column) {
                continue;
            }

            $values = DB::table('attribute_values')
                ->select('entity_id', $valueColumn)
                ->where('entity_type', 'leads')
                ->where('attribute_id', $attribute->id)
                ->whereNotNull($valueColumn)
                ->get();

            foreach ($values as $value) {
                $stored = $value->{$valueColumn};

                if ($stored === null || $stored === '') {
                    continue;
                }

                $query = DB::table('leads')
                    ->where('id', $value->entity_id)
                    ->where(function ($inner) use ($column) {
                        $inner->whereNull($column)
                            ->orWhere($column, '');
                    });

                $query->update([
                    $column => is_string($stored) ? $stored : json_encode($stored),
                ]);
            }

            DB::table('attribute_values')
                ->where('entity_type', 'leads')
                ->where('attribute_id', $attribute->id)
                ->delete();
        }

        if ($attributes->isNotEmpty()) {
            DB::table('attributes')
                ->whereIn('id', $attributes->pluck('id'))
                ->delete();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $now = Carbon::now();

        foreach ($this->columnMap as $code => $column) {
            $existingAttribute = DB::table('attributes')
                ->where('entity_type', 'leads')
                ->where('code', $code)
                ->first();

            if ($existingAttribute) {
                $attributeId = $existingAttribute->id;
            } else {
                $attributeId = DB::table('attributes')->insertGetId([
                    'code'            => $code,
                    'name'            => ucfirst(str_replace('_', ' ', $code)),
                    'type'            => 'text',
                    'entity_type'     => 'leads',
                    'lookup_type'     => null,
                    'validation'      => null,
                    'sort_order'      => '0',
                    'is_required'     => '0',
                    'is_unique'       => '0',
                    'quick_add'       => '0',
                    'is_user_defined' => '0',
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ]);
            }

            $leadRows = DB::table('leads')
                ->select('id', $column)
                ->whereNotNull($column)
                ->where($column, '!=', '')
                ->get();

            foreach ($leadRows as $row) {
                $exists = DB::table('attribute_values')
                    ->where('entity_type', 'leads')
                    ->where('entity_id', $row->id)
                    ->where('attribute_id', $attributeId)
                    ->exists();

                if ($exists) {
                    continue;
                }

                DB::table('attribute_values')->insert([
                    'attribute_id' => $attributeId,
                    'entity_type'  => 'leads',
                    'entity_id'    => $row->id,
                    'text_value'   => $row->{$column},
                ]);
            }
        }
    }
};
