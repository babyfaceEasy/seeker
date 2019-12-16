<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'customer_id',
        'service_provider_id',
        'service_id',
        'location',
        'offer_on',
        'status',
        'amount',
        'comment'
    ];

    // relationships
}
