<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HallUnAvailability extends Model
{
    use HasFactory;

    protected $table = 'hall_availabilities';

    protected $fillable = 
    [
        'hall_id',
        'date',
        'start_time',
        'end_time'
    ];

    public function hall()
    {
        return $this->belongsTo(HallModel::class,'hall_id');
    }
}
