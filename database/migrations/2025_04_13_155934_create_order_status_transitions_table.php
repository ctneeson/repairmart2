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
        Schema::create('order_status_transitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->foreignId('from_status_id')->constrained('order_statuses')->cascadeOnDelete();
            $table->foreignId('to_status_id')->constrained('order_statuses')->cascadeOnDelete();
        });

        DB::table('order_status_transitions')->insert([
            ['role_id' => 2, 'from_status_id' => 1, 'to_status_id' => 2],
            ['role_id' => 3, 'from_status_id' => 1, 'to_status_id' => 3],
            ['role_id' => 3, 'from_status_id' => 2, 'to_status_id' => 3],
            ['role_id' => 3, 'from_status_id' => 2, 'to_status_id' => 4],
            ['role_id' => 3, 'from_status_id' => 2, 'to_status_id' => 7],
            ['role_id' => 3, 'from_status_id' => 3, 'to_status_id' => 4],
            ['role_id' => 3, 'from_status_id' => 3, 'to_status_id' => 7],
            ['role_id' => 2, 'from_status_id' => 4, 'to_status_id' => 5],
            ['role_id' => 2, 'from_status_id' => 4, 'to_status_id' => 6],
            ['role_id' => 3, 'from_status_id' => 5, 'to_status_id' => 7],
            ['role_id' => 3, 'from_status_id' => 5, 'to_status_id' => 8],
            ['role_id' => 3, 'from_status_id' => 5, 'to_status_id' => 9],
            ['role_id' => 2, 'from_status_id' => 6, 'to_status_id' => 13],
            ['role_id' => 3, 'from_status_id' => 6, 'to_status_id' => 8],
            ['role_id' => 3, 'from_status_id' => 7, 'to_status_id' => 8],
            ['role_id' => 2, 'from_status_id' => 7, 'to_status_id' => 9],
            ['role_id' => 2, 'from_status_id' => 8, 'to_status_id' => 9],
            ['role_id' => 3, 'from_status_id' => 9, 'to_status_id' => 10],
            ['role_id' => 3, 'from_status_id' => 10, 'to_status_id' => 11],
            ['role_id' => 2, 'from_status_id' => 11, 'to_status_id' => 12],
            ['role_id' => 3, 'from_status_id' => 11, 'to_status_id' => 12],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_status_transitions');
    }
};
