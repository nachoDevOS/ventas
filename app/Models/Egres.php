<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\RegistersUserEvents;

class Egres extends Model
{
    use SoftDeletes, RegistersUserEvents;

    protected $table = 'egres';

    protected $fillable = [
        'reason',
        'dateEgress',
        'observation',
        'status',

        'registerUser_id',
        'registerRole',
        'deleted_at',
        'deleteUser_id',
        'deleteRole',
        'deleteObservation',
    ];

    public function details()
    {
        return $this->hasMany(EgresDetail::class, 'egres_id');
    }

    public function registerUser()
    {
        return $this->belongsTo(User::class, 'registerUser_id');
    }
}
