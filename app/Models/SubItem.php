<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubItem extends Model
{
    use HasFactory;

    protected $table = 'sub_items';

    protected $fillable = [
        'item_id',
        'item_code',
        'item_name',
        'qty_send',
        'total_send',
        'qty_po',
        'unit_id',
        'description',
        'information',
    ];

    public function item()
    {
        return $this->belongsTo(Items::class, 'item_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }
}
