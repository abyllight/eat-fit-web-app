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
    <div class="row justify-content-center">
        <div class="col-sm-12 col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        <strong>Отчет по {{$order->name}}</strong>
                        {!!  $order->getColourTag() !!}
                    </h5>

                    <form method="POST" action="{{ route('report.create', $order->id) }}">
                        @csrf
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="group1" value="1" checked>
                            <label class="form-check-label">Доставлено</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="group1" value="2">
                            <label class="form-check-label">Не доставлено</label>
                        </div>

                        <textarea class="form-control my-4" name="comment" rows="5" placeholder="Заметки/Комментарии"></textarea>

                        <div id="forma" class="mb-4">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" checked type="radio" name="m" id="bez" value="Без оплаты">
                                <label class="form-check-label" for="inlineRadio3">Без оплаты</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="m" id="nal" value="Наличный расчет">
                                <label class="form-check-label" for="inlineRadio1">Наличный расчет</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="m" id="pos" value="POS-терминал">
                                <label class="form-check-label" for="inlineRadio2">POS-терминал</label>
                            </div>
                            <input class="form-control mt-4" type="text" placeholder="Сумма оплаты" id="sum" name="summa">
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">Отправить</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js_bottom')
    <script type="text/javascript">
        $(document).ready(function() {

            $('#sum').hide();

            $('input[name = "group1"]').on('change', function() {
                let checked = document.querySelector('input[name = "group1"]:checked').value;

                if(checked === '1'){
                    $('#forma').show();
                }else{
                    $('#forma').hide();
                }
            });

            $('input[name = "m"]').on('change', function() {

                let checked = document.querySelector('input[name = "m"]:checked').value;

                if(checked === 'Без оплаты'){
                    $('#sum').hide();
                }else{
                    $('#sum').show();
                }
            });
        });
    </script>
@endsection
