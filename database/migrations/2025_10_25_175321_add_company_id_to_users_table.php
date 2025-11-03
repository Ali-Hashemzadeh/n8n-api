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
        // We use Schema::table() because we are modifying an existing table
        Schema::table('users', function (Blueprint $table) {

            // 1. Create the company_id column
            // - foreignId: creates an unsignedBigInteger
            // - nullable: allows existing users to not have a company
            // - after('mobile'): (Optional) places it nicely in your table
            $table->foreignId('company_id')
                ->nullable()
                ->after('mobile');

            // 2. Add the foreign key constraint
            // - references('id')->on('companies'): links to the 'id' on 'companies' table
            // - nullOnDelete(): if a company is deleted, set the user's company_id to NULL
            $table->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // When rolling back, we must drop the foreign key first
            $table->dropForeign(['company_id']);

            // Then we can drop the column
            $table->dropColumn('company_id');
        });
    }
};
