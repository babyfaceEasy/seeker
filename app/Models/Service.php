<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class Service extends Model implements HasMedia
{
    use HasMediaTrait;
    protected $fillable = ['name', 'user_id', 'category_id'];

    public function format()
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'name' => $this->name,
            'category_id' => $this->category_id,
            'created_on' => $this->created_at != null ? $this->created_at->diffForHumans() : null,
            'pictures' => $this->getMedia('service_pics') ->map(function ($media){
              return $media->getFullUrl();
            })
        ];
    }

    public static function getSortableColumn() : string
    {
        return 'name';
    }

    // collections for this model
    public function registerMediaCollections()
    {
        $this->addMediaCollection('service_pics')
            ->useDisk('service_pics');
    }

    // relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}