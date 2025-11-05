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
        Schema::create('call_reports', function (Blueprint $table) {
            $table->id();

            // Each call BELONGS TO a company
            $table->foreignId('company_id')
                ->constrained('companies')
                ->onDelete('cascade'); // If company is deleted, delete its reports

            // Customer info from 'profile'
            $table->string('customer_phone')->index();
            $table->string('customer_name');
            $table->string('customer_lastname')->nullable();
            $table->string('customer_email')->nullable()->index();

            // Call data from n8n
            $table->text('summary');                    // From n8n 'text'
            $table->json('conversation');               // From n8n 'json'
            $table->json('metadata')->nullable();       // From n8n 'meta'
            $table->string('state')->index();           // 'confirmed', 'failed', etc.

            // We will use Laravel's timestamps. We can set 'created_at'
            // manually to match the n8n timestamp when the data comes in.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('call_reports');
    }
};
