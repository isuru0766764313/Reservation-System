<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RescheduleRequest extends Model
{
    use HasFactory;

    protected $table = "reschedule_requests_table";

    // Request lifecycle statuses
    const STATUS_PENDING = 1;
    const STATUS_ACCEPTED = 2;
    const STATUS_REJECTED = 3;

    protected $fillable = [
        'reservation_id',
        'customer_id',
        'hall_id',
        'new_date',
        'new_start_time',
        'new_end_time',
        'new_pre_arrange_time',
        'new_post_arrange_time',
        'old_date',
        'old_start_time',
        'old_end_time',
        'old_pre_arrange_time',
        'old_post_arrange_time',
        'status',
        'remarks',
    ];

    protected $casts = [
        'new_pre_arrange_time' => 'integer',
        'new_post_arrange_time' => 'integer',
        'old_pre_arrange_time' => 'integer',
        'old_post_arrange_time' => 'integer',
        'status' => 'integer',
    ];

    public function reservation()
    {
        return $this->belongsTo(ReservationModel::class, 'reservation_id');
    }

    public function customer()
    {
        return $this->belongsTo(CustomerModel::class, 'customer_id');
    }

    public function hall()
    {
        return $this->belongsTo(HallModel::class, 'hall_id');
    }
}
