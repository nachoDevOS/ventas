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
        Schema::create('income_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('income_id')->nullable()->constrained('incomes');
            $table->foreignId('item_id')->nullable()->constrained('items');

            $table->string('lote')->nullable();
            $table->date('expirationDate')->nullable();
            $table->decimal('quantity', 10, 2)->nullable(); //monto total
            $table->decimal('stock', 10, 2)->nullable(); //monto disponible

            $table->decimal('pricePurchase', 10, 2)->nullable(); //Precio de Compra
            $table->decimal('priceSale', 10, 2)->nullable(); //Precio de Venta

            $table->decimal('amountPurchase', 10, 2)->nullable(); //monto total de compra       
            $table->decimal('amountSale', 10, 2)->nullable(); //monto total de venta al por menor  

            // Parte fraccionaria
            $table->string('dispensed')->nullable(); // Entero, Fraccionado
            $table->decimal('dispensedQuantity', 10, 2)->nullable();
            
            $table->decimal('dispensedPrice', 10, 2)->nullable();
            // :::::::

            $table->smallInteger('status')->default(1);

            $table->timestamps();            
            $table->foreignId('registerUser_id')->nullable()->constrained('users');
            $table->string('registerRole')->nullable();

            $table->softDeletes();
            $table->foreignId('deleteUser_id')->nullable()->constrained('users');
            $table->string('deleteRole')->nullable();
            $table->text('deleteObservation')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('income_details');
    }
};
