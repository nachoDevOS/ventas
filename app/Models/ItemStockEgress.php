<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\RegistersUserEvents;

class ItemStockEgress extends Model
{
    use SoftDeletes, RegistersUserEvents;

    protected $table = 'item_stock_egresos';

    protected $fillable = [
        'egress_id',
        'itemStock_id',
        'itemStockFraction_id',

        'pricePurchase',
        'presentation_id',
        'price',
        'quantity',
        'amount',
        'status',

        'registerUser_id',
        'registerRole',
        'deleted_at',
        'deleteUser_id',
        'deleteRole',
        'deleteObservation',
    ];

    public function egress()
    {
        return $this->belongsTo(Egress::class, 'egress_id');
    }

    public function itemStock()
    {
        return $this->belongsTo(ItemStock::class, 'itemStock_id');
    }

    public function itemStockFraction()
    {
        return $this->belongsTo(ItemStockFraction::class, 'itemStockFraction_id');
    }

    public function presentation()
    {
        return $this->belongsTo(Presentation::class, 'presentation_id');
    }

    public function registerUser()
    {
        return $this->belongsTo(User::class, 'registerUser_id');
    }
}
