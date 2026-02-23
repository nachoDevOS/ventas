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
        Schema::create('item_stock_fractions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('itemStock_id')->nullable()->constrained('item_stocks');

            $table->decimal('quantity', 10, 2)->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('amount', 10, 2)->nullable();

            // $table->string('status')->default('Pendiente'); // Pendiente / Inventariado es cuando ya fue descontado
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_stock_fractions');
    }
};
