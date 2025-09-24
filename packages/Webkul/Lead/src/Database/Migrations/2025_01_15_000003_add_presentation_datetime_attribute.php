<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $now = Carbon::now();
        
        DB::table('attributes')->insert([
            'code'            => 'presentation_datetime',
            'name'            => 'Presentation Date & Time',
            'type'            => 'datetime',
            'entity_type'     => 'leads',
            'lookup_type'     => null,
            'validation'      => null,
            'sort_order'      => '24',
            'is_required'     => '0',
            'is_unique'       => '0',
            'quick_add'       => '1',
            'is_user_defined' => '0',
            'created_at'      => $now,
            'updated_at'      => $now,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('attributes')->where('code', 'presentation_datetime')->where('entity_type', 'leads')->delete();
    }
};
