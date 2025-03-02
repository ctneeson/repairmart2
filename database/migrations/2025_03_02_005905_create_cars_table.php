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
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maker_id')->constrainedTo('makers');
            $table->foreignId('model_id')->constrainedTo('models');
            $table->integer('year');
            $table->integer('price');
            $table->integer('mileage');
            $table->string('vin', 255);
            $table->integer('car_type_id')->constrainedTo('car_types');
            $table->integer('fuel_type_id')->constrainedTo('fuel_types');
            $table->integer('user_id')->constrainedTo('users');
            $table->integer('city_id')->constrainedTo('cities');
            $table->string('address', 255);
            $table->string('phone', 45);
            $table->longText('description')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
