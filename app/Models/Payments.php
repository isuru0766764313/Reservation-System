<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payments extends Model
{
    use HasFactory;

    protected $table = "payments";
    protected $guard = 'customers';

    protected $fillable = 
    [
        'reservation_id',
        'payment_alias',
        'amount',
        'receipt_path',
        'status',
        'remarks'
    ] ;
    protected $casts = 
    [
        'amount'=> 'decimal:2',
        'status' => 'integer',
        'remarks' => 'string'
    ] ;
    

    public function reservation()
    {
        return $this->belongsTo(ReservationModel::class, 'reservation_id');
    }
}
