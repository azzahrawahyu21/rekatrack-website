<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $table = 'location';

    // protected $fillable = [
    //     'track_id', 'latitude', 'longitude', 'time_stamp',
    // ];
    protected $fillable = [
        'track_id', 'latitude', 'longitude', 'is_checkpoint', 'distance_from_last', 'time_stamp',
    ];

    public function track()
    {
        return $this->belongsTo(Track::class);
    }
}
