@extends('layouts.app')

@section('header')

<link href="{{asset('assets/node_modules/ion-rangeslider/css/ion.rangeSlider.css')}}" rel="stylesheet">
<link href="{{asset('assets/node_modules/ion-rangeslider/css/ion.rangeSlider.skinModern.css')}}" rel="stylesheet">
@endsection

@section('content')

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">{{$formTitle}}</h4>
                <h6 class="card-subtitle">{{$formSubtitle}}</h6>
                <form class="form pt-3" method="post">
                    @csrf

                    <div class=row>
                        <div class="col-6 form-group">
                            <label>From*</label>
                            <div class="input-group">
                                <input type="date" class="form-control" name=from required>
                            </div>
                        </div>


                        <div class="col-6 form-group">
                            <label>To*</label>
                            <div class="input-group">
                                <input type="date" class="form-control" name=to value="{{date('Y-m-d')}}" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Session State</label>
                        <div class="input-group mb-3">
                            <select name=state class="select form-control custom-select" style="width: 100%; height:36px;" required>
                                <option value="All" selected>All</option>
                                <option value="New">New</option>
                                <option value="Pending Payment">Pending Payment</option>
                                <option value="Done">Done</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>


                    <div class="form-group">
                        <label>Patient</label>
                        <select class="select2 form-control  col-md-12 mb-3" style="width:100%" name=patient>
                            <option value="0">All</option>
                            @foreach($patients as $patient)
                            <option value="{{$patient->id}}"> {{$patient->PTNT_NAME}} ({{$patient->PTNT_MOBN}}) </option>
                            @endforeach
                        </select>
                    </div>

                    <div class=row>
                        <div class="col-9 form-group">
                            <label>Doctor</label>
                            <select class="select2 form-control  col-md-12 mb-3" style="width:100%" name=doctor>
                                <option value="0">All</option>
                                @foreach($doctors as $doctor)
                                <option value="{{$doctor->id}}"> {{$doctor->DASH_USNM}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-3 form-group">
                            <label>Commission?</label>
                            <div class="input-group mb-3">
                                <select name=type class="select form-control custom-select">
                                    <option value="0">All</option>
                                    <option value="true">Comission Only</option>
                                    <option value="false">No Comission</option>
                                </select>
                            </div>
                        </div>
                    </div>


                    <div class=row>
                        <div class="form-group col-6">
                            <label>Created By</label>
                            <select class="select2 form-control  col-md-12 mb-3" style="width:100%" name=opener>
                                <option value="0">All</option>
                                @foreach($admins as $admin)
                                <option value="{{$admin->id}}"> {{$admin->DASH_USNM}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-6">
                            <label>Money Received By</label>
                            <select class="select2 form-control  col-md-12 mb-3" style="width:100%" name=moneyMan>
                                <option value="0">All</option>
                                @foreach($admins as $admin)
                                <option value="{{$admin->id}}"> {{$admin->DASH_USNM}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-12 mb-5">
                        <label>Total Range</label>
                        <div id="range_02" name=totalRange></div>
                        <input value="{{$sessionsMin}}" id=totalBegin name=totalBegin type="hidden">
                        <input value="{{$sessionsMax}}" id=totalEnd name=totalEnd type="hidden">
                    </div>

                    <button type="submit" class="btn btn-success mr-2">Submit</button>

                </form>
            </div>
        </div>
    </div>

</div>

@endsection

@section('js_content')
<script src="{{asset('assets/node_modules/ion-rangeslider/js/ion-rangeSlider/ion.rangeSlider.min.js')}}"></script>
<script>
    $("#range_02").ionRangeSlider({
    type: "double",
    min: {{$sessionsMin}},
    max: {{$sessionsMax}},
    from: {{$sessionsMin}},
    to: {{$sessionsMax}},
    onChange: function (data) {
            $('#totalBegin').val(data.from)
            $('#totalEnd').val(data.to)
        }
});
</script>
@endsection