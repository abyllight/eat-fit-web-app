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
    </div>

    <div class="row mb-3">
        <a class="btn btn-primary" href="{{ route('admin.courier.create') }}" role="button">Добавить курьера</a>
    </div>

    <div class="row">
        <h3 class="mb-3">Курьеры - {{ count($couriers) }}</h3>
        <table class="table">
            <thead class="thead-light">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Имя</th>
                    <th scope="col">Фамилия</th>
                    <th scope="col">Почта</th>
                    <th scope="col">Телефон</th>
                    <th scope="col">Номер машины</th>
                    <th scope="col">Действие</th>
                </tr>
            </thead>
            <tbody>
                @foreach($couriers as $key => $courier)
                    <tr>
                        <th scope="row">{{ $key + 1 }}</th>
                        <td>{{ $courier->first_name }}</td>
                        <td>{{ $courier->last_name }}</td>
                        <td>{{ $courier->email }}</td>
                        <td>{{ $courier->phone }}</td>
                        <td>{{ $courier->car_num }}</td>
                        <td>
                            <a href="{{ route('admin.courier.show', $courier->id) }}">ред.</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
