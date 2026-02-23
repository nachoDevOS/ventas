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
        Schema::create('item_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->nullable()->constrained('items');
            $table->foreignId('incomeDetail_id')->nullable()->constrained('income_details');  

            $table->string('lote')->nullable();
            $table->date('expirationDate')->nullable();

            $table->decimal('quantity', 10, 2)->nullable();
            $table->decimal('stock', 10, 2)->nullable();

            $table->decimal('pricePurchase', 10, 2)->nullable();
            $table->decimal('priceSale', 10, 2)->nullable();

            $table->string('dispensed')->nullable(); // Entero, Fraccionado
            $table->decimal('dispensedQuantity', 10, 2)->nullable();
            
            $table->decimal('dispensedPrice', 10, 2)->nullable();


            $table->string('type')->nullable(); //Ingreso, Egreso            
            $table->text('observation')->nullable();

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
        Schema::dropIfExists('item_stocks');
    }
};
