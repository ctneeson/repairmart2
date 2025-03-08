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
        Schema::create('emails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_id')->constrainedTo('users');
            $table->foreignId('listing_id')->constrainedTo('users')->nullable();
            $table->foreignId('quote_id')->constrainedTo('listings')->nullable();
            $table->foreignId('order_id')->constrainedTo('orders')->nullable();
            $table->string('subject', 255);
            $table->text('content');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emails');
    }
};
