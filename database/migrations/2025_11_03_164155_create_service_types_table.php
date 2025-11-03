<?php
// In database/migrations/YYYY_MM_DD_HHMMSS_create_service_types_table.php

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
        Schema::create('service_types', function (Blueprint $table) {
            $table->id();

            // We use a foreignId for the company relationship
            $table->foreignId('company_id')
                ->constrained('companies')  // Ensures it exists in the 'companies' table
                ->onDelete('cascade');     // Deletes service types if the company is deleted

            $table->string('name');
            $table->timestamps();

            // Add a unique constraint to ensure a name is unique
            // only *within* a specific company.
            $table->unique(['company_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_types');
    }
};
