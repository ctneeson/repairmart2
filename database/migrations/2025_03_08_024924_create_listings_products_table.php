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
        Schema::create('listings_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrainedTo('listings');
            $table->foreignId('product_id')->constrainedTo('products');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listings_products');
    }
};
