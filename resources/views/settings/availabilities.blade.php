@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-7">
        <x-datatable id="myTable" :title="$title" :subtitle="$subTitle" :cols="$cols" :items="$items" :atts="$atts" />
    </div>

    <div class="col-lg-5">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">{{ $formTitle }}</h4>
                <form class="form pt-3" method="post" action="{{ url($formURL) }}" enctype="multipart/form-data">
                    @csrf
                    <input type=hidden name=id value="{{(isset($availiabity)) ? $availiabity->id : ''}}">

                    <div class="form-group">
                        <label for="input-file-now-custom-1">Day*</label>
                        <div class="input-group mb-3">
                            <select name=day class="form-control">
                                @foreach ($days as $id => $day)
                                <option value={{$id}} {{ (isset($availiabity) && $availiabity->DVAC_DAY_OF_WEEK==$id) ? "selected" : "" }}>{{$day}}</option>
                                @endforeach
                            </select>
                        </div>
                        <small class="text-danger">{{$errors->first('day')}}</small>
                    </div>

                    <div class="form-group">
                        <label for="input-file-now-custom-1">Shift*</label>
                        <div class="input-group mb-3">
                            <select name=shift class="form-control">
                                @foreach ($shifts as $shift)
                                <option value="{{$shift}}" {{ (isset($availiabity) && $availiabity->DCAV_SHFT==$id) ? "selected" : "" }}>{{$shift}}</option>
                                @endforeach
                            </select>
                        </div>
                        <small class="text-danger">{{$errors->first('shift')}}</small>
                    </div>

                    <div class="form-group">
                        <label for="input-file-now-custom-1">Doctor*</label>
                        <div class="input-group mb-3">
                            <select name=doctor_id class="form-control select2 custom-select">
                                @foreach ($doctors as $doc)
                                <option value={{$doc->id}} {{ (isset($availiabity) && $availiabity->DCAV_DASH_ID==$doc->id) ? "selected" : "" }}>{{$doc->DASH_USNM}}</option>
                                @endforeach
                            </select>
                        </div>
                        <small class="text-danger">{{$errors->first('doctor_id')}}</small>
                    </div>
                    <div class="form-group">
                        <label for="input-file-now-custom-1">Branch*</label>
                        <div class="input-group mb-3">
                            <select name=branch_id class="form-control">
                                @foreach ($branches as $branch)
                                <option value={{$branch->id}} {{ (isset($availiabity) && $availiabity->DVAC_BRCH_ID==$branch->id) ? "selected" : "" }}>{{$branch->BRCH_NAME}}</option>
                                @endforeach
                            </select>
                        </div>
                        <small class="text-danger">{{$errors->first('branch_id')}}</small>
                    </div>

                    <div class="form-group">
                        <label>Note</label>
                        <div class="input-group mb-3">
                            <textarea class="form-control" rows="2" name="desc">{{$availiabity->DVAC_DESC ?? ''}}</textarea>
                        </div>
                        <small class="text-danger">{{$errors->first('desc')}}</small>
                    </div>

                    <button type="submit" class="btn btn-success mr-2">Submit</button>
                    @if($isCancel)
                    <a href="{{url($homeURL) }}" class="btn btn-dark">Cancel</a>
                    @endif
                </form>

            </div>
        </div>
    </div>
</div>
@endsection