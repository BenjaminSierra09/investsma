<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ampi_properties', function (Blueprint $table) {
            $table->id();
            $table->string('mls_id')->unique();
            $table->unsignedBigInteger('external_id')->nullable()->index();
            $table->string('name')->nullable();
            $table->string('slug')->nullable()->index();
            $table->string('status', 80)->nullable()->index();
            $table->string('category', 120)->nullable()->index();
            $table->unsignedBigInteger('office_id')->nullable()->index();
            $table->string('city')->nullable()->index();
            $table->string('neighborhood')->nullable()->index();
            $table->decimal('price', 18, 2)->nullable();
            $table->char('currency', 3)->nullable()->index();
            $table->decimal('normalized_price', 18, 2)->nullable()->index();
            $table->unsignedSmallInteger('bedrooms')->nullable()->index();
            $table->decimal('bathrooms', 5, 1)->nullable();
            $table->unsignedSmallInteger('floors')->nullable();
            $table->decimal('construction_meters', 12, 2)->nullable();
            $table->decimal('lot_meters', 12, 2)->nullable();
            $table->string('furnished', 40)->nullable();
            $table->string('parking_type', 80)->nullable();
            $table->boolean('with_yard')->nullable();
            $table->boolean('pool')->nullable();
            $table->boolean('casita')->nullable();
            $table->boolean('gated_comm')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->text('featured_image')->nullable();
            $table->json('photos')->nullable();
            $table->json('raw_payload');
            $table->dateTime('api_created_at')->nullable();
            $table->dateTime('api_updated_at')->nullable();
            $table->dateTime('last_synced_at')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();

            $table->index(['is_active', 'normalized_price']);
            $table->index(['office_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ampi_properties');
    }
};
