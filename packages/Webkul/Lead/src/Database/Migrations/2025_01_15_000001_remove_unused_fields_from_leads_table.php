<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            // Drop foreign key constraints first
            $table->dropForeign(['user_id']);
            $table->dropForeign(['lead_source_id']);
            $table->dropForeign(['lead_type_id']);
        });
        
        Schema::table('leads', function (Blueprint $table) {
            // Remove unused fields
            $table->dropColumn([
                'expected_close_date',
                'user_id',
                'lead_source_id', 
                'lead_type_id'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            // Add back the fields if needed
            $table->date('expected_close_date')->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('lead_source_id')->nullable();
            $table->unsignedInteger('lead_type_id')->nullable();
            
            // Add foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('lead_source_id')->references('id')->on('lead_sources')->onDelete('set null');
            $table->foreign('lead_type_id')->references('id')->on('lead_types')->onDelete('set null');
        });
    }
};
