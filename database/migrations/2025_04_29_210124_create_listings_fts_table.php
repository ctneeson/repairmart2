<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Only create FTS table if using SQLite
        if (config('database.default') === 'sqlite') {
            // Create the FTS5 virtual table
            DB::statement('CREATE VIRTUAL TABLE IF NOT EXISTS listings_fts USING fts5(title, description)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (config('database.default') === 'sqlite') {
            DB::statement('DROP TABLE IF EXISTS listings_fts');
        }
    }
};
