<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\RegistersUserEvents;

class CashierDetailCash extends Model
{
    use HasFactory, SoftDeletes, RegistersUserEvents;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'cashierDetail_id',
        'cash_value',
        'quantity',

        'status',

        'registerUser_id',
        'registerRole',
        'deleted_at',
        'deleteUser_id',
        'deleteRole',
        'deleteObservation',
    ];
}
