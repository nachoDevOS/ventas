<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\RegistersUserEvents;

class EgresDetail extends Model
{
    use SoftDeletes, RegistersUserEvents;

    protected $table = 'egres_details';

    protected $fillable = [
        'egres_id',
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

    public function egres()
    {
        return $this->belongsTo(Egres::class, 'egres_id');
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
