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
            $table->foreignId('user_id')->constrainedTo('users')->cascadeOnDelete();
            $table->foreignId('listing_id')->constrainedTo('listings')->cascadeOnDelete();
            $table->foreignId('status_id')->constrainedTo('quote_statuses')->default(1)->restrictOnDelete();
            $table->foreignId('currency_id')->constrainedTo('currencies')->restrictOnDelete();
            $table->foreignId('deliverymethod_id')->constrainedTo('deliverymethods')->restrictOnDelete();
            $table->decimal('amount', 10, 2);
            $table->integer('turnaround');
            $table->text('description')->nullable();
            $table->boolean('use_default_location')->defaultValue(true);
            $table->string('address_line1', 255);
            $table->string('address_line2', 255)->nullable();
            $table->string('city', 255);
            $table->string('postcode', 50);
            $table->foreignId('country_id')->constrainedTo('countries')->restrictOnDelete();
            $table->string('phone', 45)->unique()->nullable();
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
