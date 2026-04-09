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
            'images'   => 'required|array|max:5',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120'
        ];
    }

    public static function createMessageErrors(){
        return [
            'images.required' => 'Debes subir al menos una imagen.',
            'images.array'    => 'El formato de las imágenes no es válido.',
            'images.max'      => 'No puedes subir más de 5 imágenes a la vez.',
            'images.*.image'  => 'Uno de los archivos no es una imagen válida.',
            'images.*.mimes'  => 'Las imágenes deben ser de tipo: jpeg, png, jpg, webp.',
            'images.*.max'    => 'Una de las imágenes supera el tamaño máximo de 5MB.',
        ];
    }
}
