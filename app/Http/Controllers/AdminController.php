<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Week;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index()
    {
        /*$amo = new AmoController();
        $amo->amo_auth();
        $subdomain = env('AMO_SUBDOMAIN', '');
        $link = 'https://' . $subdomain . '.amocrm.ru/private/api/v2/json/leads/set';
        $success = $amo->getAmoLeads(142, 500, 1603238400, 1604102400);
        $now = strtotime('now');
        //dd($success);
        foreach ($success['content']['response']['leads'] as $key => $item) {
            $course = null;

            foreach ($item['custom_fields'] as $field) {
                if ($field['id'] === 456321) {
                    $course = (int)$field['values'][0]['value'];
                }
            }

            dump($key + 1, $item['name'], $item['price'], $course);

            if ($course) {

                $leads['request']['leads']['update'] = array(
                    array(
                        'id' => $item['id'],
                        'last_modified' => $now,
                        'price' => $course
                    )
                );

                $amo->amo_post($link, 'POST', $leads);
            }
        }*/

        $week = Week::find(1);

        return view('admin.home', ['week' => $week]);
    }

    public function setWeek(Request $request)
    {
        $value = $request['options'];

        if (!$value) {
            return redirect()->route('admin.home')->with('error', 'Не удалось переключить');
        }

        $week             = Week::find(1);
        $week->is_weekend = $value === '1' ? false : true;
        $week->name       = $value === '1' ? 'Будни' : 'Выходные';
        $week->save();

        $orders = Order::where('active',true)->get();

        if ($value === '1') {
            foreach ($orders as $order) {
                $order->lat        = $order->lat1;
                $order->lng        = $order->lng1;
                $order->time       = $order->time1;
                $order->yaddress   = $order->yaddress1;
                $order->address    = $order->address1;
                $order->courier_id = $order->courier1_id;
                $order->save();
            }
        }else {
            foreach ($orders as $order) {
                $order->lat        = $order->lat2;
                $order->lng        = $order->lng2;
                $order->time       = $order->time2;
                $order->yaddress   = $order->yaddress2;
                $order->address    = $order->address2;
                $order->courier_id = $order->courier2_id;
                $order->save();
            }
        }

        return redirect()->route('admin.home')->with('status', 'Переключен на ' . $week->name);
    }

    public function showWhatsapp()
    {
        $text = Week::find(1)->whatsapp_message;

        return view('admin.whatsapp', ['text' => $text]);
    }

    public function updateWhatsappText(Request $request)
    {
        $text = $request['text'];
        $week = Week::find(1);

        $week->whatsapp_message = $text;
        $week->save();

        return redirect()->route('admin.home')->with('status', 'Сообщение изменено!');
    }

    public function showDays()
    {
        $orders = Order::where('active', true)->orderBy('name')->get();

        return view('admin.days', ['orders' => $orders]);
    }

    public function incrementDay()
    {

    }

    public function showShift()
    {

    }

}
