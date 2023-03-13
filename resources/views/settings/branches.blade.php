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
                    <input type=hidden name=id value="{{(isset($branch)) ? $branch->id : ''}}">

                    <div class="form-group">
                        <label>Name*</label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon11"><i class="ti-branch"></i></span>
                            </div>
                            <input type="text" class="form-control" placeholder="Branchname" name=name aria-label="Branchname" aria-describedby="basic-addon11"
                                value="{{ (isset($branch)) ? $branch->BRCH_NAME : old('name')}}" required>
                        </div>
                        <small class="text-danger">{{$errors->first('name')}}</small>
                    </div>

                    <div class="form-group">
                        <label>Location*</label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon11"><i class="ti-branch"></i></span>
                            </div>
                            <input type="text" class="form-control" placeholder="Branch location" name=location aria-label="Branchname" aria-describedby="basic-addon11"
                                value="{{ (isset($branch)) ? $branch->BRCH_LOCT : old('location')}}" required>
                        </div>
                        <small class="text-danger">{{$errors->first('location')}}</small>
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