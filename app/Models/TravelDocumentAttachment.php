<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TravelDocumentAttachment extends Model
{
    protected $table = 'travel_document_attachments';

    protected $fillable = [
        'travel_document_id',
        'name',
    ];

    public function travelDocument()
    {
        return $this->belongsTo(TravelDocument::class, 'travel_document_id');
    }
}
