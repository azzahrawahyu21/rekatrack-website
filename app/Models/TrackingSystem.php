<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrackingSystem extends Model
{
    use HasFactory;

    protected $table = 'tracking_system';

    protected $fillable = [
        'track_id', 'travel_document_id', 'time_stamp', 'status',
    ];

    public function track()
    {
        return $this->belongsTo(Track::class);
    }

    public function travelDocument()
    {
        return $this->belongsTo(TravelDocument::class);
    }

    public function locations()
    {
        return $this->hasMany(Location::class, 'track_id', 'track_id');
    }
}
