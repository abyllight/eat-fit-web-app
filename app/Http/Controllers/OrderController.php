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

        $v_rabote = $amo->getAmoLeads(16566964, 400);

        if (!$v_rabote['status']) {
            return $v_rabote;
        }

        $v_rabote = $v_rabote['content']['response']['leads'];

        $probnyi  = $amo->getAmoLeads(16536847, 100);

        if (!$probnyi['status']) {
            return $probnyi;
        }

        $probnyi = $probnyi['content']['response']['leads'];

        $array = array_merge($v_rabote, $probnyi);

        return [
            'status' => true,
            'data'   => $array
        ];
    }

    public function getOrders()
    {
        $orders = $this->fetchLeads();

        if (!$orders['status']) {
            return redirect()->route('admin.orders')->with('error', $orders['message'] . ', ' . $orders['code']);
        }

        $is_weekend = Week::find(1)->is_weekend;

        Order::where('active', true)->update(['active' => null]);

        foreach ($orders['data'] as $order) {

            $fields = [
                'amo_id'    => null,
                'name'      => null,
                'tag'       => null,
                'size'      => null,
                'day'       => null,
                'course'    => null,
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
                    //День
                    case '328089':
                        $fields['day'] = $field["values"][0]["value"];
                        break;
                    //Курс
                    case '321235':
                        $fields['course'] = $field["values"][0]["value"];
                        break;
                    //Время
                    case '373971':
                        $fields['t1'] = $field["values"][0]["value"];
                        break;
                    //Время доп
                    case '478705':
                        $fields['t2'] = $field["values"][0]["value"];
                        break;
                    //Логистика
                    case '321281':
                        $fields['addition'] = $field["values"][0]["value"];
                        break;
                    //Яндекс
                    case '478767':
                        $fields['y1'] = $field["values"][0]["value"];
                        break;
                    //Яндекс доп
                    case '478769':
                        $fields['y2'] = $field["values"][0]["value"];
                        break;
                    //Адрес
                    case '478763':
                        $fields['a1'] = $field["values"][0]["value"];
                        break;
                    //Адрес доп
                    case '478765':
                        $fields['a2'] = $field["values"][0]["value"];
                        break;
                    //Телефон
                    case '478771':
                        $fields['phone'] = $field["values"][0]["value"];
                        break;
                    //Ватсап
                    case '478851':
                        $fields['whatsapp'] = $field["values"][0]["value"];
                        break;
                    //Тип
                    case '321197':
                        $fields['tag'] = $field["values"][0]["value"];
                        break;
                    //Размер
                    case '327953':
                        $fields['size'] = $field["values"][0]["value"];
                        break;
                }
            }

            $fields['tb'] = $is_weekend ? $fields['t2'] : $fields['t1'];
            $fields['yb'] = $is_weekend ? $fields['y2'] : $fields['y1'];
            $fields['ab'] = $is_weekend ? $fields['a2'] : $fields['a1'];

            $existing_order = Order::where('amo_id', $order['id'])->orWhere('name', $order['name'])->first();

            if ($existing_order) {
                $this->updateOrder($existing_order, $fields, $is_weekend);
            }else {
                Order::create([
                    'amo_id'    => $order['id'],
                    'name'      => $order['name'],
                    'tag'       => $fields['tag'],
                    'size'      => $fields['size'],
                    'day'       => $fields['day'],
                    'course'    => $fields['course'],
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
            'time_old'      => null,
            'time1_old'     => null,
            'time2_old'     => null,
            'yaddress_old'  => null,
            'yaddress1_old' => null,
            'yaddress2_old' => null,
            'day_old'       => null
        ]);

        return redirect()->route('admin.orders')->with('status', 'Данные получены!');
    }

    public function updateOrder(Order $order, array $fields = [], bool $is_weekend = false)
    {
        $order->amo_id   = $fields['amo_id'];
        $order->name     = $fields['name'];
        $order->tag      = $fields['tag'];
        $order->size     = $fields['size'];
        $order->day      = $fields['day'];
        $order->course   = $fields['course'];
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

        $order->lat          = $is_weekend ? $order->lat2 : $order->lat1;
        $order->lng          = $is_weekend ? $order->lng2 : $order->lng1;
        $order->time_old     = $is_weekend ? $order->time2_old : $order->time1_old;
        $order->yaddress_old = $is_weekend ? $order->yaddress2_old : $order->yaddress1_old;
        $order->courier_id   = $is_weekend ? $order->courier2_id : $order->courier1_id;
        $order->active       = true;
        $order->save();
    }
}
