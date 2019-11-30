<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = ['name', 'user_id', 'category_id'];

    public function format()
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'name' => $this->name,
            'category_id' => $this->category_id,
            'created_on' => $this->created_at != null ? $this->created_at->diffForHumans() : null,
        ];
    }

    public static function formatData($data)
    {
        $data = (object) $data;
        dd($data->created_at);
        return [
            'id' => $data->id,
            'user_id' => $data->user_id,
            'name' => $data->name,
            'category_id' => $data->category_id,
            'created_on' => $data->created_at != null ? $data->created_at->diffForHumans() : null,
        ];
    }

    public static function getSortableColumn() : string
    {
        return 'name';
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