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
        Schema::create('sale_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cashier_id')->nullable()->constrained('cashiers');
            $table->foreignId('transaction_id')->nullable()->constrained('transactions');
            $table->foreignId('sale_id')->nullable()->constrained('sales');  
            // $table->foreignId('branch_id')->nullable()->constrained('branches');

            $table->string('paymentType'); // Ej: Qr, Efectivo

            $table->decimal('amount', 10, 2)->nullable();
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
        Schema::dropIfExists('sale_transactions');
    }
};
