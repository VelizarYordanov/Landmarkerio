<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FavouritePlace extends Model
{
    use HasFactory;

    protected $table = 'favourite_places';

    protected $fillable = [
        'user_id',
        'place_id'
    ];

    public $timestamps = false;
}
