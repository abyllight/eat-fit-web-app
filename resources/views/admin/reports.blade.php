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
        <input data-date-format="dd/mm/yyyy" id="datepicker" class="mr-3">
        <form class="form-inline" method="GET" action="{{ route('admin.reports.export') }}">
            @csrf
            <button type="submit" class="btn btn-success">
                Отчет Excel
            </button>
        </form>
    </div>

    <div class="row" id="table">

    </div>

    <div class="row" id="empty">

    </div>

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

        function displayReport(data) {

            if(data.length === 0) {
                $('#empty').html('На эту дату отчетов не было');

                return;
            }

            let html   = '';
            let name   = '';
            let header = '<table class="table table-bordered">'
                + '<thead class="thead-light"><tr><th scope="col">#</th><th scope="col">Интервал</th><th scope="col">Телефон</th><th scope="col">Имя</th><th scope="col">Адрес</th><th scope="col">Оплата</th><th scope="col">Тип</th><th scope="col">Статус</th><th scope="col">Отчет</th><th scope="col">Whatsapp</th></tr></thead>';
            let tbody  = '';

            $.each(data ,function (index, value) {

                let courier_name = value[0].user ? value[0].user.first_name : ''

                name += '<h3 class="mb-3">' + courier_name + ' - [' + value.length + ']</h3>';

                $.each(value, function (i, v) {

                    tbody += '<tbody>'
                            + '<tr>'
                                + '<td>' + (i + 1) + '</td>'
                                + '<td>' + v.order.time + '</td>'
                                + '<td>' + v.order.phone + '</td>'
                                + '<td>' + v.order.name + '</td>'
                                + '<td>' + v.order.yaddress + '</td>'
                                + '<td>' + v.payment + '</td>'
                                + '<td>' + v.payment_method + '</td>'
                                + v.status
                                + '<td>' + v.reported + '</td>'
                                + '<td>' + v.delivered + '</td>'
                            + '</tr>'
                            + '<tr>'
                                + '<td colspan="10">' + v.comment + '</td>'
                            + '</tr>'
                            + '</tbody>';
                })

                html += name + header + tbody + '</table>';

                name  = '';
                tbody = '';
            });

            return html;
        }

        $(document).ready(function () {

            var last_date = {!! json_encode($date) !!};
            var reports   = {!! json_encode($reports) !!};

            $('#table').html(displayReport(reports));

            $('#datepicker').datepicker({
                weekStart: 1,
                daysOfWeekHighlighted: "0",
                autoclose: true,
                todayHighlight: true,
                clearBtn: true,
                format: 'yyyy-mm-dd',
                endDate: last_date,
                defaultViewDate: last_date
            });

            $('#datepicker').datepicker("setDate", last_date);


            $('#datepicker').change(function(){

                let date = $(this).val();

                $.ajax({
                    type:'POST',
                    url:'{{ route('admin.reports.filter') }}',
                    data:{
                        date: date
                    },
                    success:function(data){
                        $('#empty').html('');
                        $('#table').html('');
                        $('#table').html(displayReport(data));
                    },
                    error: function(data){
                        var errors = data.responseJSON;
                        console.log(errors);
                    }
                });
            });
        });



    </script>
@endsection
