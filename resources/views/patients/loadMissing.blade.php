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

                    <div class="col-6 form-group">
                        <label>Load Patients who didn't visit From*</label>
                        <div class="input-group">
                            <input type="number" class="form-control" name=daysFrom value=45 required>
                        </div>
                    </div>

                    <div class="col-6 form-group">
                        <label>To*</label>
                        <div class="input-group">
                            <input type="number" class="form-control" name=daysTo value=90 required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success mr-2">Submit</button>

                </form>
            </div>
        </div>
    </div>

</div>

@endsection