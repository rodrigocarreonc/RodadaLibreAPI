<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    protected $table = 'places';
    
    protected $fillable = [
        'name',
        'description',
        'schedule',
        'capacity',
        'cost',
        'latitude',
        'longitude',
        'category_id'
    ];

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function photos(){
        return $this->hasMany(Photo::class);
    }
}
