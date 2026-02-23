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
        Schema::create('incomes', function (Blueprint $table) {
            $table->id();
            // $table->foreignId('supplier_id')->nullable()->constrained('suppliers');
            $table->decimal('amount', 10, 2)->nullable(); //monto total
            $table->string('typeIncome')->nullable(); //Venta credito, venta al contado
            $table->date('date')->nullable();
            $table->text('file')->nullable();            
            $table->text('observation')->nullable(); 

            $table->string('status')->default('Pendiente');

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
        Schema::dropIfExists('incomes');
    }
};
