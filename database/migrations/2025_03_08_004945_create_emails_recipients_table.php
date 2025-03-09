<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Email;
use App\Models\User;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('emails_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Email::class)->constrained('emails')->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'recipient_id')->constrained('users')->cascadeOnDelete();
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
