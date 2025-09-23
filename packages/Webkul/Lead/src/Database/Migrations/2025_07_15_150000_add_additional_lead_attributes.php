<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $now = Carbon::now();

        $attributes = [
            [
                'code'        => 'project_address_1',
                'name'        => 'Project Address Line 1',
                'type'        => 'text',
                'validation'  => null,
                'sort_order'  => 11,
                'is_required' => 0,
            ],
            [
                'code'        => 'project_city',
                'name'        => 'Project City',
                'type'        => 'text',
                'validation'  => null,
                'sort_order'  => 12,
                'is_required' => 0,
            ],
            [
                'code'        => 'project_state',
                'name'        => 'Project State',
                'type'        => 'text',
                'validation'  => null,
                'sort_order'  => 13,
                'is_required' => 0,
            ],
            [
                'code'        => 'project_postal_code',
                'name'        => 'Project Postal Code',
                'type'        => 'text',
                'validation'  => null,
                'sort_order'  => 14,
                'is_required' => 0,
            ],
            [
                'code'        => 'total_estimated_fee',
                'name'        => 'Total Estimated Fee',
                'type'        => 'price',
                'validation'  => 'decimal',
                'sort_order'  => 15,
                'is_required' => 0,
            ],
            [
                'code'        => 'firm_estimated_fee',
                'name'        => 'Firm Estimated Fee',
                'type'        => 'price',
                'validation'  => 'decimal',
                'sort_order'  => 16,
                'is_required' => 0,
            ],
            [
                'code'        => 'win_probability',
                'name'        => 'Win Probability',
                'type'        => 'text',
                'validation'  => 'numeric',
                'sort_order'  => 17,
                'is_required' => 0,
            ],
            [
                'code'        => 'quals_due_date',
                'name'        => 'Qualifications Due Date',
                'type'        => 'date',
                'validation'  => null,
                'sort_order'  => 18,
                'is_required' => 0,
            ],
            [
                'code'        => 'proposal_due_date',
                'name'        => 'Proposal Due Date',
                'type'        => 'date',
                'validation'  => null,
                'sort_order'  => 19,
                'is_required' => 0,
            ],
            [
                'code'        => 'estimated_selection_date',
                'name'        => 'Estimated Selection Date',
                'type'        => 'date',
                'validation'  => null,
                'sort_order'  => 20,
                'is_required' => 0,
            ],
            [
                'code'        => 'close_date',
                'name'        => 'Close Date',
                'type'        => 'date',
                'validation'  => null,
                'sort_order'  => 21,
                'is_required' => 0,
            ],
            [
                'code'        => 'estimated_start_date',
                'name'        => 'Estimated Start Date',
                'type'        => 'date',
                'validation'  => null,
                'sort_order'  => 22,
                'is_required' => 0,
            ],
            [
                'code'        => 'estimated_completion_date',
                'name'        => 'Estimated Completion Date',
                'type'        => 'date',
                'validation'  => null,
                'sort_order'  => 23,
                'is_required' => 0,
            ],
        ];

        foreach ($attributes as $attribute) {
            $payload = [
                'code'            => $attribute['code'],
                'name'            => $attribute['name'],
                'type'            => $attribute['type'],
                'entity_type'     => 'leads',
                'lookup_type'     => null,
                'validation'      => $attribute['validation'],
                'sort_order'      => (string) $attribute['sort_order'],
                'is_required'     => (string) $attribute['is_required'],
                'is_unique'       => '0',
                'quick_add'       => '1',
                'is_user_defined' => '0',
                'created_at'      => $now,
                'updated_at'      => $now,
            ];

            $existing = DB::table('attributes')
                ->where('entity_type', 'leads')
                ->where('code', $attribute['code'])
                ->first();

            if ($existing) {
                DB::table('attributes')
                    ->where('id', $existing->id)
                    ->update(array_merge($payload, [
                        'created_at' => $existing->created_at,
                        'updated_at' => $now,
                    ]));
            } else {
                DB::table('attributes')->insert($payload);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('attributes')
            ->where('entity_type', 'leads')
            ->whereIn('code', [
                'project_address_1',
                'project_city',
                'project_state',
                'project_postal_code',
                'total_estimated_fee',
                'firm_estimated_fee',
                'win_probability',
                'quals_due_date',
                'proposal_due_date',
                'estimated_selection_date',
                'close_date',
                'estimated_start_date',
                'estimated_completion_date',
            ])
            ->delete();
    }
};
