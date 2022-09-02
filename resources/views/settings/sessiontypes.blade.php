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
                    <input type=hidden name=id value="{{(isset($sessiontype)) ? $sessiontype->id : ''}}">

                    <div class="form-group">
                        <label>Name*</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="Session Type Name" name=name value="{{ (isset($sessiontype)) ? $sessiontype->SHTP_NAME : old('name')}}" required>
                        </div>
                        <small class="text-danger">{{$errors->first('name')}}</small>
                    </div>

                    <div class="form-group">
                        <label>Duration (mins)*</label>
                        <div class="input-group mb-3">
                            <input type="number" step=1 class="form-control" placeholder="Duration in minutes" name=duration value="{{ (isset($sessiontype)) ? $sessiontype->SHTP_DUR : old('duration')}}" required>
                        </div>
                        <small class="text-danger">{{$errors->first('duration')}}</small>
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <div class="input-group mb-3">
                            <textarea class="form-control" rows="2" name="desc">{{$sessiontype->SHTP_DESC ?? ''}}</textarea>
                        </div>

                        <small class="text-danger">{{$errors->first('desc')}}</small>
                    </div>
                    <hr>
                    <div class="form-group">
                        <label>Doctors</label>
                        <div class=row>
                            <div class=col-12>
                                <div id="dynamicContainer">
                                </div>
                            </div>
                        </div>
                        <?php $i = 0?>
                        @isset($sessiontype)
                        @foreach ($sessiontype->doctors as $doc)
                        <div class="row removeclass{{$i}}">
                            <div class="col-lg-12">
                                <div class="input-group mb-2">
                                    <select name="doctors[{{$i}}]" class="form-control select2 custom-select" required>
                                        <option disabled hidden selected value="" class="text-muted">Select a doctor</option>
                                        @foreach($doctors as $doctor)
                                        <option value="{{ $doctor->id }}" @if($doctor->id == $doc->id) selected @endif>
                                            {{$doc->DASH_USNM}} - {{$doc->DASH_FLNM}}
                                        </option>
                                        @endforeach
                                    </select>
                                    <div class="input-group-append">
                                        <button class="btn btn-danger" type="button" onclick="removeDoctor({{$i}});"><i class="fa fa-minus"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php $i++ ?>
                        @endforeach
                        @endisset

                        <small class="text-danger">{{$errors->first('desc')}}</small>
                    </div>

                    <button type="submit" class="btn btn-success mr-2">Submit</button>
                    <button type="button" class="btn btn-info mr-2" onclick="addDoctor()">Add Doctor</button>
                    @if($isCancel)
                    <a href="{{url($homeURL) }}" class="btn btn-dark">Cancel</a>
                    @endif
                </form>

            </div>
        </div>
    </div>
</div>
<script>
    var room = {{$i}};

function addDoctor() {

    var objTo = document.getElementById('dynamicContainer')
    var divtest = document.createElement("div");
    divtest.setAttribute("class", "row removeclass" + room);


    var concatString = ` <div class="col-lg-12">
                                <div class="input-group mb-3">
                                    <select name="doctors[` + room + `]" class="form-control select2 custom-select" required>
                                        <option disabled hidden selected value="" class="text-muted">Select a doctor</option>
                                        @foreach($doctors as $doc)
                                        <option value="{{ $doc->id }}">
                                            {{$doc->DASH_USNM}} - {{$doc->DASH_FLNM}}
                                        </option>
                                        @endforeach
                                    </select>
                                    <div class="input-group-append">
                                        <button class="btn btn-danger" type="button" onclick="removeDoctor(` + room + `);"><i class="fa fa-minus"></i></button>
                                    </div>
                                </div>
                            </div>`

    divtest.innerHTML = concatString;
    
    objTo.appendChild(divtest);
    $(".select2").select2()
                            
    room++
}

function removeDoctor(rid) {
    $('.removeclass' + rid).remove();
}

</script>
@endsection