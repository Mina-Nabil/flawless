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
                    <input type=hidden name=id value="{{(isset($item)) ? $item->id : ''}}">

                    <div class="form-group">
                        <label>Name*</label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon11"><i class="ti-type"></i></span>
                            </div>
                            <input type="text" class="form-control" placeholder="Item name" name=name aria-label="Typename" aria-describedby="basic-addon11"
                                value="{{ (isset($item)) ? $item->STCK_NAME : old('name')}}" required>
                        </div>
                        <small class="text-danger">{{$errors->first('name')}}</small>
                    </div>

                    <div class="form-group">
                        <label>Unit*</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="Item unit" name=unit aria-label="Unit" aria-describedby="basic-addon11"
                                value="{{ (isset($item)) ? $item->STCK_UNIT : old('unit')}}" required>
                        </div>
                        <small class="text-danger">{{$errors->first('unit')}}</small>
                    </div>

                    <div class="form-group">
                        <label>Type</label>
                        <select class="select2 form-control  col-md-12 mb-3" style="width:100%" name=type_id>
                            @foreach($types as $type)
                            <option value="{{$type->id}}" @if($type->STCK_STTP_ID == $type->id) selected @endif> {{$type->STTP_NAME}}</option>
                            @endforeach
                        </select>
                        <small class="text-danger">{{$errors->first('type_id')}}</small>
                    </div>

                    <div class="form-group">
                        <label>Usage</label>
                        <div class="bt-switch">
                            <div>
                                <input type="checkbox" data-size="large" data-on-color="info" data-off-color="success" data-on-text="Session" data-off-text="Stock" name="is_session" id="is_session" @if(isset($item) &&  $item->STCK_SSHN) checked @endif>
                            </div>
                        </div>
                        <small class="text-danger">{{$errors->first('is_session')}}</small>

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