<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryConfirmation extends Model
{
    protected $table = 'delivery_confirmations';

    protected $fillable = [
        'travel_document_id',
        'receiver_name',
        'received_at',
        'note',
        'photo_path',
    ];

    public function travelDocument()
    {
        return $this->belongsTo(TravelDocument::class, 'travel_document_id');
    }

    public function photos()
    {
        return $this->hasMany(\App\Models\DeliveryConfirmationPhoto::class, 'delivery_confirmation_id');
    }
}
