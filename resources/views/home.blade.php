@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        @foreach($orders as $key => $order)
            <div class="col-sm-12 col-md-4 col-lg-3 my-2">
                <div class="card" id="{{ $order->id }}">
                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="card-title"><strong>{{ $order->name }}</strong></h4>
                            <h4 class="card-title"><strong>{{ $key + 1 }}</strong></h4>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">

                            <h6 class="card-subtitle mb-2 text-muted">{{ $order->time }}</h6>

                            <h5 class="card-subtitle">{!! $order->getColourTag() !!}</h5>
                        </div>

                        <hr>

                        <p class="card-text lead">{{ $order->addition }}</p>
                        <p class="card-text">{{ $order->address }}</p>

                        <hr>

                        <a href="tel:{{ $order->phone }}" class="card-link">Телефон</a>
                        <a href="https://wa.me/{{ $order->whatsapp }}?text={{ urlencode('Здравствуйте! Это, ' . Auth::user()->first_name . ' - EAT&FIT') }}" class="card-link">Whatsapp</a>
                        <a href="dgis://2gis.ru/routeSearch/rsType/car/to/{{ $order->lng }},{{ $order->lat }}" class="card-link">2GIS</a>
                        <a href="yandexmaps://maps.yandex.ru/?rtext=~{{ $order->lat }},{{ $order->lng }}" class="card-link">Yndx</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
