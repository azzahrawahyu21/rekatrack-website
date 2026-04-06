<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Items extends Model
{
    use HasFactory;

    protected $table = 'items';

    protected $fillable = [
        'travel_document_id', 'no', 'item_code', 'item_name', 'qty_send', 'total_send', 'qty_po', 'unit_id', 'description', 'information','sub_item_group_title',
    ];

    public function travelDocument()
    {
        return $this->belongsTo(TravelDocument::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function subItems()
    {
        return $this->hasMany(SubItem::class, 'item_id');
    }
}
