<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\RegistersUserEvents;

class ItemStock extends Model
{
    use HasFactory, RegistersUserEvents, SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'item_id',
        'incomeDetail_id',
        'lote',
        'expirationDate',
        'quantity',
        'stock',
        'pricePurchase',
        'priceSale',

        'dispensed',
        'dispensedQuantity',
        'dispensedPrice',
        'status',

        'type',
        'observation',

        'registerUser_id',
        'registerRole',
        'deleted_at',
        'deleteUser_id',
        'deleteRole',
        'deleteObservation',
    ];

    public function itemStockFractions()
    {
        return $this->hasMany(ItemStockFraction::class, 'itemStock_id');
    }

    public function egresDetails()
    {
        return $this->hasMany(EgresDetail::class, 'itemStock_id');
    }


    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id')->withTrashed();
    }

    public function register()
    {
        return $this->belongsTo(User::class, 'registerUser_id');
    }

    public function incomeDetail()
    {
        return $this->belongsTo(IncomeDetail::class, 'incomeDetail_id');
    }
}
