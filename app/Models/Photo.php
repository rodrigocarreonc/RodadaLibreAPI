<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    protected $table = 'photos';

    protected $fillable = ['url_source', 'status', 'place_id'];

    protected static function booted(){
        static::addGlobalScope('approved', function ($builder){
            $builder->where('status', 'approved');
        });
    }

    public function place(){
        return $this->belongsTo(Place::class);
    }
}
