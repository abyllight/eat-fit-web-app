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

    protected $dates = [
        'delivered_at',
        'reported_at',
    ];

    public function order()
    {
        return $this->belongsTo('App\Models\Order', 'order_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'courier_id', 'id');
    }

    public function getStatus()
    {
        if (!$this->delivered_at) {
            return '<td><span class="badge badge-danger">не доставлено</span></td>';
        }

        $time = explode('-', $this->order->time);
        $from = (float) $time[0] * 100;
        $to   = (float) $time[1] * 100;

        $delivered_at = (float) $this->delivered_at->format('H:i') * 100;
        $delivered_at = (int) $delivered_at;

        switch (true) {
            case $delivered_at >= $from && $delivered_at <= $to:
                $status = '<td><span class="badge badge-success">вовремя</span></td>';
                break;
            case $delivered_at < $from:
                $status = '<td><span class="badge badge-primary">рано</span></td>';
                break;
            case $delivered_at > $from:
                $status = '<td><span class="badge badge-warning">не вовремя</span></td>';
                break;
            default:
                $status = '<td><span class="badge badge-secondary">незвестно</span></td>';
                break;
        }

        return $status;
    }

    public function getDeliveredAt()
    {
        return $this->delivered_at ? $this->delivered_at->format('Y-m-d H:i:s') : '';
    }

    public function getReportedAt()
    {
        return $this->reported_at ? $this->reported_at->format('Y-m-d H:i:s') : '';
    }
}
