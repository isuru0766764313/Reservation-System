<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackagesModel extends Model
{
    use HasFactory;
    protected $table = "packages_table";

    protected $fillable =
        [
            'hall_id',
            'name',
            'price',
            'hourly_rate',
            'discount',
            'description',
            'duration',
            'maximum_hours',
            'fixed_price_facilities',
            'unit_price_facilities'
        ];

    protected $casts =
        [
            'price' => 'decimal:2',
            'hourly_rate' => 'decimal:2',
            'discount' => 'decimal:2',
            'maximum_hours' => 'decimal:2',
            'fixed_price_facilities' => 'array',
            'unit_price_facilities' => 'array'
        ];


    // Packages belog to Hall
    public function hall()
    {
        return $this->belongsTo(HallModel::class, 'hall_id');
    }

    public function reservations()
    {
        return $this->hasMany(ReservationModel::class, 'package_id');
    }

    // Accessor for fixed_price_facilities to ensure it's always an array
    public function getFixedPriceFacilitiesAttribute($value)
    {
        return is_array($value) ? $value : json_decode($value, true) ?? [];
    }

    // Accessor for unit_price_facilities to ensure it's always an array
    public function getUnitPriceFacilitiesAttribute($value)
    {
        return is_array($value) ? $value : json_decode($value, true) ?? [];
    }

    public function getFixedFacilitiesAttribute()
    {
        $facilityIds = $this->attributes['fixed_price_facilities'] ?? null;

        if (empty($facilityIds)) {
            return collect([]);
        }

        if (is_string($facilityIds)) {
            $facilityIds = json_decode($facilityIds, true);
        }

        if (!is_array($facilityIds) || empty($facilityIds)) {
            return collect([]);
        }

        return FixedPriceFacilitiesModel::whereIn('id', $facilityIds)
            ->where('hall_id', $this->hall_id)
            ->get();
    }

    public function getUnitFacilitiesAttribute()
    {
        $facilityIds = $this->attributes['unit_price_facilities'] ?? null;

        if (empty($facilityIds)) {
            return collect([]);
        }

        if (is_string($facilityIds)) {
            $facilityIds = json_decode($facilityIds, true);
        }

        if (!is_array($facilityIds) || empty($facilityIds)) {
            return collect([]);
        }

        return UnitPriceFacilitiesModel::whereIn('id', $facilityIds)
            ->where('hall_id', $this->hall_id)
            ->get();
    }
    
}
