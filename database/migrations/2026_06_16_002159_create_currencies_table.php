<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->char('code', 3)->unique();
            $table->string('symbol', 8)->nullable();
            $table->decimal('exchange_rate', 15, 6);
            $table->boolean('is_base')->default(false);
            $table->timestamps();
        });

        DB::table('currencies')->insert([
            ['name' => 'Mexican Peso', 'code' => 'MXN', 'symbol' => '$', 'exchange_rate' => 1, 'is_base' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'US Dollar', 'code' => 'USD', 'symbol' => 'US$', 'exchange_rate' => 17, 'is_base' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Canadian Dollar', 'code' => 'CAD', 'symbol' => 'C$', 'exchange_rate' => 12.5, 'is_base' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Euro', 'code' => 'EUR', 'symbol' => 'EUR', 'exchange_rate' => 18.5, 'is_base' => false, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
