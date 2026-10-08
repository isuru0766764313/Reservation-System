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

    // Human-readable labels for the stored hall type slugs (used for display only;
    // the raw 'type' value in the database is never changed)
    public static function typeLabels(): array
    {
        return [
            'wedding'         => 'Wedding',
            'party'           => 'Party',
            'exhibition'      => 'Exhibition',
            'reception'       => 'Reception',
            'sport'           => 'Sport',
            'arena'           => 'Arena',
            'concert'         => 'Concert',
            'memorial'        => 'Memorial',
            'lecture'         => 'Lecture',
            'building'        => 'Building',
            'floor'           => 'Floor',
            'room'            => 'Room',
            'outdoortheator'  => 'Outdoor Theator',
            'multipurpose'    => 'Multi-Purpose',
            'resorts'         => 'Resorts',
            'bangalow'        => 'Bangalow',
            'conference'      => 'Conference',
            'banquet'         => 'Banquet',
            'convention'      => 'Convention',
            'crematorium'     => 'Crematorium',
            'auditorium'      => 'Auditorium',
            'community'       => 'Community',
            'stadium'         => 'Stadium',
            'outdoorground'   => 'Outdoor Ground',
        ];
    }

    // Accessor: returns the formatted display label for the hall type
    public function getTypeLabelAttribute(): string
    {
        $labels = self::typeLabels();

        return $labels[$this->type] ?? ucwords($this->type);
    }

}