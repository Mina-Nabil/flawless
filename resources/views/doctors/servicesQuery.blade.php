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
                    @if(Auth::user()->isDoctor())
                    <input type="hidden" name="doctorID" value="{{Auth::id()}}">
                    @else
                    <div class="col-12 form-group">
                        <label>Doctor</label>
                        <select class="select2 form-control  col-md-12 mb-3" style="width:100%" name=doctorID required>
                            @foreach($doctors as $doctor)
                            <option value="{{$doctor->id}}"> {{$doctor->DASH_USNM}}</option>
                            @endforeach
                        </select>
                        <small class="text-danger">{{$errors->first('doctorID')}}</small>
                    </div>
                    @endif

                
                    <div class="form-group col-md-6 m-t-0">
                        <label>Location</label>
                        <select class="select2  select2-multiple form-control" multiple="multiple" name=device_ids[]>
                          <option value="-1" selected>All Devices</option>
                          <?php foreach ($devices as $device) { ?>
                            <option value="<?= $device['id'] ?>"><?= $device['DVIC_NAME'] ?></option>
                          <?php } ?>
                        </select>
                      </div>
                    <button type="submit" class="btn btn-success mr-2">Submit</button>

                </form>
            </div>
        </div>
    </div>

</div>

@endsection