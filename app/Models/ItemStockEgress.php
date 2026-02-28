<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemStockEgress extends Model
{
    use SoftDeletes;

    protected $table = 'item_stock_egresos';

    protected $fillable = [
        'item_stock_id',
        'item_id',
        'quantity',
        'quantity_fractions',
        'item_stock_fraction_id',
        'reason',
        'register_user_id',
    ];

    public function itemStock()
    {
        return $this->belongsTo(ItemStock::class, 'item_stock_id');
    }

    public function itemStockFraction()
    {
        return $this->belongsTo(ItemStockFraction::class, 'item_stock_fraction_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function registerUser()
    {
        return $this->belongsTo(User::class, 'register_user_id');
    }
}
