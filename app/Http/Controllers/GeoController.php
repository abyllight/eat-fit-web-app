<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Week;
use Illuminate\Http\Request;

class GeoController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index()
    {
        $is_weekend = Week::find(1)->is_weekend;

        $orders = Order::where('active', true)->where('lat1', null)->orderBy('name', 'asc')->get();

        if ($is_weekend) {
            $orders = Order::where('active', true)->where('lat2', null)->orderBy('name', 'asc')->get();
        }

        return view('admin.geo', ['orders' => $orders]);
    }

    public function geocode()
    {
        $is_weekend = Week::find(1)->is_weekend;

        if ($is_weekend) {
            return $this->geocode_weekend();
        }else {
            return $this->geocode_week();
        }
    }

    public function geocode_week()
    {
        $orders = Order::where('active',true)->where('lat1', null)->get();

        if (count($orders) === 0) {
            return redirect()->back()->with('status', 'Все адреса уже геокодированы!');
        }

        $link = 'https://geocode-maps.yandex.ru/1.x/?apikey=df752091-7d67-4202-af5c-322b09947c85&geocode=';

        foreach ($orders as $order) {

            if ($order->yaddress1) {

                $xml     = simplexml_load_file($link . urlencode($order->yaddress1));
                $lat_lng = explode(" ", $xml->GeoObjectCollection->featureMember->GeoObject->Point->pos);

                if (!$lat_lng) {
                    return redirect()->back()->with('error', 'Не удалось геокодировать адрес' . $order->name . ' ' . $order->yaddress1);
                }

                $order->lat1 = $lat_lng[1];
                $order->lng1 = $lat_lng[0];
                $order->lat  = $lat_lng[1];
                $order->lng  = $lat_lng[0];

                if($order->yaddress1 === $order->yaddress2){
                    $order->lat2 = $lat_lng[1];
                    $order->lng2 = $lat_lng[0];
                }

                $order->save();
            }
        }

        return redirect()->route('admin.geo')->with('status', 'Адреса успешно геокодированы1!');
    }

    public function geocode_weekend()
    {
        $orders = Order::where('active',true)->where('lat2', null)->get();

        if (count($orders) === 0) {
            return redirect()->back()->with('status', 'Все адреса уже геокодированы!');
        }

        $link = 'https://geocode-maps.yandex.ru/1.x/?apikey=df752091-7d67-4202-af5c-322b09947c85&geocode=';

        foreach ($orders as $order) {

            if ($order->yaddress2) {

                $xml     = simplexml_load_file($link . urlencode($order->yaddress2));
                $lat_lng = explode(" ", $xml->GeoObjectCollection->featureMember->GeoObject->Point->pos);

                if (!$lat_lng) {
                    return redirect()->back()->with('error', 'Не удалось геокодировать адрес' . $order->name . ' ' . $order->yaddress2);
                }

                $order->lat2 = $lat_lng[1];
                $order->lng2 = $lat_lng[0];
                $order->lat  = $lat_lng[1];
                $order->lng  = $lat_lng[0];

                $order->save();
            }
        }

        return redirect()->route('admin.geo')->with('status', 'Адреса успешно геокодированы2!');
    }

    public function interval()
    {
        Order::where('active', true)->update([
            'interval' => null
        ]);

        $orders = Order::where('active', true)->orderBy('time')->get()->groupBy(['time']);

        if (count($orders) === 0) {
            return redirect()->back()->with('error', 'Нет заказов с заполненым временем!');
        }

        $intervals = [
            [630, 700],
            [700, 730],
            [730, 800],
            [800, 830],
            [830, 900],
            [900, 930],
            [930, 1000]
        ];

        $checkint = collect();

        foreach ($orders as $key => $value) {

            $pieces = explode("-", $value[0]->time);
            $dfrom  = intval((float) $pieces[0] * 100);
            $dto    = intval((float) $pieces[1] * 100);
            $dfrom  = $dfrom ?? null;
            $dto    = $dto ?? null;

            for($i = 0; $i < count($intervals); $i++) {

                if($dfrom >= $intervals[$i][0] && $dfrom < $intervals[$i][1]) {

                    for ($j = $i; $j < count($intervals); $j++){

                        if($dto > $intervals[$j][0] && $dto <= $intervals[$j][1]) {

                            if($j === $i) {
                                foreach($value as $v) {
                                    $v->interval = $j+1;
                                    $v->save();
                                }
                            }else{
                                for ($a = $i; $a <= $j; $a++) {
                                    $checkint->push([
                                        'time' => $value[0]->time,
                                        'i'         => $a+1
                                    ]);
                                }
                            }
                        }
                    }
                }
            }
        }

        foreach ($checkint->groupBy('time') as $key => $value) {

            $orders = Order::where('active', true)->where('time', $value[0]['time'])->get();

            foreach($orders as $val) {

                $almost = collect();

                foreach ($value as $key => $i) {

                    $uzhe = Order::where('active', true)->where('interval', $i['i'])->count();

                    $almost->push([
                        'i'   => $i['i'],
                        'num' => $uzhe
                    ]);
                }

                $min           = $almost->where('num', $almost->min('num'))->last()['i'];
                $val->interval = $min;
                $val->save();
            }
        }

        Order::where('active', true)->whereNull('interval')->update([
            'interval' => 0
        ]);

        return redirect()->back()->with('status', 'Успешно отсортировано!');
    }
}
