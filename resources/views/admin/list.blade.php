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
        <form class="form-inline" method="GET" action="{{ route('admin.list.export') }}">
            @csrf
            <button type="submit" class="btn btn-success">
                Список Excel <span class="badge badge-light"> {{ $a }}/{{ $b }} </span>
            </button>
        </form>
    </div>

    <div class="row">
        @foreach($orders as $key => $order)
            <div>
                <h3 class="mt-3">{{ $order[0]->user->first_name }} - {{ count($order) }}</h3>
                <table class="table table-bordered table-sortable">
                    <thead id="{{ $order[0]->courier_id }}">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Имя</th>
                        <th scope="col">Tag</th>
                        <th scope="col">Phone</th>
                        <th scope="col">Время</th>
                        <th scope="col">Адрес</th>
                        <th scope="col">Comment</th>
                    </tr>
                    </thead>

                    @foreach($order as $v => $value)
                        <tbody style="border: 1px solid grey">
                        <tr class="r" id="{{ $value->id }}">
                            <td id="{{ $value->id }}">{{ $v+1 }}</td>
                            @switch($value->tag)
                                @case('Select'))
                                    <td class="table-success"><strong>{{ $value->name }}</strong></td>
                                    <td class="table-success"><strong>{{ $value->getTag() }}</strong></td>
                                    @break
                                @case('Daily'))
                                    <td class="table-danger"><strong>{{ $value->name }}</strong></td>
                                    <td class="table-danger"><strong>{{ $value->getTag() }}</strong></td>
                                    @break
                                @case('Lite')
                                    <td class="table-warning"><strong>{{ $value->name }}</strong></td>
                                    <td class="table-warning"><strong>{{ $value->getTag() }}</strong></td>
                                    @break
                                @default
                                    <td class="table-warning"><strong>{{ $value->name }}</strong></td>
                                    <td class="table-warning"><strong>{{ $value->getTag() }}</strong></td>
                            @endswitch
                            <td>{{ $value->phone }}</td>
                            <td>{{ $value->time }}</td>
                            <td>{{ $value->address }}</td>
                            <td>{{ $value->addition }}</td>
                        </tr>
                        </tbody>
                    @endforeach
                </table>
            </div>
        @endforeach
    </div>
</div>
@endsection

@section('js_bottom')
    <script src="{{ asset('js/jquery-ui.min.js') }}"></script>
    <script type="text/javascript">

        let CSRF_TOKEN = '{{csrf_token()}}';

        $('.table-sortable').sortable({
            connectWith: '.table-sortable',
            opacity: 0.5,
            cursor: 'move',
            handle: 'tr',
            scroll: true,
            tolerance: 'pointer',
            stop: function(event, ui) {

                let dataArray = [];
                let table     = $('table tbody');

                table.find('.r').each(function (i) {
                    let id = $(this).find('td')[0]['id'];
                    dataArray.push(id);
                });

                let my_id     = ui['item'][0]['rows'][0]['id'];
                let parent_id = ui['item'][0]['offsetParent']['tHead']['id'];

                $.ajax({
                    type:'POST',
                    url:'{{ route('admin.list.update') }}',
                    data:{
                        my_id: my_id,
                        parent_id: parent_id,
                        ids: dataArray,
                        '_token': CSRF_TOKEN
                    },
                    success:function(data){
                        console.log(data);
                    },
                    error: function(data){
                        var errors = data.responseJSON;
                        console.log(errors);
                        alert(errors);
                    }
                });
            }
        });
    </script>
@endsection
