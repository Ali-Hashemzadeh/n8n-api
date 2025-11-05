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

            // Link to the company that received the call
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');

            // Link to the global customer who made the call
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');

            // --- Removed Fields ---
            // We no longer store customer_phone, customer_name, etc. here.
            // We get that from the new 'customers' table.

            // --- Call Details ---
            $table->text('summary'); // For n8n 'text'
            $table->json('conversation'); // For n8n 'json'
            $table->json('metadata')->nullable(); // For n8n 'meta'
            $table->string('state'); // 'confirmed', 'failed', 'unfinished'

            $table->timestamps(); // We'll use created_at for the n8n 'timestamp'
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
