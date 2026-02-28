<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('item_stock_egresos', function (Blueprint $table) {
            // FK al registro de fracción sobrante creado al egresar (para poder revertirlo)
            $table->foreignId('item_stock_fraction_id')
                  ->nullable()
                  ->after('quantity_fractions')
                  ->constrained('item_stock_fractions');
        });
    }

    public function down(): void
    {
        Schema::table('item_stock_egresos', function (Blueprint $table) {
            $table->dropForeign(['item_stock_fraction_id']);
            $table->dropColumn('item_stock_fraction_id');
        });
    }
};
