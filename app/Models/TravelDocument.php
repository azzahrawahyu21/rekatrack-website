<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TravelDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'travel_document';

    protected $fillable = [
        'no_travel_document',
        'posting_date',
        'document_date',
        'is_backdate',
        'send_to',
        'driver_id',
        'driver_name',
        'vehicle_number',
        'po_number',
        'reference_number',
        'reference_date',
        'delivery_type',
        'project',
        'status',
        'start_time',
        'end_time'
    ];

    protected $dates = [
        'deleted_at',
        'posting_date',
        'document_date',
        'reference_date',
        'start_time',
        'end_time'
    ];

    public function items()
    {
        return $this->hasMany(Items::class);
    }

    public function trackingSystems()
    {
        return $this->hasMany(TrackingSystem::class);
    }

    public function deliveryConfirmation()
    {
        return $this->hasOne(DeliveryConfirmation::class);
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function attachments()
    {
        return $this->hasMany(TravelDocumentAttachment::class, 'travel_document_id');
    }

}
