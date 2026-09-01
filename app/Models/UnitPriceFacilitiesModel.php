<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitPriceFacilitiesModel extends Model
{
    use HasFactory;

    protected $table = "unit_price_facilities_table";

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

    


    // Unit price facilitites belong to Hall
    public function hall()
    {
        return $this->belongsTo(HallModel::class, 'hall_id');
    }
}
