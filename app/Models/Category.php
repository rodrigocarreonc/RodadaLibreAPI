<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';

    protected $filleable = ['name', 'slug'];

    public function places(){
        return $this->hasMany(Places::class);
    }
}
