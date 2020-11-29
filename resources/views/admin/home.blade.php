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

    <div class="row">
        <div class="col-md-4 col-sm-12 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Скрипт +1</h5>
                    <a href="#" class="btn btn-primary">Go somewhere</a>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-sm-12 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Закрыть смену</h5>
                    <a href="#" class="btn btn-primary">Go somewhere</a>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-sm-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Переключатель
                        <span class="badge badge-warning">{{ $week->name }}</span>
                    </h5>
                    <form method="POST" action="{{ route('admin.home.week') }}" name="setWeek">
                        @csrf
                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                            <label class="btn btn-secondary {{ !$week->is_weekend ? 'active' : '' }}">
                                <input type="radio" name="options" id="week" {{ !$week->is_weekend ? 'checked' : '' }} value="1" onchange="setWeek.submit()"> Будни
                            </label>
                            <label class="btn btn-secondary {{ $week->is_weekend ? 'active' : '' }}">
                                <input type="radio" name="options" id="weekend" {{ $week->is_weekend ? 'checked' : '' }} value="2" onchange="setWeek.submit()"> Выходные
                            </label>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12 col-sm-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Whatsapp</h5>
                    <p class="card-text">{{ $week->whatsapp_message }}</p>
                    <a href="{{ route('admin.whatsapp') }}" class="btn btn-primary">Изменить</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
