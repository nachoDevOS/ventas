<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_stock_egresos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('egress_id')->nullable()->constrained('egresos');          // cabecera de egreso
            $table->foreignId('itemStock_id')->constrained('item_stocks');               // lote de origen
            $table->foreignId('itemStockFraction_id')->nullable()->constrained('item_stock_fractions');

            $table->decimal('pricePurchase', 10, 2)->nullable();
            $table->foreignId('presentation_id')->nullable()->constrained('presentations');
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('quantity', 10, 2)->nullable();
            $table->decimal('amount', 10, 2)->nullable();

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

    public function down(): void
    {
        Schema::dropIfExists('item_stock_egresos');
    }
};
