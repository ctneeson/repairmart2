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
        Schema::create('order_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('amount_editable')->default(false);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('deleted_at')->nullable();
        });

        DB::table('order_status_transitions')->insert([
            ['name' => 'Created', 'amount_editable' => false],
            ['name' => 'Dispatched to Specialist', 'amount_editable' => false],
            ['name' => 'Specialist Assessing', 'amount_editable' => false],
            ['name' => 'Price Adjustment Requested', 'amount_editable' => false],
            ['name' => 'Price Adjustment Approved', 'amount_editable' => true],
            ['name' => 'Price Adjustment Rejected', 'amount_editable' => false],
            ['name' => 'Specialist Repairing', 'amount_editable' => false],
            ['name' => 'Dispatched to Customer', 'amount_editable' => false],
            ['name' => 'Received by Customer', 'amount_editable' => false],
            ['name' => 'Payment requested', 'amount_editable' => false],
            ['name' => 'Payment received', 'amount_editable' => false],
            ['name' => 'Closed', 'amount_editable' => false],
            ['name' => 'Cancelled', 'amount_editable' => false],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_statuses');
    }
};
