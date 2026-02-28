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

            $table->foreignId('item_stock_id')->constrained('item_stocks');  // lote de origen
            $table->foreignId('item_id')->constrained('items');               // item (para filtros)

            $table->decimal('quantity', 10, 2);                    // unidades enteras egresadas
            $table->decimal('quantity_fractions', 10, 2)->default(0); // fracciones egresadas (si aplica)

            $table->text('reason');                                // motivo del egreso

            $table->foreignId('register_user_id')->nullable()->constrained('users');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_stock_egresos');
    }
};
