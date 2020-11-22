<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Week;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index()
    {
        $orders = Order::where('active', true)->orderBy('name')->get();
        $week   = Week::find(1);

        return view('admin.orders', ['orders' => $orders, 'week' => $week]);
    }

    /*
     * Vrabote = 16566964
     * Probnyi = 16536847
     * */
    public function fetchLeads()
    {
        $amo = new AmoController();

        $v_rabote = $amo->amo_leads(16566964, 300) ?? [];
        $probnyi  = $amo->amo_leads(16536847, 100) ?? [];

        return array_merge($v_rabote, $probnyi);
    }

    public function getOrders()
    {
        $orders = $this->fetchLeads();

        if (empty($orders)) {
            return redirect()->route('admin.orders')->with('error', 'Не удалось получить данные');
        }

        $is_weekend = Week::find(1)->isWeekend;

        foreach ($orders as $order) {

            $fields = [
                'amo_id'    => null,
                'name'      => null,
                'tag'       => null,
                'size'      => null,
                'phone'     => null,
                'whatsapp'  => null,
                't1'        => null,
                't2'        => null,
                'y1'        => null,
                'y2'        => null,
                'a1'        => null,
                'a2'        => null,
                'tb'        => null,
                'ab'        => null,
                'yb'        => null,
                'addition'  => null
            ];

            $fields['amo_id'] = $order['id'];
            $fields['name']   = $order['name'];

            foreach ($order['custom_fields'] as $key => $field) {
                switch ($field['id']) {
                    case '373971'://Время
                        $fields['t1'] = $field["values"][0]["value"];
                        break;
                    case '478705'://Время доп
                        $fields['t2'] = $field["values"][0]["value"];
                        break;
                    case '321281'://Логистика
                        $fields['addition'] = $field["values"][0]["value"];
                        break;
                    case '478767'://Яндекс
                        $fields['y1'] = $field["values"][0]["value"];
                        break;
                    case '478769'://Яндекс доп
                        $fields['y2'] = $field["values"][0]["value"];
                        break;
                    case '478763'://Адрес
                        $fields['a1'] = $field["values"][0]["value"];
                        break;
                    case '478765'://Адрес доп
                        $fields['a2'] = $field["values"][0]["value"];
                        break;
                    case '478771'://Телефон
                        $fields['phone'] = $field["values"][0]["value"];
                        break;
                    case '478851'://Ватсап
                        $fields['whatsapp'] = $field["values"][0]["value"];
                        break;
                    case '321197'://Тип
                        $fields['tag'] = $field["values"][0]["value"];
                        break;
                    case '327953'://Размер
                        $fields['size'] = $field["values"][0]["value"];
                        break;
                }
            }

            $fields['tb'] = $is_weekend ? $fields['t2'] : $fields['t1'];
            $fields['yb'] = $is_weekend ? $fields['y2'] : $fields['y1'];
            $fields['ab'] = $is_weekend ? $fields['a2'] : $fields['a1'];

            $existing_order = Order::where('amo_id', $order['id'])->orWhere('name', $order['name'])->first();

            if ($existing_order) {
                $this->createOrder($existing_order, $fields, $is_weekend);
            }else {
                Order::create([
                    'amo_id'    => $order['id'],
                    'name'      => $order['name'],
                    'tag'       => $fields['tag'],
                    'size'      => $fields['size'],
                    'phone'     => $fields['phone'],
                    'whatsapp'  => $fields['whatsapp'],
                    'time'      => $fields['tb'],
                    'time1'     => $fields['t1'],
                    'time2'     => $fields['t2'],
                    'yaddress'  => $fields['yb'],
                    'yaddress1' => $fields['y1'],
                    'yaddress2' => $fields['y2'],
                    'address'   => $fields['ab'],
                    'address1'  => $fields['a1'],
                    'address2'  => $fields['a2'],
                    'addition'  => $fields['addition'],
                    'active'    => true
                ]);
            }
        }

        Order::whereNull('active')->update([
            'position'      => null,
            'interval'      => null,
            'time1_old'     => null,
            'time2_old'     => null,
            'yaddress1_old' => null,
            'yaddress2_old' => null
        ]);

        $orders = Order::where('active', true)->orderBy('name')->get();

        return redirect()->route('admin.orders', ['orders' => $orders]);
    }

    public function createOrder(Order $order, array $fields = [], bool $is_weekend = false)
    {
        $order->amo_id   = $fields['amo_id'];
        $order->name     = $fields['name'];
        $order->tag      = $fields['tag'];
        $order->size     = $fields['size'];
        $order->phone    = $fields['phone'];
        $order->whatsapp = $fields['whatsapp'];
        $order->time     = $fields['tb'];
        $order->yaddress = $fields['yb'];
        $order->address  = $fields['ab'];
        $order->address1 = $fields['a1'];
        $order->address2 = $fields['a2'];
        $order->addition = $fields['addition'];

        $now = Carbon::now();

        if($order->time1 !== $fields['t1']) {
            $order->time1_old = $order->time1;
            $order->time1     = $fields['t1'];
        }

        if($now->diffInDays($order->updated_at) > 1 && $order->time1_old) {
            $order->time1_old = null;
        }

        if($order->time2 !== $fields['t2']) {
            $order->time2_old = $order->time2;
            $order->time2     = $fields['t2'];
        }

        if($now->diffInDays($order->updated_at) > 1 && $order->time2_old) {
            $order->time2_old = null;
        }

        if($order->yaddress1 !== $fields['y1']) {
            $order->yaddress1_old = $order->yaddress1;
            $order->yaddress1     = $fields['y1'];
            $order->lat           = null;
            $order->lng           = null;
            $order->lat1          = null;
            $order->lng1          = null;
            $order->courier_id    = null;
            $order->courier1_id   = null;
        }

        if($now->diffInDays($order->updated_at) > 1 && $order->yaddress1_old) {
            $order->yaddress1_old = null;
        }

        if($order->yaddress2 !== $fields['y2']) {
            $order->yaddress2_old = $order->yaddress2;
            $order->yaddress2     = $fields['y2'];
            $order->lat           = null;
            $order->lng           = null;
            $order->lat2          = null;
            $order->lng2          = null;
            $order->courier_id    = null;
            $order->courier2_id   = null;
        }

        if($now->diffInDays($order->updated_at) > 1 && $order->yaddress2_old) {
            $order->yaddress2_old = null;
        }

        $order->lat = $is_weekend ? $order->lat2 : $order->lat1;
        $order->lng = $is_weekend ? $order->lng2 : $order->lng1;
        $order->courier_id = $is_weekend ? $order->courier2_id : $order->courier1_id;
        $order->active  = true;
        $order->save();
    }
}
