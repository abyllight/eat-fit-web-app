<?php

namespace App\Http\Controllers;

use App\Models\Order;
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

    public function sendWhatsapp(int $order_id)
    {
        dd($order_id);
    }
}
