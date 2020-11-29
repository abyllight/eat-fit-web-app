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

    <div class="row">
        <div class="col-md-8">
            <div class="form-group row">

                <label for="time" class="col-sm-1 col-form-label mr-4">Интервал</label>

                <div>
                    <select id="time" name="time[]" class="form-control" multiple>
                        @foreach($intervals as $key => $value)
                            <option>{{ $value->interval }}</option>
                        @endforeach
                    </select>
                </div>

                <label for="c1" class="col-sm-1 col-form-label ml-3 mr-4">Курьеры</label>

                <div>
                    <select id="c1" name="group1" class="form-control">
                        <option value="0" selected>Не выбрано</option>
                        @foreach($couriers as $courier)
                            <option value="{{ $courier->id }}">{{ $courier->first_name }}</option>
                        @endforeach
                    </select>
                </div>


                <h3 class="ml-5" id="total">0</h3>
            </div>
            <div id="map" style="width: 100%; height: 600px">

            </div>
        </div>

        <div class="col-md-4">
            <h4><span class="badge badge-warning">{{$week->name}}</span></h4>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">ID</label>
                <label class="col-md-9 col-form-label" id="id"></label>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Имя</label>
                <label class="col-md-9 col-form-label" id="name"></label>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Телефон</label>
                <label class="col-md-9 col-form-label" id="phone"></label>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Статус</label>
                <label class="col-md-9 col-form-label" id="status"></label>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Я-Адрес</label>
                <label class="col-md-9 col-form-label" id="address"></label>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Время</label>
                <label class="col-md-9 col-form-label" id="interval"></label>
            </div>
            <div class="form-group row">
                <select id="c2" name="group2" class="form-control col-md-6 mr-2">
                    <option value="0" selected>Не выбрано</option>
                    @foreach($couriers as $key => $value)
                        <option value="{{ $value->id }}">{{ $value->first_name }}</option>
                    @endforeach
                </select>
                <button class="btn btn-primary col-md-4" type="submit" id="submit">Submit</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js_bottom')
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="{{ asset('js/bootstrap-multiselect.js') }}"></script>
    <script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>

    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function () {

            $('#time').multiselect({
                buttonWidth: '200px',
                includeSelectAllOption: true,
                maxHeight: 400
            });

            let colors = [
                'islands#blackStretchyIcon',
                'islands#redStretchyIcon',
                'islands#darkOrangeStretchyIcon',
                'islands#yellowStretchyIcon',
                'islands#darkGreenStretchyIcon',
                'islands#lightBlueStretchyIcon',
                'islands#darkBlueStretchyIcon',
                'islands#violetStretchyIcon',
                'islands#blackStretchyIcon',
            ];

            ymaps.ready(function () {

                let map = new ymaps.Map("map", {
                    center: [51.1801, 71.44598],
                    zoom: 11
                });

                let myClusterer = new ymaps.Clusterer({
                    clusterDisableClickZoom: true,
                    groupByCoordinates: true,
                    clusterIconLayout: 'default#pieChart',
                    zIndex: 10
                });

                function createPlacemark(value){

                    let placemark = new ymaps.Placemark([value.lat, value.lng],{
                        balloonContentHeader: value.name,
                        balloonContentBody: value.time + '<br/>' +
                            value.phone + '<br/>' +
                            value.tag + '<br/>' +
                            value.yaddress + '<br/>' +
                            value.created_at + '<br/>' +
                            value.yaddress_old + '<br/>' +
                            value.time_old,
                        balloonContentFooter: value.id + ' ' + value.courier_id,
                        iconContent: value.courier_name,
                        hintContent: value.name + '<br/' + value.tag + '<br/>' + value.time
                    },{
                        preset: colors[value.interval],
                        zIndex: value.courier_id ? 1 : 1000
                    });

                    placemark.events.add('click', function(e){
                        $('#id').html(value.id);
                        $('#name').html(value.name);
                        $('#phone').html(value.phone);
                        $("#status").html(value.status);
                        $("#address").html(
                            value.yaddress  +
                            '<p class="text-danger">' + value.yaddress_old + '</p>'
                        );
                        $("#interval").html(
                            value.time +
                            '<p class="text-danger">' + value.time_old + '</p>'
                        );
                        $("#c2").val(value.courier_id ?? '0');
                    });

                    return placemark;
                }

                function displayPlacemarks(data){

                    myClusterer.removeAll();

                    $.each(data, function(index,value){
                        myClusterer.add(createPlacemark(value));
                    });

                    myClusterer.events.add('click', function(e) {

                        let target       = e.get('target');
                        let activeObject = target.state.get('activeObject');

                        if(target.getGeoObjects) {

                            target.state.events.add('change',function() {

                                if (target.state.get('activeObject') !== activeObject) {

                                    activeObject = target.state.get('activeObject');
                                    let o_id   = activeObject.properties.get('balloonContentFooter').split(' ');
                                    let c_body = activeObject.properties.get('balloonContentBody').split('<br/>');

                                    $("#id").html(o_id[0]);
                                    $("#name").html(activeObject.properties.get('balloonContentHeader'));
                                    $("#phone").html(c_body[1]);

                                    let today = new Date();
                                    let d     = c_body[4].split('-');

                                    if(d[1] === today.getMonth() + 1 && d[2] === today.getDate()) {
                                        $("#status").html('<span class="badge badge-success">Новый</span>');
                                    }else if(c_body[5] !== '' || c_body[6] !== ''){
                                        $("#status").html('<span class="badge badge-danger">Изменен</span>');
                                    }else{
                                        $("#status").html('<span class="badge badge-secondary">Не изменен</span>');
                                    }
                                    if(c_body[5] !== ''){
                                        $("#address").html('<p class="text-danger">'+c_body[5]+'</p><p class="text-success">'+c_body[3]+'</p>');
                                    }else{
                                        $("#address").html(c_body[3]);
                                    }
                                    if(c_body[6] !== ''){
                                        $("#interval").html('<p class="text-danger">'+c_body[6]+'</p><p class="text-success">'+c_body[0]+'</p>');
                                    }else{
                                        $("#interval").html(c_body[0]);
                                    }
                                    if(o_id[1] !== 'null'){
                                        $("#c2").val(o_id[1]);
                                    }else{
                                        $("#c2").val('0');
                                    }
                                }
                            });
                        }
                    });
                    map.geoObjects.add(myClusterer);
                    map.events.add('boundschange',function(){

                        let cls   = myClusterer.getClusters();
                        let count = 0;

                        $.each(cls, function(index,value) {
                            let cpm = value.properties.get('geoObjects');
                            for (var i = 0; i < cpm.length; i++) {
                                let cpmcour = cpm[i].properties.get('balloonContentFooter').split(" ");

                                if(cpmcour[1] !== 'null'){
                                    count++;
                                }
                            }

                            value.properties.set('iconContent',count + '/' + cpm.length);
                            count = 0;
                        });
                    });
                }

                $('#time').change(function(){
                    $.ajax({
                        type: 'POST',
                        url: '{{ route('admin.map.filter') }}',
                        data: {
                            time: $('#time').val(),
                            c1: $('#c1').val()
                        },
                        success: function(data){
                            $("#total").html(data.length);
                            displayPlacemarks(data)
                        },
                        error: function(data){
                            var errors = data.responseJSON;
                            console.log(errors);
                        }
                    });
                });

                $('#c1').on('change', function() {
                    $.ajax({
                        url: '{{ route('admin.map.filter') }}',
                        type: 'POST',
                        data: {
                            c1: $('#c1').val(),
                            time: $('#time').val()
                        },
                        success: function(data){
                            $("#total").html(data.length);
                            displayPlacemarks(data)
                        },
                        error: function(data){
                            var errors = data.responseJSON;
                            console.log(errors);
                        }
                    });
                });

                $("#submit").click(function(){
                    let id = document.getElementById('id').innerHTML;

                    if (id === '') {
                        alert('Выберите метку');
                        return;
                    }

                    $.ajax({
                        type: 'POST',
                        url: '{{ route('admin.map.filter') }}',
                        data: {
                            o_id: id,
                            c_id: $('#c2').val(),
                            c1: $('#c1').val(),
                            time: $('#time').val()
                        },
                        success: function(data) {
                            displayPlacemarks(data);
                        },
                        error: function(data) {
                            var errors = data.responseJSON;
                            console.log(errors);
                        }
                    });
                });
            });
        });

    </script>
@endsection
