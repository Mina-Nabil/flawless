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

                    @if(session('branch')==0)
                    <div class="col-12 form-group">
                        <label>Branch</label>
                        <select class="select2 form-control  col-md-12 mb-3" style="width:100%" name=branchID>
                            <option value=0> All</option>
                            @foreach($branches as $branch)
                            <option value="{{$branch->id}}"> {{$branch->BRCH_NAME}}</option>
                            @endforeach
                        </select>
                        <small class="text-danger">{{$errors->first('branchID')}}</small>
                    </div>
                    @elseif(session('branch')>0)
                    <input type="hidden" value="{{session('branch')}}" name=branchID />
                    @else
                    <p class="text-danger">Unable to find branch! Please select branch</p>
                    @endif
                    
                    <div class="form-group">
                        <label>Followup State*</label>
                        <div class="input-group mb-3">
                            <select name=type class="select form-control custom-select" style="width: 100%; height:36px;" required>
                                <option value="All" selected>All</option>
                                @foreach($states as $state)
                                <option value="{{$state}}">{{$state}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Patient</label>
                        <select class="select2 form-control  col-md-12 mb-3" style="width:100%" name=doctor>
                            <option value="0">All</option>
                            @foreach($patients as $patient)
                            <option value="{{$patient->id}}"> {{$patient->PTNT_NAME}} </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Caller</label>
                        <select class="select2 form-control  col-md-12 mb-3" style="width:100%" name=doctor>
                            <option value="0">All</option>
                            @foreach($admins as $admin)
                            <option value="{{$admin->id}}"> {{$admin->DASH_USNM}} </option>
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