<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->date('quals_due_date')->nullable()->after('expected_close_date');
            $table->date('proposal_due_date')->nullable()->after('quals_due_date');
            $table->datetime('presentation_datetime')->nullable()->after('proposal_due_date');
            $table->string('lead_department')->nullable()->after('presentation_datetime');
            $table->string('proposal_manager')->nullable()->after('lead_department');
            $table->string('staff_representative')->nullable()->after('proposal_manager');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn([
                'quals_due_date',
                'proposal_due_date', 
                'presentation_datetime',
                'lead_department',
                'proposal_manager',
                'staff_representative'
            ]);
        });
    }
};
