<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FixedPriceFacilitiesModel extends Model
{
    use HasFactory;

    protected $table = "fixed_price_facilities_table";

    protected $fillable =
        [
            'hall_id',
            'name',
            'charge'
        ];

    protected $casts =
        [
            'charge' => 'decimal:2'
        ];

       
    
    // Fixed price facilitites belong to Hall
    public function hall()
    {
        return $this->belongsTo(HallModel::class, 'hall_id');
    }
}
