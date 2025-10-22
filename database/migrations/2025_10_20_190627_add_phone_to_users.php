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
        Schema::table('users', function (Blueprint $table) {
            // Adds a 'mobile' column after the 'email' column
            // It's nullable() so existing users don't cause errors
            // It's unique() so no two users can have the same number
            $table->string('mobile')->nullable()->unique()->after('name');
        });
    }

    /**
     * Reverse the migrations.
     * This REMOVES the column.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('mobile');
        });
    }
};
