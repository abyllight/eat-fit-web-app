<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CourierController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index()
    {
        $couriers = User::where('is_admin', 0)
            ->orderBy('first_name', 'asc')
            ->get();

        return view('admin.couriers', ['couriers' => $couriers]);
    }

    public function show($id)
    {
        $courier = User::find($id);

        return view('admin.courier-update', ['courier' => $courier]);
    }

    public function create()
    {
        return view('admin.courier-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name'       => 'required|string|max:255',
            'email'            => 'required|email|unique:users,email',
            'phone'            => 'required|unique:users,phone',
            'password'         => 'required|min:6|confirmed',
        ]);

        $courier=new User;

        $courier->first_name = $request['first_name'];
        $courier->last_name = $request['last_name'];
        $courier->phone = $request['phone'];
        $courier->car_number = $request['car_number'];
        $courier->email = $request['email'];
        $courier->is_admin = false;
        $courier->password = Hash::make($request['password']);
        $courier->save();

        return redirect()->route('admin.couriers')->with('status', 'Курьер добавлен!');
    }

    public function update(Request $request, $id)
    {
        $courier = User::find($id);

        $request->validate([
            'first_name'       => 'required|string|max:255',
            'email'            => 'required|email|unique:users,email,' . $id,
            'phone'            => 'required|unique:users,phone,' . $id,
        ]);

        $courier->first_name = $request['first_name'];
        $courier->last_name = $request['last_name'];
        $courier->phone = $request['phone'];
        $courier->car_number = $request['car_number'];
        $courier->email = $request['email'];
        $courier->save();

        return redirect()->route('admin.couriers')->with('status', 'Данные курьера изменены!');
    }

    public function updatePass(Request $request, $id)
    {
        $request->validate([
            'password'         => 'required|min:6|confirmed',
        ]);

        $courier = User::find($id);
        $courier->password = Hash::make($request['password']);

        $courier->save();

        return redirect()->route('admin.couriers')->with('status', 'Пароль изменен!');
    }

    public function delete($id)
    {
        $courier = User::find($id);

        $courier->delete();

        return redirect()->route('admin.couriers')->with('status', 'Курьер удален!');
    }
}
