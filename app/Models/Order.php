<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'amo_id',
        'name',
        'tag',
        'size',
        'phone',
        'whatsapp',
        'time',
        'time1',
        'time2',
        'yaddress',
        'yaddress1',
        'yaddress2',
        'address',
        'address',
        'addition',
        'active',
    ];

    public function getTag()
    {
        return $this->tag . ' ' . $this->size;
    }
}
