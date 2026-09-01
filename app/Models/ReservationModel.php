<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservationModel extends Model
{
    use HasFactory;

    protected $table = "reservations_table";
    protected $guard = 'customers';

    protected $fillable =
        [
            'customer_id',
            'ref_code',
            'customer_name',
            'customer_email',
            'customer_tel',
            'hall_id',
            'hall_name',
            'reservation_date',
            'advancePaymentDate',
            'cancellationExpiryDate',
            'rescheduledExpiryDate',
            'start_time',
            'end_time',
            'charge',
            'advanceAmount',
            'discount',
            'discount_custom',
            'deposit',
            'accepted',
            'payment_status',
            'reserved',
            'receipt_path',
            'clearence_form',
            'reservation_type',
            'package_id',
            'pre_arrange_time',
            'post_arrange_time',
            'status',
            'agree_terms',
            'logged',
            'advancePaid',
            'advance_accepted',
            'user_cancelled',
            're_scheduled',
        ];

    protected $casts =
        [
            'charge'=> 'decimal:2',
            'advanceAmount'=> 'decimal:2',
            'discount'=> 'decimal:2',
            'discount_custom'=> 'decimal:2',
            'deposit'=> 'decimal:2',
            'agree_terms'=> 'boolean',
            'logged'=> 'boolean',
            'advancePaid'=> 'boolean',
            'advance_accepted'=> 'boolean',
            'user_cancelled'=> 'boolean',
            're_scheduled'=> 'boolean',
            'accepted' => 'boolean',
            'payment_status' => 'boolean',
            'reserved' => 'boolean',
            'pre_arrange_time' => 'integer',
            'post_arrange_time' => 'integer',
            'status' => 'integer'
        ];

    // These relationships allow you to access the associated customer and hall for each reservation.
    public function customer()
    {
        return $this->belongsTo(CustomerModel::class, 'customer_id');
    }

    public function hall()
    {
        return $this->belongsTo(HallModel::class, 'hall_id');
    }

    public function package()
    {
        return $this->belongsTo(PackagesModel::class, 'package_id');
    }

    // NEW RELATIONSHIPS FOR FACILITIES
    public function fixedFacilities()
    {
        return $this->belongsToMany
        (
            FixedPriceFacilitiesModel::class,
            'reservation_fixed_facilities',
            'reservation_id',
            'fixed_facility_id'
        );
    }

    public function unitFacilities()
    {
        return $this->belongsToMany
        (
            UnitPriceFacilitiesModel::class,
            'reservation_unit_facilities',
            'reservation_id',
            'unit_facility_id'
        );
    }

    // Helper method to get all facilities with their types
    public function getAllFacilitiesAttribute()
    {
        return [
            'fixed' => $this->fixedFacilities,
            'unit' => $this->unitFacilities,
        ];
    }

    public function payments()
    {
        return $this->hasMany(Payments::class, 'reservation_id');
    }
}