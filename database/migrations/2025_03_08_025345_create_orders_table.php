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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrainedTo('listings');
            $table->foreignId('quote_deliverymethod_id')->constrainedTo('quotes_deliverymethods');
            $table->foreignId('status_id')->constrainedTo('order_status')->cascadeOnDelete();
            $table->boolean('override_quote')->default(false);
            $table->decimal('override_amount', 10, 2)->nullable();
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
