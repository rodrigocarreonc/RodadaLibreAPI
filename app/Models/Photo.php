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

    public static function createValidation(){
        return [
            'image' => 'required|image|mimes:jpeg,png,jpg|max:5120'
        ];
    }

    public static function createMessageErrors(){
        return [
            'image.required' => 'Image file is required.',
            'image.image' => 'The file must be an image.',
            'image.mimes' => 'The image must be a file of type: jpeg, png, jpg.',
            'image.max' => 'The image size must not exceed 5MB.'
        ];
    }
}
