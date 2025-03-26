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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('display_name');
            $table->string('description')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('deleted_at')->nullable();
        });

        DB::table('roles')->insert([
            ['name' => 'admin', 'display_name' => 'Administrator', 'description' => 'Full system access'],
            ['name' => 'customer', 'display_name' => 'Customer', 'description' => 'Service requestor'],
            ['name' => 'specialist', 'display_name' => 'Repair Specialist', 'description' => 'Service provider']
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
