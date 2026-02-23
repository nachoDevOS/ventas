<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\RegistersUserEvents;

class SaleDetail extends Model
{
    use HasFactory, RegistersUserEvents, SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'sale_id',
        'itemStock_id',
        'itemStockFraction_id',
        'dispensed', // Entero, Fraccionado
    
        'pricePurchase',
        'presentation_id',
        'price',
        'quantity',
        'amount',

        'registerUser_id',
        'registerRole',
        'deleted_at',
        'deleteUser_id',
        'deleteRole',
        'deleteObservation',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class,'sale_id');
    }
    public function itemStock()
    {
        return $this->belongsTo(ItemStock::class,'itemStock_id');
    }
    public function itemStockFraction()
    {
        return $this->belongsTo(ItemStockFraction::class,'itemStockFraction_id');
    }
}
