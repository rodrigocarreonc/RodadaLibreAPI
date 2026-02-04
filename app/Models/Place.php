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

    public static function validations(){
        return [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            
            'schedule' => 'nullable|string',
            'capacity' => 'nullable|integer',
            'cost' => 'nullable|numeric',

            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',

            'photo_ids' => 'nullable|array',
            'photo_ids.*' => 'exists:photos,id'
        ];
    }

    public static function messageErrors(){
        return [
            'category_id.required' => 'Category is required.',
            'category_id.exists' => 'The selected category does not exist.',

            'name.required' => 'Name is required.',
            'name.string' => 'Name must be text.',
            'name.max' => 'Name cannot be longer than 100 characters.',

            'description.string' => 'Description must be text.',
            'schedule.string' => 'Schedule must be text.',
            'capacity.integer' => 'Capacity must be an integer.',
            'cost.numeric' => 'Cost must be a number.',

            'latitude.required' => 'Latitude is required.',
            'latitude.numeric' => 'Latitude must be a number.',

            'longitude.required' => 'longitude is required.',
            'longitude.numeric' => 'longitude must be a number.',

            'photo_ids.array' => 'Photo IDs must be submitted as an array.',
            'photo_ids.*.exists' => 'One of the selected photos does not exist.'
        ];
    }
}
