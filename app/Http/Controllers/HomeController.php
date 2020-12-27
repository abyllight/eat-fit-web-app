<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Report;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $orders = Order::where('courier_id', Auth::user()->id)
                        ->where('active', true)
                        ->orderBy('position')
                        ->get();


        return view('home', ['orders' => $orders]);
    }

    public function setStatusToDelivered(Request $request)
    {
        dd('asd', $request['amo']);
        /*
         * Обновление сделки
         * Переместить в воронку Доставлено
         * и присвоить имя, телефон курьера
         * */

        $user      = Auth::user();
        $amo_id    = $request['amo'];
        $now       = strtotime('now');
        $subdomain = env('AMO_SUBDOMAIN', '');
        $link      = 'https://' . $subdomain . '.amocrm.ru/private/api/v2/json/leads/set';

        $leads['request']['leads']['update'] = array(
            array(
                'id'            => $amo_id,
                'last_modified' => $now,
                'status_id'     => 27248140, # Доставлено
                'custom_fields' => array(
                    array(
                        'id'     => 489499, # ТТМ телефон
                        'values' => array(
                            array(
                                'value' => $user->phone
                            )
                        )
                    ),
                    array(
                        'id'     => 489497, # ТТМ имя
                        'values' => array(
                            array(
                                'value' => $user->first_name
                            )
                        )
                    )
                )
            )
        );

        $amo = new AmoController();

        if ($amo->amo_auth()) {

            $amo->amo_curl($link, 'POST', $leads);

        }else {
            return redirect()->route('home')->with('error', 'Ошибка в авторизации АМО');
        }

        /*
         * Создать отчет
         * */

        $order_id = $request['order'];

        $report = Report::where('order_id',  $order_id)
            ->where('courier_id', $user->id)
            ->whereDate('created_at', Carbon::today()->toDateString())
            ->first();

        $created_at = $report ? $report->created_at : Carbon::now();

        Report::updateOrCreate(
            [
                'order_id'   => $order_id,
                'courier_id' => $user->id,
                'created_at' => $created_at
            ],
            [
                'delivered_at' => Carbon::now()
            ]
        );

        return redirect()->to('home/#o' . $order_id)->with('status', 'Сообщение отправлено!');
    }

    public function showReport($order_id)
    {
        $order = Order::find($order_id);

        return view('report', ['order' => $order]);
    }

    public function createReport($id, Request $request)
    {
        if (!$id) {
            return redirect()->back()->with('error', 'Не указан ID заказа');
        }

        $user_id = Auth::user()->id;

        $report  = Report::where('order_id',  $id)
                ->where('courier_id', $user_id)
                ->whereDate('created_at', Carbon::today()->toDateString())
                ->first();

        $created_at  = $report ? $report->created_at : Carbon::now();
        $reported_at = $report ? $report->reported_at : Carbon::now();

        Report::updateOrCreate(
            [
                'order_id'    => $id,
                'courier_id'  => $user_id,
                'created_at'  => $created_at,
                'reported_at' => $reported_at
            ],
            [
                'comment'        => $request['comment'],
                'payment'        => $request['summa'],
                'payment_method' => $request['m']
            ]
        );

        return redirect()->to('home/#o' . $id)->with('status', 'Отчет отправлен');
    }
}
