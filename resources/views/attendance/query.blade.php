@extends('layouts.app')

@section('content')

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">{{$formTitle}}</h4>
                <h6 class="card-subtitle">{{$formSubtitle}}</h6>
                <form class="form pt-3" method="post">
                    @csrf
                    
                    <div class="form-group">
                        <label>Attendance State*</label>
                        <div class="input-group mb-3">
                            <select name=type class="select form-control custom-select" style="width: 100%; height:36px;" required>
                                <option value="All" selected>All</option>
                                <option value="New">New</option>
                                <option value="Confirmed">Confirmed</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>


                    <div class="form-group">
                        <label>Doctor</label>
                        <select class="select2 form-control  col-md-12 mb-3" style="width:100%" name=doctor>
                            <option value="0">All</option>
                            @foreach($doctors as $doctor)
                            <option value="{{$doctor->id}}"> {{$doctor->DASH_USNM}} </option>
                            @endforeach
                        </select>
                    </div>


                    <div class=row>
                        <div class="col-6 form-group">
                            <label>From*</label>
                            <div class="input-group mb-3">
                                <input type="date" class="form-control" name=from required>
                            </div>
                        </div>


                        <div class="col-6 form-group">
                            <label>To*</label>
                            <div class="input-group mb-3">
                                <input type="date" class="form-control" name=to value="{{date('Y-m-d')}}" required>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success mr-2">Submit</button>

                </form>
            </div>
        </div>
    </div>

</div>

@endsection