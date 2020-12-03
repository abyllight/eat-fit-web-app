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
        dd('asd');
        $amo_id    = $request['amo'];
        $now       = strtotime('now');
        $subdomain = env('AMO_SUBDOMAIN', '');
        $link      = 'https://' . $subdomain . '.amocrm.ru/private/api/v2/json/leads/set';

        /*$leads['request']['leads']['update'] = array(
            array(
                'id' => $amo_id,
                'last_modified' => $now,
                'status_id'=>142,
                'price'=>602041,
                'responsible_user_id'=>109999,
                'custom_fields'=>array(
                    array(
                        'id'=>427493, # id поля типа numeric
                        'values'=>array(
                            array(
                                'value'=>65535 # сюда передается только числовое значение (float, int). Значения float передаются с точкой, например: 27.3
                            )
                        )
                    ),
                    array(
                        'id'=>427494, # id поля типа checkbox
                        'values'=>array(
                            array(
                                'value'=>1 # допустимые значения 1 или 0
                            )
                        )
                    ),
                    array(
                        'id'=>427495, #id поля типа select
                        'values'=>array(
                            array(
                                'value'=>1240662 # одно из enum значений
                            )
                        )
                    )
                )
            ),
            array(
                'id'=>3698754,
                'name'=>'Keep Calm',
                //'date_create'=>1298904164, //optional
                'last_modified'=>1375110129,
                'status_id'=>7087607,
                'price'=>1008200,
                'responsible_user_id'=>109999,
                'custom_fields'=>array(
                    array(
                        #Нестандартное дополнительное поле типа "мультисписок", которое мы создали
                        'id'=>426106,
                        'values'=>array(
                            1237755,
                            1237757
                        )
                    )
                )
            )
        );*/

        $user_id  = Auth::user()->id;
        $order_id = $request['order'];

        $report  = Report::where('order_id',  $order_id)
            ->where('courier_id', $user_id)
            ->whereDate('created_at', Carbon::today()->toDateString())
            ->first();

        $created_at  = $report ? $report->created_at : Carbon::now();

        Report::updateOrCreate(
            [
                'order_id'   => $order_id,
                'courier_id' => $user_id,
                'created_at' => $created_at
            ],
            [
                'delivered_at' => Carbon::now()
            ]
        );

        return redirect()->to('home/#o' . $order_id);
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

        return redirect()->to('home/#o' . $id);
    }
}
