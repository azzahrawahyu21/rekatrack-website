<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Track extends Model
{
    use HasFactory;

    protected $table = 'track';

    protected $fillable = [
        'driver_id', 'time_stamp', 'status',
    ];

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function locations()
    {
        return $this->hasMany(Location::class);
    }

    public function trackingSystems()
    {
        return $this->hasMany(TrackingSystem::class);
    }
}
