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

                    <div class="form-group">
                        <label>Date</label>
                        <div class="input-group mb-3">
                            <input type="date" value="{{date('Y-m-d')}}" name="date" class="form-control" placeholder="Exception Date" required>
                        </div>
                        <small class="text-danger">{{$errors->first('date')}}</small>
                    </div>

                    <div class="form-group">
                        <label for="input-file-now-custom-1">Shift*</label>
                        <div class="input-group mb-3">
                            <select name=shift class="form-control">
                                @foreach ($shifts as $shift)
                                <option value="{{$shift}}">{{$shift}}</option>
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
                                <option value={{$doc->id}} >{{$doc->DASH_USNM}}</option>
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
                        <div class="bt-switch justify-content-end">
                            <input type="checkbox" name="coming" data-size="large" data-on-color="success" data-off-color="danger" data-on-text="Coming" data-off-text="Not Coming">
                        </div>
                        <small class="text-danger">{{$errors->first('coming')}}</small>
                    </div>

                    <div class="form-group">
                        <label>Note</label>
                        <div class="input-group mb-3">
                            <textarea class="form-control" rows="2" name="note"></textarea>
                        </div>
                        <small class="text-danger">{{$errors->first('note')}}</small>
                    </div>

                    <button type="submit" class="btn btn-success mr-2">Submit</button>

                </form>

            </div>
        </div>
    </div>
</div>
@endsection