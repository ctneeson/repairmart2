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
        Schema::create('emails_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('email_id')->constrainedTo('email');
            $table->foreignId('recipient_id')->constrainedTo('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emails_recipients');
    }
};
