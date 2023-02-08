<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FavouriteVisit extends Model
{
    protected $table = 'favourite_visits';

    protected $fillable = [
        'user_id',
        'visit_id'
    ];

    public $timestamps = false;
}
