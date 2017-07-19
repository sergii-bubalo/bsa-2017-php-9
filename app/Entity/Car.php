<?php

namespace App\Entity;

use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    protected $fillable = [
        'model',
        'color',
        'registration_number',
        'year',
        'price',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
