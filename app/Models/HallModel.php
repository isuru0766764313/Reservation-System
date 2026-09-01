<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HallModel extends Model
{
    use HasFactory;

    protected $table = "halls_table";
    protected $guard = 'admin';

    protected $fillable =
        [
            'admin_id',
            'name',
            'type',
            'price',
            'discount',
            'deposit',
            'cancellation_fee',
            'capacity',
            'max_pre_arrange_hours',
            'max_post_arrange_hours',
            'description',
            'address',
            'province',
            'district',
            'area',
            'latitude',
            'longitude',
            'images',
            'pdf',
            'clearence_form',
            'available',
            'booking_method'
            
        ];

    protected $casts =
        [
            'price' => 'decimal:2',
            'discount'=> 'decimal:2',
            'cancellation_fee'=> 'decimal:2',
            'deposit'=> 'decimal:2',
            'images' => 'array',
            'available' => 'boolean'
        ];

    // Hall has many relationships to "HallUnAvailability" model
    public function availability()
    {
        return $this->hasMany(HallUnAvailability::class, 'hall_id');
    }

    // Hall has many relationships to reservations model
    public function reservations()
    {
        return $this->hasMany(ReservationModel::class, 'hall_id');
    }

    // hall has many relationships to "PackagesModel"
    public function packages()
    {
        return $this->hasMany(PackagesModel::class, 'hall_id');
    }

    // hall has many relationships to "FixedPriceFacilitiesModel"
    public function fixedfacilities()
    {
        return $this->hasMany(FixedPriceFacilitiesModel::class, 'hall_id');
    }

    // hall has many relationships to "UnitPriceFacilitiesModel"
    public function unitfacilities()
    {
        return $this->hasMany(UnitPriceFacilitiesModel::class, 'hall_id');
    }

    // Hall belogs to admin
    public function admin()
    {
        return $this->belongsTo(AdminModel::class, 'admin_id');
    }

    




    protected $attributes =
        [
            'images' => '[]'  // Add default empty array
        ];

}