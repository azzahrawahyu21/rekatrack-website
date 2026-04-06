<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryConfirmationPhoto extends Model
{
    protected $table = 'delivery_confirmation_photos';

    protected $fillable = [
        'delivery_confirmation_id',
        'photo_path',
    ];

    public function confirmation()
    {
        return $this->belongsTo(DeliveryConfirmation::class, 'delivery_confirmation_id');
    }
}
