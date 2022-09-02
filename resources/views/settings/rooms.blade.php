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
                    <input type=hidden name=id value="{{(isset($room)) ? $room->id : ''}}">
                    <div class="form-group">
                        <label for="input-file-now-custom-1">Branch*</label>
                        <div class="input-group mb-3">
                            <select name=branch_id class="form-control">
                                @foreach ($branches as $branch)
                                <option value={{$branch->id}} {{ (isset($room) && $room->ROOM_BRCH_ID==$branch->id) ? "selected" : "" }}>{{$branch->BRCH_NAME}}</option>
                                @endforeach
                            </select>
                        </div>
                        <small class="text-danger">{{$errors->first('branch_id')}}</small>
                    </div>
                    <div class="form-group">
                        <label>Name*</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="Room name" name=name value="{{ (isset($room)) ? $room->ROOM_NAME : old('name')}}" required>
                        </div>
                        <small class="text-danger">{{$errors->first('name')}}</small>
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <div class="input-group mb-3">
                            <textarea class="form-control" rows="2" name="desc">{{$room->ROOM_DESC ?? ''}}</textarea>
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