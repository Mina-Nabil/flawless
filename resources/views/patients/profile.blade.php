@extends('layouts.app')


@section('content')

<div class="row">
    <!-- Column -->
    <div class="col-lg-4 col-xlg-3 col-md-5">
        <div class="card">
            <div class="card-body">
                <small class="text-muted">Patient Name </small>
                <h6>{{$patient->PTNT_NAME}}</h6>
                <small class="text-muted p-t-30 db">Phone</small>
                <h6>{{$patient->PTNT_MOBN}}</h6>
                <small class="text-muted p-t-30 db">Number of sessions</small>
                {{-- <h6>{{$patient->sessionCount()}}</h6> --}}
                <small class="text-muted p-t-30 db">Address</small>
                <h6>{{$patient->PTNT_ADRS}}</h6>
                <small class="text-muted p-t-30 db">Since</small>
                <h6>{{$patient->created_at->format("Y-M-d")}}</h6>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Total Paid</h5>
                <div class="d-flex m-t-30 m-b-20 no-block align-items-center">
                    <span class="display-5 text-success"><i class=" ti-money"></i></span>
                    {{-- <a href="javscript:void(0)" class="link display-5 ml-auto">{{number_format($patient->totalPaid(),2)}}</a> --}}
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Current Balance</h5>
                <div class="d-flex m-t-30 m-b-20 no-block align-items-center">
                    <span class="display-5 text-success"><i class=" ti-book"></i></span>
                    <a href="javscript:void(0)" class="link display-5 ml-auto">{{number_format($patient->PTNT_BLNC,2)}}</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Column -->
    <!-- Column -->
    <div class="col-lg-8 col-xlg-9 col-md-7">
        <div class="card">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs profile-tab" role="tablist">
                <li class="nav-item"> <a class="nav-link active" data-toggle="tab" href="#history" role="tab">Sessions</a> </li>
                <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#services" role="tab">Services</a> </li>
                <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#paid" role="tab">Account</a> </li>
                <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#pay" role="tab">Add Payment</a> </li>
                <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#settings" role="tab">Info</a> </li>
            </ul>
            <!-- Tab panes -->
            <div class="tab-content">

                <!--Sessions tab-->
                <div class="tab-pane active" id="history" role="tabpanel">
                    <div class="card-body">
                        <h4 class="card-title">Patient's Sessions History</h4>
                        <h6 class="card-subtitle">Total Money Paid: {{$patient->totalPaid()}}, Discount offered: {{$patient->totalDiscount()}}</h6>
                        <div class="col-12">
                            {{-- <x-datatable id="patientHistoryTable" :title="$title ?? 'Sessions History'" :subtitle="$subTitle ?? ''" :cols="$sessionsCols" :items="$sessionList" :atts="$sessionAtts" :cardTitle="false" /> --}}
                        </div>
                    </div>
                </div>


                <!--Item Bought tab-->
                <div class="tab-pane" id="services" role="tabpanel">
                    <div class="card-body">
                        <h4 class="card-title">Patient's Services</h4>
                        <h6 class="card-subtitle">All Services applied to the Patient</h6>
                        <div class="col-12">
                            {{-- <x-datatable id="patientServicesTable" :title="$title ?? 'Services'" :subtitle="$subTitle ?? ''" :cols="$servicesCols" :items="$servicesList" :atts="$servicesAtts" :cardTitle="false" /> --}}
                        </div>
                    </div>
                </div>

                <div class="tab-pane" id="paid" role="tabpanel">
                    <div class="row">
                        <div class="col-12">
                            <x-datatable id="patientPaymentsTable" :title="$payTitle" :subtitle="$paySubtitle" :cols="$payCols" :items="$pays" :atts="$payAtts" />
                        </div>
                    </div>
                </div>

                <div class="tab-pane" id="pay" role="tabpanel">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">{{ $payFormTitle }}</h4>
                                    <form class="form pt-3" method="post" action="{{ url($payFormURL) }}" enctype="multipart/form-data">
                                        @csrf
                                        <input type=hidden name=patientID value="{{(isset($patient)) ? $patient->id : ''}}">
                                        <input type="hidden" name="goToHome" value="0" >

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
                                                <textarea class="form-control" rows="2" name="comment"></textarea>
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

                <div class="tab-pane" id="settings" role="tabpanel">
                    <div class="card-body">
                        <h4 class="card-title">Edit {{ $patient->PTNT_NAME }}'s Info</h4>
                        <form class="form pt-3" method="post" action="{{ url($formURL) }}" enctype="multipart/form-data">
                            @csrf
                            <input type=hidden name=id value="{{(isset($patient)) ? $patient->id : ''}}">
                            <div class="form-group">
                                <label>Full Name*</label>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" placeholder="Full Name" name=name value="{{ (isset($patient)) ? $patient->PTNT_NAME : old('name')}}" required>
                                </div>
                                <small class="text-danger">{{$errors->first('name')}}</small>
                            </div>


                            <div class="form-group">
                                <label>Mobile Number*</label>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" placeholder="Mobile Number" name=mobn value="{{ (isset($patient)) ? $patient->PTNT_MOBN : old('mob') }}" required>
                                </div>
                                <small class="text-danger">{{$errors->first('mobn')}}</small>
                            </div>

                            <div class="form-group">
                                <label>Balance</label>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" placeholder="Patient Balance" name=balance value="{{ (isset($patient)) ? $patient->PTNT_BLNC : old('balance') }}" required>
                                </div>
                                <small class="text-danger">{{$errors->first('balance')}}</small>
                            </div>

                            <div class="form-group">
                                <label>Address</label>
                                <div class="input-group mb-3">
                                    <textarea class="form-control" rows="3" name="address">{{ (isset($patient)) ? $patient->PTNT_ADRS : old('address') }}</textarea>
                                </div>
                                <small class="text-danger">{{$errors->first('balance')}}</small>
                            </div>

                            <button type="submit" class="btn btn-success mr-2">Submit</button>
                        </form>
                    </div>
                </div>


            </div>
        </div>
    </div>
    <!-- Column -->
</div>
@endsection

@section("js_content")
<script type="text/javascript" src="{{asset('assets/node_modules/multiselect/js/jquery.multi-select.js')}}"></script>
<script type="text/javascript" src="{{asset('assets/node_modules/bootstrap-select/bootstrap-select.min.js')}}"></script>
<script type="text/javascript" src="{{asset('assets/node_modules/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js')}}"></script>


<script src="https://widget.cloudinary.com/v2.0/global/all.js" type="text/javascript"></script>
<script>
    var myWidget = cloudinary.createUploadWidget({
    cloudName: 'sasawhale', 
    folder: "whale/models",
    uploadPreset: 'whalesec'}, (error, result) => { 
      if (!error && result && result.event === "success") { 
        document.getElementById('uploaded').value = result.info.url;
      }
    }
  )
  
  document.getElementById("upload_widget").addEventListener("click", function(){
      myWidget.open();
    }, false);

    function confirmAndGoTo(url, action){
    Swal.fire({
        text: "Are you sure you want to " + action + "?",
        icon: "warning",
        showCancelButton: true,
        }).then((isConfirm) => {
    if(isConfirm.value){

    window.location.href = url;
        }
    });

}

</script>
@endsection