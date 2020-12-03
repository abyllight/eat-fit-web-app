<?php

namespace App\Models;

use Carbon\Carbon;
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
        'day',
        'course',
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

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'courier_id', 'id');
    }

    public function reports()
    {
        return $this->hasMany('App\Models\Report', 'order_id', 'id');
    }

    public function getTag()
    {
        return $this->tag . ' ' . $this->size;
    }

    public function getColourTag()
    {
        $colour_tag = '<span class="badge badge-info">' . $this->getTag() .'</span>';

        switch ($this->tag) {
            case 'Select':
                $colour_tag = '<span class="badge badge-success">' . $this->getTag() .'</span>';
                break;
            case 'Lite':
                $colour_tag = '<span class="badge badge-warning">' . $this->getTag() .'</span>';
                break;
            case 'Daily':
                $colour_tag = '<span class="badge badge-danger">' . $this->getTag() .'</span>';
                break;

        }

        return $colour_tag;
    }

    public function getStatus()
    {
        $now = Carbon::now()->format('Y-m-d');

        if ($this->created_at->format('Y-m-d') === $now) {
            return '<span class="badge badge-success">Новый</span>';
        }elseif ($this->yaddress_old || $this->time_old) {
            return '<span class="badge badge-danger">Изменен</span>';
        }else {
            return '<span class="badge badge-secondary">Не изменен</span>';
        }
    }

    public function hasReportToday()
    {
        $report = $this->reports()->whereDate('created_at', Carbon::today()->toDateString())->first();

        return $report ? true : false;
    }

    public function hasDeliveredToday()
    {
        $report = $this->reports()->whereDate('created_at', Carbon::today()->toDateString())->first();

        return $report && $report->delivered_at ? true : false;
    }
}
