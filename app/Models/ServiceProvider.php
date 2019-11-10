<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceProvider extends Model
{
    protected $fillable = [
        'user_id', 'category_id', 'business_name', 'location', 'opening_hours', 'general_information', 'instagram', 'twitter', 'linkedin'
    ];

}
