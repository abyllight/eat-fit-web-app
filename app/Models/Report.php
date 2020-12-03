<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'order_id',
        'courier_id',
        'created_at',
        'comment',
        'reported_at',
        'payment',
        'payment_method',
        'delivered_at'
    ];

    public function order()
    {
        return $this->belongsTo('App\Models\Order', 'order_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'courier_id', 'id');
    }
}
