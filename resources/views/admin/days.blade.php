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
        <form class="form-inline" method="GET" action="{{ route('admin.list.export') }}">
            @csrf
            <button type="submit" class="btn btn-success">
                +1 день
            </button>
        </form>
    </div>

    <div class="row">
        <table class="table">
            <thead class="thead-light">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Имя</th>
                    <th scope="col">День</th>
                    <th scope="col">День +1</th>
                    <th scope="col">Статус</th>
                </tr>
            </thead>
            <tbody id="table">
            @foreach($orders as $key => $order)
                <tr>
                    <th scope="row">{{ $key + 1 }}</th>
                    <td>{{ $order->name }}</td>
                    <td>{{ $order->day }} / {{ $order->course }}</td>
                    <td>{{ $order->day_old }}</td>
                    <td>{{ $order->interval }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
