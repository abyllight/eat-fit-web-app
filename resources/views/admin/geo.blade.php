@extends('layouts.app')

@section('content')
<div class="container">
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
        <a class="btn btn-primary mr-3" href="{{ route('admin.geocode') }}" role="button">
            Геокодировать <span class="badge badge-light"> {{ count($orders) }} </span>
        </a>

        <a class="btn btn-primary" href="{{ route('admin.interval') }}" role="button">
            Авто Интервал
        </a>
    </div>

    <div class="row">
        <table class="table">
            <thead class="thead-light">
            <tr>
                <th scope="col">#</th>
                <th scope="col">Имя</th>
                <th scope="col">Яндекс</th>
                <th scope="col">Статус</th>
                <th scope="col">Время</th>
                <th scope="col">Интервал</th>
            </tr>
            </thead>
            <tbody id="table">
            @foreach($orders as $key => $order)
                <tr>
                    <th scope="row">{{ $key + 1 }}</th>
                    <td>{{ $order->name }}</td>
                    <td>{{ $order->yaddress }}</td>
                    <td>
                        @if($order->lat)
                            <span class="badge badge-success">geo</span>
                        @else
                            <span class="badge badge-danger">geo</span>
                        @endif
                    </td>
                    <td>{{ $order->time }}</td>
                    <td>{{ $order->interval }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
