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




    // public function salesTransactions(){
    //     return $this->hasMany(SaleTransaction::class, 'cashier_id')->withTrashed();
    // }
    public function userclose(){
        return $this->belongsTo(User::class, 'closeUser_id');
    }
}
