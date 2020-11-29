<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Models\Week;
use Illuminate\Http\Request;

class MapController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index()
    {

        $intervals = Order::where('active',true)
                        ->whereNotNull('lat')
                        ->orderBy('interval')
                        ->distinct()
                        ->get(['interval']);

        $couriers = User::where('is_admin', false)->get();
        $week     = Week::find(1);

        return view('admin.map', ['intervals' => $intervals, 'couriers' => $couriers, 'week' => $week]);
    }

    public function filter(Request $request)
    {
        if (!$request->has('time')) {
            return response()->json([]);
        }

        if($request->has('o_id') && $request->has('c_id')){
            $this->setCourier($request['o_id'], $request['c_id']);
        }

        $intervals = $request['time'];

        $orders = Order::where('active', true)
                        ->whereNotNull('lat')
                        ->whereNotNull('lng')
                        ->whereIn('interval', $intervals);

        if ($request['c1'] != '0') {
            $orders = $orders->where('courier_id', $request['c1']);
        }

        $orders = $orders->with('user')->get();

        $content = [];

        foreach ($orders as $order) {

            $content[] = [
                'id'           => $order->id,
                'lat'          => $order->lat,
                'lng'          => $order->lng,
                'name'         => $order->name,
                'time'         => $order->time,
                'time_old'     => $order->time_old ?? '',
                'phone'        => $order->phone,
                'yaddress'     => $order->yaddress,
                'yaddress_old' => $order->yaddress_old ?? '',
                'status'       => $order->getStatus(),
                'courier_id'   => $order->courier_id,
                'courier_name' => $order->user ? $order->user->first_name : '',
                'interval'     => $order->interval,
                'tag'          => $order->getTag(),
                'created_at'   => $order->created_at->format('Y-m-d')
            ];
        }

        return response()->json($content);
    }

    public function setCourier(int $o_id, int $c_id)
    {
        $is_weekend = Week::find(1)->is_weekend;
        $max        = Order::max('position') ?? 0;
        $order      = Order::find($o_id);

        if(!$order->courier_id){
            $order->position   = ++$max;
        }

        if ($c_id === 0) {
            $is_weekend ? $order->courier2_id = null : $order->courier1_id = null;
            $order->courier_id  = null;
            $order->position    = null;
        }

        $order->courier_id = $c_id;
        $is_weekend ? $order->courier2_id = $c_id : $order->courier1_id = $c_id;
        $order->save();
    }
}
