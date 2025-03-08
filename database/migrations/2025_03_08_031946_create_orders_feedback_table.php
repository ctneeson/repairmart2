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
        Schema::create('orders_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrainedTo('orders');
            $table->foreignId('user_id')->constrainedTo('users');
            $table->foreignId('feedback_type_id')->constrainedTo('feedback_type');
            $table->text('comments');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders_feedback');
    }
};
