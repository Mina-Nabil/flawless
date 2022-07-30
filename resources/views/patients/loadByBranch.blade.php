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

                    <button type="submit" class="btn btn-success mr-2">Submit</button>

                </form>
            </div>
        </div>
    </div>

</div>

@endsection