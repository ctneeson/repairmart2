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
        Schema::create('quotes_deliverymethods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id')->constrainedTo('quotes');
            $table->foreignId('deliverymethod_ir')->constrainedTo('deliverymethods');
            $table->decimal('delivery_amount', 10, 2);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotes_deliverymethods');
    }
};
