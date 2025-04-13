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
            $table->foreignId('sender_id')->constrainedTo('users')->cascadeOnDelete();
            $table->foreignId('listing_id')->constrainedTo('listings')->nullable()->cascadeOnDelete();
            $table->foreignId('quote_id')->constrainedTo('quotes')->nullable()->cascadeOnDelete();
            $table->foreignId('order_id')->constrainedTo('orders')->nullable()->cascadeOnDelete();
            $table->string('subject', 255);
            $table->text('content');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('read_at')->nullable();
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
