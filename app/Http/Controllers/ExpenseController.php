<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cashier;
use App\Models\Expense;
use App\Models\ExpenseTransaction;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;


class ExpenseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request)
    {
        $cashier = $this->cashierMoney(null, 'user_id = "'.Auth::user()->id.'"', 'status = "Abierta"')->original;

        $amount_cash = $request->amount_cash??0;
        $amount_qr = $request->amount_qr??0;

        $amountTotal = $amount_cash + $amount_qr;
        if($amountTotal <= 0)
        {
            return redirect()->back()->with(['message' => 'Ingrese un monto valido para realizar la transaccion.', 'alert-type' => 'warning']);
        }

        if (!$cashier) {
            return redirect()
                ->back()
                ->with(['message' => 'Usted no cuenta con caja abierta.', 'alert-type' => 'warning']);
        }

        if($cashier['amountEfectivoCashier'] < $amount_cash)
        {
            return redirect()->back()->with(['message' => 'No cuenta con monto en efectivo disponible para realizar la transaccion.', 'alert-type' => 'warning']);
        }

        if($cashier['amountQrCashier'] < $amount_qr)
        {
            return redirect()->back()->with(['message' => 'No cuenta con monto en Qr disponible para realizar la transaccion.', 'alert-type' => 'warning']);
        }

        if($request->details == null)
        {
            return redirect()->back()->with(['message' => 'Lista vacia de gastos.', 'alert-type' => 'warning']);
        }

        $total_amount = 0;
        for ($i=0; $i < count($request->details); $i++) { 
            $total_amount += $request->quantities[$i] * $request->prices[$i];
        }

        if($total_amount != $amountTotal)
        {
            return redirect()
                ->back()
                ->with(['message' => 'La suma de los montos de pago no coincide con el total de los gastos.', 'alert-type' => 'warning']);
        }

        DB::beginTransaction();
        try {
            for ($i=0; $i < count($request->details); $i++) { 
                $transaction = Transaction::create([
                    'status' => 'Completado',
                ]);
                $expense = Expense::create([
                    'observation' => $request->details[$i],
                    'quantity' => $request->quantities[$i],
                    'price' => $request->prices[$i],
                    'amount' => $request->quantities[$i] * $request->prices[$i],
                    'cashier_id' => $cashier['cashier']->id,
                ]);

                if ($request->payment_type == 'Efectivo' || $request->payment_type == 'Efectivo y Qr') {
                    ExpenseTransaction::create([
                        'expense_id' => $expense->id,
                        'transaction_id' => $transaction->id,
                        'amount' => $amount_cash,
                        'paymentType' => 'Efectivo',
                    ]);
                }
                if ($request->payment_type == 'Qr' || $request->payment_type == 'Efectivo y Qr') {
                    ExpenseTransaction::create([
                        'expense_id' => $expense->id,
                        'transaction_id' => $transaction->id,
                        'amount' => $amount_qr,
                        'paymentType' => 'Qr',
                    ]);
                }
            }
            DB::commit();
            return redirect()
                ->back()
                ->with(['message' => 'Registrado exitosamente.', 'alert-type' => 'success']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with(['message' => 'Ocurrió un error.', 'alert-type' => 'error']);
        }
    }

    public function destroy(Request $request, Expense $expense)
    {
        $cashier = Cashier::findOrFail($expense->cashier_id);

        if ($cashier->status != 'Abierta') {
            return redirect()
                ->back()
                ->with(['message' => 'No se puede eliminar un gasto de una caja que no está abierta.', 'alert-type' => 'warning']);
        }

        DB::beginTransaction();
        try {
            $expense->delete();
            DB::commit();
            return redirect()
                ->back()
                ->with(['message' => 'Gasto eliminado exitosamente.', 'alert-type' => 'success']);
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->logError($th, $request);
            return redirect()
                ->back()
                ->with(['message' => 'Ocurrió un error.', 'alert-type' => 'error']);
        }
    }
}
