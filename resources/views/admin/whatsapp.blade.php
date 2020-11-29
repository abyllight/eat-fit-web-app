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

    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-6 text-center py-5">
            <form method="POST" action="{{ route('admin.whatsapp.update') }}">
                @csrf
                <div class="form-group">
                    <label for="exampleFormControlTextarea1">Whatsapp сообщение</label>
                    <textarea class="form-control" rows="10" name="text">{{ $text }}</textarea>
                    <button type="submit" class="btn btn-primary mt-3">
                        Сохранить
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
