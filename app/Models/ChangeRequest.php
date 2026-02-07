<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChangeRequest extends Model
{
    protected $table = 'change_requests';

    protected $fillable = ['action_type', 'payload', 'status', 'admin_comment', 'user_id', 'place_id'];

    protected $casts = [
        'payload' => 'array', 
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function place()
    {
        return $this->belongsTo(Place::class);
    }
}
