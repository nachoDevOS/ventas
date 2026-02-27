<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\RegistersUserEvents;

class Cashier extends Model
{
    use HasFactory, SoftDeletes, RegistersUserEvents;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'user_id',
        'title',
        'amountOpening',
        'amountExpectedClosing',
        'amountClosed',
        'amountMissing',
        'amountLeftover',
        'view',
        'observation',
        'status',
        'open_at',
        'closed_at',
        'closeUser_id',

        'registerUser_id',
        'registerRole',
        'deleted_at',
        'deleteUser_id',
        'deleteRole',
        'deleteObservation'
    ];

    public function movements(){
        return $this->hasMany(CashierMovement::class, 'cashier_id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id');//Para el cajero 
    }

    public function expenses(){
        return $this->hasMany(Expense::class, 'cashier_id')->withTrashed();
    }

    public function details(){
        return $this->hasMany(CashierDetail::class);
    }

    public function sales(){
        return $this->hasMany(Sale::class, 'cashier_id')->withTrashed();
    }

    // public function vaccinationRecords(){
    //     return $this->hasMany(VaccinationRecord::class, 'cashier_id')->withTrashed();
    // }

    // public function dewormings(){
    //     return $this->hasMany(Deworming::class, 'cashier_id')->withTrashed();
    // }

    // public function anamnesisForms(){
    //     return $this->hasMany(AnamnesisForm::class, 'cashier_id')->withTrashed();
    // }

    // public function hairSalons(){
    //     return $this->hasMany(HairSalon::class, 'cashier_id')->withTrashed();
    // }

    // public function homeServices(){
    //     return $this->hasMany(HomeService::class, 'cashier_id')->withTrashed();
    // }

    // public function euthanasias(){
    //     return $this->hasMany(Euthanasia::class, 'cashier_id')->withTrashed();
    // }


    // Egresooooo

    // public function advancePayments(){
    //     return $this->hasMany(AdvancePayment::class, 'cashier_id')->withTrashed();
    // }
    // public function paymentSheets(){
    //     return $this->hasMany(PaymentSheet::class, 'cashier_id')->withTrashed();
    // }





    // public function salesTransactions(){
    //     return $this->hasMany(SaleTransaction::class, 'cashier_id')->withTrashed();
    // }
    public function userclose(){
        return $this->belongsTo(User::class, 'closeUser_id');
    }
}
