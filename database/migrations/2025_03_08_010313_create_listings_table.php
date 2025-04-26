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
        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrainedTo('users')->restrictOnDelete();
            $table->foreignId('status_id')->constrainedTo('listing_status')->restrictOnDelete();
            $table->foreignId('manufacturer_id')->constrainedTo('manufacturers')->restrictOnDelete();
            $table->string('title', 255);
            $table->text('description');
            $table->foreignId('currency_id')->constrainedTo('currency')->nullable()->restrictOnDelete();
            $table->decimal('budget', 10, 2)->nullable();
            $table->boolean('use_default_location')->default(false);
            $table->string('address_line1', 255)->nullable();
            $table->string('address_line2', 255)->nullable();
            $table->string('city', 255)->nullable();
            $table->string('postcode', 50)->nullable();
            $table->foreignId('country_id')->constrainedTo('countries')->nullable()->restrictOnDelete();
            $table->string('phone', 45)->unique()->nullable();
            $table->integer('expiry_days')->default(30);
            $table->timestamp('published_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamp('expired_at')->nullable();
        });

        Schema::table('listings', function (Blueprint $table) {
            // Create a composite index for status and published date
            $table->index(['status_id', 'published_at'], 'listings_status_published_index');

            // Index for expiry calculation
            $table->index(['published_at', 'expiry_days'], 'listings_expiry_index');

            // Other common search filters
            $table->index('manufacturer_id');
            $table->index('country_id');
            $table->index('use_default_location');
            $table->index('title');
            $table->index('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropIndex('listings_status_published_index');
            $table->dropIndex('listings_expiry_index');
            $table->dropIndex('manufacturer_id');
            $table->dropIndex('country_id');
            $table->dropIndex('use_default_location');
            $table->dropIndex('title');
            $table->dropIndex('description');
        });

        Schema::dropIfExists('listings');
    }
};
