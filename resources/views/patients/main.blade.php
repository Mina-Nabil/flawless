@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Patients Count</h5>
                <div class="d-flex m-t-30 m-b-20 no-block align-items-center">
                    <span class="display-6 text-info"><i class="fas fa-users"></i></span>
                    <a href="javscript:void(0)" class="link display-6 ml-auto">{{$allCount}}</a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">New This Month</h5>
                <div class="d-flex m-t-30 m-b-20 no-block align-items-center">
                    <span class="display-6 text-danger"><i class="fas fa-user-plus"></i></span>
                    <a href="javscript:void(0)" class="link display-6 ml-auto">{{$newCount}}</a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Follow-Ups</h5>
                <div class="d-flex m-t-30 m-b-20 no-block align-items-center">
                    <span class="display-6 text-success"><i class="fas fa-street-view"></i></span>
                    <a href="javscript:void(0)" class="link display-6 ml-auto">{{0}}</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Patients Page</h4>
                <h6 class="card-subtitle">Check out all patients data </h6>
                <!-- Nav tabs -->
                <div class="vtabs">
                    <ul class="nav nav-tabs tabs-vertical" role="tablist">
                        <li class="nav-item ">
                            <a class="nav-link active" data-toggle="tab" href="#patients" role="tab" aria-selected="false">
                                <span class="hidden-sm-up"><i class="ti-user"></i></span>
                                <span class="hidden-xs-down">Patients</span>
                            </a>
                        </li>

                        {{-- <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#addPatient" role="tab" aria-selected="false">
                                <span class="hidden-sm-up">
                                    <i class="fas fa-user-plus"></i></span>
                                <span class="hidden-xs-down">Add Patient</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#pay" role="tab" aria-selected="false">
                                <span class="hidden-sm-up">
                                    <i class="fas fa-hand-holding-usd"></i></span>
                                <span class="hidden-xs-down">Add Patient Payment</span>
                            </a>
                        </li> --}}
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div class="tab-pane active" id="patients" role="tabpanel">
                            <div class="row">
                                <div class="col-12">
                                    <x-datatable id="patientsTable" :title="$patientsTitle" :subtitle="$patientsSubtitle" :cols="$patientsCols" :items="$patients" :atts="$patientsAtts" />
                                </div>
                            </div>
                        </div>


                        <div class="tab-pane" id="addPatient" role="tabpanel">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <h4 class="card-title">{{ $addPatientFormTitle }}</h4>
                                            {{-- <form class="form pt-3" method="post" action="{{ url($addPatientFormURL) }}" enctype="multipart/form-data"> --}}
                                                @csrf

                                                <div class="form-group">
                                                    <label>Name*</label>
                                                    <div class="input-group mb-3">
                                                        <input type="text" id="patientName" class="form-control" placeholder="Patient Name" name=name value="{{old('name')}}" required>
                                                    </div>
                                                    <small class="text-danger">{{$errors->first('name')}}</small>
                                                </div>

                                                <div class="form-group">
                                                    <label>Mobile*</label>
                                                    <div class="input-group mb-3">
                                                        <input type="text" id="patientMobn" class="form-control" placeholder="Patient Mobile Number" name=mobn value="{{old('mobn')}}" required>
                                                    </div>
                                                    <small class="text-danger">{{$errors->first('in')}}</small>
                                                </div>

                                                <div class="form-group">
                                                    <label>Pricelist*</label>
                                                    <select class="select2 form-control  col-md-12 mb-3 " style="width:100%" id=listID>
                                                        @foreach($allPricelists as $list)
                                                        <option value="{{$list->id}}" @if($list->PRLS_DFLT) selected @endif >
                                                            {{$list->PRLS_NAME}} @if($list->PRLS_DFLT)(Default)@endif
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label>Balance*</label>
                                                    <div class="input-group mb-3">
                                                        <input type="number" id="patientBlnc" step=0.01 class="form-control" placeholder="Patient Balance" name=balance value="{{old('balance') ?? 0}}" required>
                                                    </div>
                                                    <small class="text-muted">In case patient owes us or we owe him money, default is 0</small>
                                                    <small class="text-danger">{{$errors->first('balance')}}</small>
                                                </div>

                                                <div class="form-group">
                                                    <label>Address</label>
                                                    <div class="input-group mb-3">
                                                        <textarea class="form-control" rows="2" name="adrs" id="patientAdrs">{{ old('adrs') }}</textarea>
                                                    </div>

                                                    <small class="text-danger">{{$errors->first('adrs')}}</small>
                                                </div>

                                                <div class="form-group">
                                                    <label>Note</label>
                                                    <div class="input-group mb-3">
                                                        <textarea class="form-control" rows="2" name="note">{{ old('note') }}</textarea>
                                                    </div>
                                                </div>

                                                <button type="button" onclick="addNewPatient(false)" class="btn btn-success mr-2">Add Patient</button>

                                                {{--
                                            </form> --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>



                        <div class="tab-pane" id="pay" role="tabpanel">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <h4 class="card-title">{{ $payFormTitle }}</h4>
                                            <h6 class="card-subtitle">{{ $payFormSubtite }}</h4>
                                                <form class="form pt-3" method="post" action="{{ url($payFormURL) }}">
                                                    @csrf
                                                    <div class="form-group col-md-12 m-t-0">
                                                        <h5>Patients</h5>
                                                        <select class="select2 form-control" style="width:100%" name=patientID>
                                                            <?php foreach ($patients as $patient) { ?>
                                                            <option value="{{$patient->id}}">
                                                                {{$patient->PTNT_NAME}} - {{$patient->PTNT_MOBN}}
                                                            </option>
                                                            <?php } ?>
                                                        </select>
                                                        <small class="text-danger">{{$errors->first('patientID')}}</small>
                                                    </div>

                                                    <input type="hidden" name="goToHome" value="1">

                                                    <div class="form-group">
                                                        <label>Amount*</label>
                                                        <div class="input-group mb-3">
                                                            <input type="number" step=0.01 class="form-control" placeholder="Payment Amount" name=amount value="0" required>
                                                        </div>
                                                        <small class="text-danger">{{$errors->first('amount')}}</small>
                                                    </div>

                                                    <div class="form-group">
                                                        <label>Comment</label>
                                                        <div class="input-group mb-3">
                                                            <textarea class="form-control" rows="2" name="comment" required></textarea>
                                                        </div>

                                                        <small class="text-danger">{{$errors->first('comment')}}</small>
                                                    </div>

                                                    <button type="submit" class="btn btn-success mr-2">Add Payment</button>

                                                </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection