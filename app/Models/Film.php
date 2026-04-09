<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Film extends Model
{
    protected $table = 'film';
    protected $primaryKey = 'film_id';
    public $timestamps = false;

    protected $fillable = [
        'title',
        'description',
        'release_year',
        'original_language_id',
        'rental_duration',
        'rental_rate',
        'length',
        'replacement_cost',
        'rating',
        'last_update',
    ];
}
