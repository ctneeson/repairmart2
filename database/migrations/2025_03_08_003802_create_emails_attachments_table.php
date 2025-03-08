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
        Schema::create('emails_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('email_id')->constrainedTo('emails');
            $table->foreignId('attachment_id')->constrainedTo('attachments');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emails_attachments');
    }
};
