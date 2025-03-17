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
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrainedTo('users');
            $table->foreignId('listing_id')->constrainedTo('listings');
            $table->foreignId('status_id')->constrainedTo('quote_statuses')->default(1);
            $table->foreignId('currency_id')->constrainedTo('currencies');
            $table->foreignId('deliverymethod_id')->constrainedTo('deliverymethods');
            $table->decimal('amount', 10, 2);
            $table->integer('turnaround');
            $table->boolean('use_default_location')->defaultValue(true);
            $table->string('override_address_line1', 255)->nullable();
            $table->string('override_address_line2', 255)->nullable();
            $table->string('override_city', 255)->nullable();
            $table->string('override_postcode', 50)->nullable();
            $table->foreignId('override_country_id')->constrainedTo('countries')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};
