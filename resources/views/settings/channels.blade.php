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
                    <input type=hidden name=id value="{{(isset($channel)) ? $channel->id : ''}}">

                    <div class="form-group">
                        <label>Name*</label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon11"><i class="ti-channel"></i></span>
                            </div>
                            <input type="text" class="form-control" placeholder="Channelname" name=name aria-label="Channelname" aria-describedby="basic-addon11"
                                value="{{ (isset($channel)) ? $channel->CHNL_NAME : old('name')}}" required>
                        </div>
                        <small class="text-danger">{{$errors->first('name')}}</small>
                    </div>


                    <button type="submit" class="btn btn-success mr-2">Submit</button>
                    @if($isCancel)
                    <a href="{{url($homeURL) }}" class="btn btn-dark">Cancel</a>
                    @endif
                </form>
                @isset($channel)
                <hr>
                <h4 class="card-title">Delete Channel</h4>
                <div class="form-group">
                    <button type="button" onclick="confirmAndGoTo('{{url('channels/delete/'.$channel->id )}}', 'delete this Channel ?')" class="btn btn-danger mr-2">Delete Channel</button>
                </div>
                @endisset
            </div>
        </div>
    </div>
</div>
@endsection