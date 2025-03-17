<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Listing;
use App\Models\Order;
use App\Models\Email;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Listing::class)->nullable()->constrained();
            $table->foreignIdFor(Order::class)->nullable()->constrained();
            $table->foreignIdFor(Email::class)->nullable()->constrained();
            $table->integer('position');
            $table->string('path', 255);
            $table->timestamp('created_at')->useCurrent(); // Set default value of current timestamp
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value of current timestamp and update on change
            $table->timestamp('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};