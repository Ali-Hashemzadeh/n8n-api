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
        // This is the pivot table for the many-to-many relationship
        Schema::create('call_report_service_type', function (Blueprint $table) {
            $table->id();
            $table->foreignId('call_report_id')->constrained('call_reports')->onDelete('cascade');
            $table->foreignId('service_type_id')->constrained('service_types')->onDelete('cascade');

            // A service type can only be linked to a call report once
            $table->unique(['call_report_id', 'service_type_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('call_report_service_type');
    }
};
