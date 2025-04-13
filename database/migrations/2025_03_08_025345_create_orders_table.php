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
            $table->foreignId('quote_id')->constrainedTo('quotes')->restrictOnDelete();
            $table->foreignId('customer_id')->nullable()->after('quote_id')
                ->constrained('users')->restrictOnDelete();
            $table->foreignId('status_id')->constrainedTo('order_status')->restrictOnDelete();
            $table->boolean('override_quote')->default(false);
            $table->decimal('amount', 10, 2)->nullable();
            $table->foreignId('customer_feedback_id')->nullable()
                ->constrainedTo('feedback_types')->restrictOnDelete();
            $table->string('customer_feedback', 255)->nullable();
            $table->foreignId('specialist_feedback_id')->nullable()
                ->constrainedTo('feedback_types')->restrictOnDelete();
            $table->string('specialist_feedback', 255)->nullable();
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
        Schema::dropIfExists('orders');
    }
};
