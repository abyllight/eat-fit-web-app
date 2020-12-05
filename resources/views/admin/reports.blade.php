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

    <div class="row mb-3 ml-2">
        <input data-date-format="dd/mm/yyyy" id="datepicker" class="mr-3">
        <form class="form-inline" method="GET" action="{{ route('admin.reports.export') }}">
            @csrf
            <button type="submit" class="btn btn-success">
                Отчет Excel
            </button>
        </form>
    </div>

    @if($reports->count() > 0)
        <div class="row">
        @foreach($reports as $key => $report)
            <div>
                <h3 class="mt-3">{{ $report[0]->user->first_name }} - {{ count($report) }}</h3>
                <table class="table">
                    <thead class="thead-light">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Интервал</th>
                            <th scope="col">Телефон</th>
                            <th scope="col">Имя</th>
                            <th scope="col">Адрес</th>
                            <th scope="col">Оплата</th>
                            <th scope="col">Тип</th>
                            <th scope="col">Статус</th>
                            <th scope="col">Факт.время</th>
                            <th scope="col">Whatsapp</th>
                        </tr>
                    </thead>

                    @foreach($report as $v => $value)
                        <tbody class="border-b border-dark">
                            <tr>
                                <th scope="row">{{ $v+1 }}</th>
                                <td>{{ $value->order->time }}</td>
                                <td>{{ $value->order->phone }}</td>
                                <td>{{ $value->order->name }}</td>
                                <td>{{ $value->order->yaddress }}</td>
                                <td>{{ $value->payment }}</td>
                                <td>{{ $value->payment_method }}</td>
                                <td>asd</td>
                                <td>{{ $value->reported_at }}</td>
                                <td>{{ $value->delivered_at }}</td>
                            </tr>
                            <tr>
                                <td colspan="10">{{ $value->comment }}</td>
                            </tr>
                        </tbody>
                    @endforeach
                </table>
            </div>
        @endforeach
    </div>
    @else
        <div class="row">
            На эту дату отчетов не было
        </div>
    @endif
</div>
@endsection

@section('js_bottom')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js" integrity="sha512-T/tUfKSV1bihCnd+MxKD0Hm1uBBroVYBOYSk1knyvQ9VyZJpc/ALb4P0r6ubwVPSGB2GvjeoMAJJImBG12TiaQ==" crossorigin="anonymous"></script>
    <script type="text/javascript">

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#datepicker').datepicker({
            weekStart: 1,
            daysOfWeekHighlighted: "0",
            autoclose: true,
            todayHighlight: true,
            clearBtn: true,
            format: 'yyyy-mm-dd',
            endDate: '2020-12-04'
        });


        $('#datepicker').change(function(){

            let date = $(this).val();
            console.log(date)

            $.ajax({
                type:'POST',
                url:'{{ route('admin.reports.filter') }}',
                data:{
                    date: date
                },
                success:function(data){
                    /*location.reload()*/
                    console.log(data)
                },
                error: function(data){
                    var errors = data.responseJSON;
                    console.log(errors);
                }
            });
        });
    </script>
@endsection
