<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name'];

    /**
     * Tells us how category object would be returned.
     * @return array
     */
    public function format()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'last_updated' => $this->updated_at != null ? $this->updated_at->diffForHumans() : null,
        ];
    }

    // relationship
    public function services()
    {
        return $this->hasMany(Service::class);
    }
}
