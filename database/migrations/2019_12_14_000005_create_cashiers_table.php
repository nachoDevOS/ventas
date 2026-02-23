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
        Schema::create('cashiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users');//Para identificar a que usuario le pertenece la caja creada
            $table->string('title')->nullable();

            $table->decimal('amountOpening', 10, 2)->nullable();
            $table->decimal('amountExpectedClosing', 10, 2)->nullable(); //Cantidad real con la que se debe cerrar caja
            $table->decimal('amountClosed', 10, 2)->nullable(); //cantida que se cierra la caja 
            $table->decimal('amountMissing', 10, 2)->nullable(); //Cantidad Faltante para cerrar caja
            $table->decimal('amountLeftover', 10, 2)->nullable(); //Cantidad sobrante para cerrar caja



            $table->text('observation')->nullable();
            $table->dateTime('view')->nullable(); 
            $table->string('status')->nullable();
            
            $table->dateTime('open_at')->nullable(); 
            $table->datetime('closed_at')->nullable();
            $table->foreignId('closeUser_id')->nullable()->constrained('users');

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
        Schema::dropIfExists('cashiers');
    }
};
