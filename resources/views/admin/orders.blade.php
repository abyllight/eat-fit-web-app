@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('status') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
    </div>

    <div class="row mb-3">
        <a class="btn btn-primary" href="{{ route('admin.orders.amo') }}" role="button">
            AmoCRM <span class="badge badge-light"> {{ count($orders) }} </span>
        </a>
    </div>

    <div class="row">
        <table class="table">
            <thead class="thead-light">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Имя</th>
                    <th scope="col">Tag</th>
                    <th scope="col">Телефон</th>
                    <th scope="col">Whatsapp</th>
                    <th scope="col">Время</th>
                    <th scope="col">Время2</th>
                    <th scope="col">Яндекс</th>
                    <th scope="col">Яндекс2</th>
                    <th scope="col">Интервал</th>
                </tr>
            </thead>
            <tbody id="table">
                @foreach($orders as $key => $order)
                    <tr>
                        <th scope="row">{{ $key + 1 }}</th>
                        <td>{{ $order->name }}</td>
                        <td>
                            @if($order->tag === 'Select')
                                <p class="text-success">{{ $order->getTag() }}</p>
                            @else
                                {{ $order->getTag() }}
                            @endif
                        </td>
                        <td>{{ $order->phone }}</td>
                        <td>{{ $order->whatsapp }}</td>
                        <td>
                            {{ $order->time1 }}
                            <span class="badge badge-danger">{{ $order->time1_old }}</span>
                        </td>
                        <td>
                            {{ $order->time2 }}
                            <span class="badge badge-danger">{{ $order->time2_old }}</span>
                        </td>
                        <td>
                            <p>{{ $order->yaddress1 }}</p>
                            <small class="text-danger">{{ $order->yaddress1_old }}</small>
                        </td>
                        <td>
                            <p>{{ $order->yaddress2 }}</p>
                            <small class="text-danger">{{ $order->yaddress2_old }}</small>
                        </td>
                        <td>{{ $order->interval }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
