<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\RegistersUserEvents;

class Item extends Model
{
    use HasFactory, RegistersUserEvents, SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'category_id',
        'presentation_id',
        'laboratory_id',
        'line_id',
        'nameGeneric',
        'nameTrade',
        'image',
        'observation',
        'status',

        'fraction',
        'fractionPresentation_id',
        'fractionQuantity',

        'registerUser_id',
        'registerRole',
        'deleted_at',
        'deleteUser_id',
        'deleteRole',
        'deleteObservation',
    ];

    public function laboratory()
    {
        return $this->belongsTo(Laboratory::class, 'laboratory_id')->withTrashed();
    }
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id')->withTrashed();
    }
    public function presentation()
    {
        return $this->belongsTo(Presentation::class, 'presentation_id')->withTrashed();
    }
    public function line()
    {
        return $this->belongsTo(Line::class, 'line_id')->withTrashed();
    }
}
