@extends('layouts.app')


@section('content')


<div class="row">
    <!-- Column -->
    <div class="col-lg-12 ">
        <div class="card">
            @switch($session->SSHN_STTS)
            @case("New")
            <div class="card-header bg-info text-light">New Session</div>
            @break
            @case("Pending Payment")
            <div class="card-header bg-warning text-light">Pending Payment!</div>
            @break
            @case("Cancelled")
            <div class="card-header bg-danger text-light">Session Cancelled :(</div>
            @break
            @case("Done")
            <div class="card-header bg-success text-light">Session Done ㋛</div>
            @break
            @default
            <div class="card-header bg-dark text-light">Session Details</div>
            @endswitch

            <div class="card-body">
                <div class="row">
                    <div class="col-md-2">
                        <div class="font-bold mb-2">
                            Branch
                        </div>
                        <p class="text-muted">{{$session->branch->BRCH_NAME}}</p>
                    </div>
                    <div class="col-md-2">
                        <div class="font-bold mb-2">
                            Patient
                        </div>
                        <p class="text-muted">
                            <a target="_blank" href="{{url('patients/profile/' . $session->SSHN_PTNT_ID )}}">
                                {{$session->patient->PTNT_NAME }}
                            </a>
                            @if($session->patient->PTNT_BLNC != 0)
                            <br>
                            <small>Current Balance: <strong>{{number_format($session->patient->PTNT_BLNC)}}</strong></small>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-2">
                        <div class="font-bold mb-2">
                            Session Date
                        </div>
                        <p class="text-muted">{{$session->SSHN_DATE->format('d-M-Y')}}</p>
                    </div>
                    <div class="col-md-2">
                        <div class="font-bold mb-2">
                            Start
                        </div>
                        <p class="text-muted">{{$session->SSHN_STRT_TIME}}</p>
                    </div>
                    <div class="col-md-2">
                        <div class="font-bold mb-2">
                            End
                        </div>
                        <p class="text-muted">{{$session->SSHN_END_TIME}}</p>
                    </div>

                    <div class="col-md-2">
                        <div class="font-bold mb-2">
                            Patient Phone
                        </div>
                        <p class="text-muted">{{$session->patient->PTNT_MOBN}}</p>
                    </div>
                    <div class="col-md-2">
                        @if(Auth::user()->canAdmin())
                        <div class="font-bold mb-2">
                            Total {{($session->discount > 0) ? "({$session->SSHN_DISC}% Discount)" : ""}}
                        </div>
                        <p class="text-muted">{{$session->SSHN_TOTL ." EGP"}} {{($session->discount > 0) ? "(" .$session->discount. ")" : ""}}</p>
                        @endif
                    </div>
                    <div class="col-md-2">
                        <div class="font-bold mb-2">
                            Doctor Total
                        </div>
                        <p class="text-muted">{{$session->doctor_total}}</p>
                    </div>
                    @if($patient_packages->count() > 0)

                    <div class="col-md-2">
                        <div class="font-bold mb-2">
                            Packages Available
                        </div>
                        @foreach($patient_packages as $item)
                        <strong>
                            <p class="text-muted">{{$item->pricelistItem->item_name}} : {{$item->PTPK_QNTY}}</p>
                        </strong>
                        @endforeach

                    </div>



                    @endif

                    <div class="col-md-6">
                        <div class="font-bold mb-2">
                            Note
                        </div>
                        <p class="text-muted">{{$session->SSHN_TEXT}}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Column -->
    <!-- Column -->
    <div class="col-lg-12">
        <div class="card">
            <!-- Nav tabs -->
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs">
                    <li class="nav-item"> <a class="nav-link active" role="tab" data-toggle="tab" href="#status">Session Status</a> </li>
                    <li class="nav-item"> <a class="nav-link" role="tab" data-toggle="tab" href="#services">Services</a> </li>
                    <li class="nav-item"> <a class="nav-link" role="tab" data-toggle="tab" href="#history">Patient</a> </li>
                    @if(Auth::user()->canAdmin() )
                    <li class="nav-item"> <a class="nav-link" role="tab" data-toggle="tab" href="#payment ">Payment</a> </li>
                    <li class="nav-item"> <a class="nav-link" role="tab" data-toggle="tab" href="#doctor ">Doctor</a> </li>
                    <li class="nav-item"> <a class="nav-link" role="tab" data-toggle="tab" href="#settings">Session Info</a> </li>
                    <li class="nav-item"> <a class="nav-link" role="tab" data-toggle="tab" href="#balancelogs">Balance Log</a> </li>
                    @endif
                    <li class="nav-item"> <a class="nav-link" role="tab" data-toggle="tab" href="#log">Log</a> </li>

                </ul>
            </div>
            <!-- Tab panes -->
            <div class="tab-content">
                <!--Status tab-->
                <div class="tab-pane active" id="status" role="tabpanel">
                    <div class="card-body">
                        <h4 class="card-title">Session Status</h4>
                        <h6 class="card-subtitle">Showing Session Status Summary</h6>
                        <ul>
                            <li>
                                <p class="text-muted">Session created by {{$session->creator->DASH_USNM}} </p>
                            </li>

                            @if(isset($session->doctor))
                            <li>
                                <p class="text-muted">Managed by Dr. {{$session->doctor->DASH_USNM}}
                                    <i class="fas fa-check-circle" style="color:lightgreen"></i>
                                </p>
                            </li>
                            @else
                            <li>
                                <p class="text-muted">No Assigned Doctor yet <i class=" fas fa-exclamation-triangle" style="color:#fec107"></i></p>
                            </li>
                            @endif

                            @if($session->items->count() > 0)
                            <li>
                                <strong>
                                    <p class="text-muted">Services Done </p>
                                </strong>
                                <ul>
                                    @foreach ($session->items as $item)
                                    <li>
                                        <p class="text-muted">{{$item->pricelistItem->device->DVIC_NAME}} - {{$item->pricelistItem->PLIT_TYPE}} @if($item->pricelistItem->PLIT_TYPE=="Area")
                                            ({{$item->pricelistItem->area->AREA_NAME}}) @endif
                                            @if(Auth::user()->canAdmin())
                                            {{$item->SHIT_TOTL}}EGP
                                            @endif
                                        </p>
                                    </li>

                                    @endforeach
                                </ul>
                            </li>
                            @endif
                            @if(Auth::user()->canAdmin())
                            <li>
                                @if($session->remaining_money != 0)
                                <p class="text-muted">Payment not yet fully collected please collect payment before setting Session as Done, {{$session->remaining_money}}EGP remaining
                                    <i class=" fas fa-exclamation-triangle" style="color:#fec107"></i>
                                </p>
                                @elseif($session->SSHN_TOTL > 0)
                                <p class="text-muted">Payment fully collected
                                    <i class="fas fa-check-circle" style="color:lightgreen"></i>
                                </p>
                                @else
                                <p class="text-muted">No Services attached</p>
                                @endif
                            </li>
                            @endif
                        </ul>

                        <button class="btn btn-info mr-2" onclick="confirmAndGoTo('{{url($setSessionNewUrl)}}', 'Set Session as New')" @if(!$session->canBeNew()) disabled @endif >Set Session as
                            New</button>
                        <button class="btn btn-warning mr-2" onclick="confirmAndGoTo('{{url($setSessionPendingUrl)}}', 'Set Session as Pending Payment')" @if(!$session->canBePending())
                            disabled @endif >Session Is Ready For Payment</button>
                        <button class="btn btn-danger mr-2" onclick="confirmAndGoTo('{{url($setSessionCancelledUrl)}}', 'Cancel the Session')" @if(!$session->canBeCancelled()) disabled @endif >
                            Cancel Session</button>
                        <button class="btn btn-success mr-2" onclick="setAsDone()" @if(!$session->canBeDone()) disabled @endif
                            >Set Session As Done</button>

                    </div>
                </div>


                {{-- Session Details --}}
                <div class="tab-pane" id="services" role="tabpanel">
                    <div class="card-body">
                        <h4 class="card-title">Services</h4>
                        <h6 class="card-subtitle">Manage Sessions Services</h6>

                        <form class="form pt-3" method="post" action="{{ url($updateServicesURL) }}">
                            @csrf
                            <input name=id value="{{$session->id}}" type="hidden">
                            <div class="row ">
                                <div class="mb-10 col-3 ">
                                    <button type="button" class="btn btn-success" onclick="addService()" @if(!$session->canEditServices()) disabled @endif>Add Service</button>
                                </div>
                                <div class="ml-10 col-9 d-flex justify-content-end">
                                    <div class="bt-switch">
                                        <div>
                                            <input type="checkbox" data-size="medium" data-on-color="success" data-off-color="danger" data-on-text="Commission" {{($session->SSHN_CMSH) ?'checked':''}}
                                            data-off-text="No Commission" name="isCommission" @if(!$session->canEditServices()) disabled @endif>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class=row>
                                <div class=col-12>
                                    <div id="dynamicContainer">
                                    </div>
                                </div>
                            </div>
                            <?php $i = 0?>
                            @foreach ($session->items as $item)
                            <div class="row removeclass{{$i}}">
                                <div class="mr-2 adjust-self-center">
                                    @if($item->is_collected)
                                    <i class="fas fa-check-circle" style="color:lightgreen" title="Collected from Client Package"></i>
                                    <input type="hidden" value="1" name="isCollected[{{$i}}]">
                                    @else
                                    <i class="fas fa-check-circle" style="color:white"></i>
                                    <input type="hidden" value="0" name="isCollected[{{$i}}]">
                                    @endif
                                </div>
                                <input type="hidden" name="isDoctor[{{$i}}]" value="off">
                                <div class="col-1">
                                    <div class="bt-switch justify-content-end">
                                        <input type="checkbox" name="isDoctor[{{$i}}]" data-size="medium" data-on-color="primary" data-off-color="info" data-on-text="Doctor" {{$item->is_doctor ?"checked":"off"}}
                                        data-off-text="Clinic" id="isDoctor{{$i}}" @if(!$session->canEditServices())
                                        disabled @endif @if(Auth::user()->isDoctor()) readonly @endif>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="input-group mb-2">
                                        <select name="device[{{$i}}]" class="form-control select2 custom-select" style="width:100%" id="device{{$i}}" onchange="loadServices({{$i}})" required
                                            @if(!$session->canEditServices())
                                            disabled @endif>
                                            <option disabled hidden selected value="" class="text-muted">Device</option>
                                            @foreach($devices as $device)
                                            <option value="{{ $device->id }}" @if($device->id == $item->deviceObj()->id) selected @endif>
                                                {{$device->DVIC_NAME}}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>



                                <div class="col-2">
                                    <div class="input-group mb-2">
                                        <select name="service[{{$i}}]" class="form-control select2 custom-select" style="width:100%" id="service{{$i}}" onchange="checkUnit({{$i}})" required
                                            @if(!$session->canEditServices())
                                            disabled @endif>
                                            <option disabled hidden selected value="" class="text-muted">Type</option>
                                            @foreach($item->availableServices($session->SSHN_PTNT_ID) as $service)
                                            <option value="{{$service['id']}}" <?php if($service['id']==$item->SHIT_PLIT_ID ) { ?> selected
                                                <?php  
                                            if($service['serviceName'] != "Pulse") { 
                                            $readOnly=true;
                                            } else {
                                            $readOnly=false;  
                                            } }?>> {{$service['serviceName']}}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-3">
                                    <div class="input-group mb-3">
                                        <input id="note{{$i}}" value="{{$item->SHIT_NOTE}}" type="text" class="form-control" placeholder="Note" name="note[{{$i}}]">
                                    </div>
                                </div>


                                <div class="col-2">
                                    <div class="input-group mb-3">
                                        <input id="unit{{$i}}" value="{{$item->SHIT_QNTY}}" type="number" step="0.01" class="form-control amount" placeholder="Unit" name="unit[{{$i}}]" {{((isset($readOnly) &&
                                            $readOnly)||!$session->canEditServices()) ? 'readonly' : ''}}>

                                        <div class="input-group-append">
                                            <button class="btn btn-danger" type="button" onclick="removeService({{$i}});" @if(!$session->canEditServices()) disabled @endif><i class="fa fa-minus"></i></button>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <?php $i++ ?>
                            @endforeach

                            <hr>
                            <button type="submit" class="btn btn-success mr-2" @if(!$session->canEditServices()) disabled @endif>Submit</button>
                        </form>


                    </div>
                </div>


                <!--Item Bought tab-->
                <div class="tab-pane" id="history" role="tabpanel">
                    <div class="card-body">
                        <h4 class="card-title">Patient's Info</h4>
                        <h6 class="card-subtitle">Patient's Notes & Services</h6>
                        <div class="col-12">
                            <form class="form pt-3" method="post" action="{{ $setNoteURL }}">
                                @csrf
                                <input name=id type="hidden" value="{{$session->SSHN_PTNT_ID}}">
                                <div class="form-group">
                                    <label>Note</label>
                                    <div class="input-group mb-3">
                                        <textarea class="form-control" rows="5" name="note">{{ (isset($session->patient)) ? $session->patient->PTNT_NOTE : old('note') }}</textarea>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-success mr-2">Submit</button>
                            </form>
                        </div>
                        <hr>

                        <div class="col-12">
                            <x-datatable id="patientServicesTable" :title="$title ?? 'Services History'" :subtitle="$subTitle ?? ''" :cols="$servicesCols" :items="$servicesList" :atts="$servicesAtts"
                                :cardTitle="false" />
                        </div>
                    </div>
                </div>
                @if(Auth::user()->canAdmin() )
                <!--Add Item tab-->
                <div class="tab-pane" id="payment" role="tabpanel">
                    <div class="card-body">
                        <h4 class="card-title">Session Payments</h4>
                        <h6 class="card-subtitle">Total: {{$session->SSHN_TOTL}} - Paid: {{$session->SSHN_PAID}} - Client Balance: {{$session->SSHN_PTNT_BLNC}} - Discount: {{$session->discount}}
                            ({{$session->SSHN_DISC}}%) -
                            <strong>Remaining: {{$session->remaining_money}}</strong>
                        </h6>
                        @if($session->canEditMoney())
                        <form class="form pt-3" method="post" action="{{ url($paymentURL) }}">
                            <input type="hidden" name=id value='{{$session->id}}'>
                            @csrf
                            <div class="row">
                                <div class="col-9">
                                    <div class="form-group">
                                        <label>Payment</label>
                                        <div class="input-group mb-3">
                                            <input type="number" step=.01 class="form-control amount" placeholder="Items Count" min=0.01 name=amount value="0" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-3 d-flex align-items-center">
                                    <div class="custom-control custom-radio mr-5">
                                        <input type="radio" id="customRadio1" name="cashRadio" class="custom-control-input" value=cash required>
                                        <label class="custom-control-label" for="customRadio1">Cash</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="customRadio2" name="cashRadio" class="custom-control-input" value=visa required>
                                        <label class="custom-control-label" for="customRadio2">Visa</label>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success mr-2">Collect Normal Payment</button>
                            <button type="button" class="btn btn-success mr-2" onclick="confirmAndGoTo('{{url($settleSessionOnPackagesURL)}}', 'Settle Session on Client Packages')" @if($session->remaining_money <=0)
                                    disabled @endif>Settle Session on Client Packages</button>
                            <button type="button" class="btn btn-success mr-2" onclick="confirmAndGoTo('{{url($settleSessionOnBalanceURL)}}', 'Settle Session on Client Balance')" @if($session->remaining_money <=0)
                                    disabled @endif>Settle Session on Client Balance</button>
                            <p class="text-muted mt-2">Payments higher than <strong>{{$session->remaining_money}}</strong> will be added to the Patient Balance</p>
                        </form>
                        <hr>
                        <form class="form pt-3" method="post" action="{{ url($discountURL) }}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name=id value={{$session->id}}>
                            <div class="form-group">
                                <label>Discount %</label>
                                <div class="input-group mb-3">
                                    <input type="number" step=1 class="form-control amount" min=0 max={{$session->remaining_discount}} name=discount value="{{$session->SSHN_DISC}}" required>
                                </div>
                                <p class="text-muted mt-2">Enter discount percentage value (0-100)</p>
                            </div>
                            <button type="submit" class="btn btn-success mr-2" @if($session->remaining_money <=0) disabled @endif>Set Discount</button>
                        </form>

                        @else
                        <p class="text-muted">Closed Session Payment & Discounts Can't be modified</p>
                        @endif
                    </div>
                </div>


                {{-- Session Details --}}
                <div class="tab-pane" id="doctor" role="tabpanel">
                    <div class="card-body">
                        <h4 class="card-title">Set Doctor</h4>
                        <h6 class="card-subtitle">Assign Session to a doctor</h6>

                        <form class="form pt-3" method="post" action="{{ url($doctorURL) }}">
                            @csrf
                            <input type="hidden" name="id" value="{{$session->id}}">
                            <div class="form-group">
                                <label>Doctors</label>
                                <select class="select2 form-control  col-md-12 mb-3" style="width:100%" name=doctorID required @if(!$session->canEditDoctor()) disabled @endif>
                                    @foreach($doctors as $doctor)
                                    <option value="{{$doctor->id}}" @if($doctor->id == $session->SSHN_DCTR_ID)
                                        selected
                                        @endif
                                        >{{$doctor->DASH_USNM}}
                                    </option>
                                    @endforeach
                                </select>
                                <small class="text-danger">{{$errors->first('doctorID')}}</small>
                            </div>

                            <button type="submit" class="btn btn-success mr-2" @if(!$session->canEditDoctor()) disabled @endif>Set Doctor</button>

                        </form>
                    </div>
                </div>

                <!--Settings tab-->
                <div class="tab-pane " id="settings" role="tabpanel">
                    <div class="card-body">
                        <div class="card-body">
                            <h4 class="card-title">Session Info </h4>
                            <h6 class="card-subtitle">Edit Session Info and date</h6>
                            @if( $session->canEditInfo() )
                            <form class="form pt-3" method="post" action="{{ url($editSessionURL) }}">
                                @csrf
                                <input name=id type="hidden" value="{{$session->id}}">

                                <div class="form-group">
                                    <label>Patients</label>
                                    <select class="select2 form-control  col-md-12 mb-3" style="width:100%" name=patientID>
                                        @foreach($patients as $patient)
                                        <option value="{{$patient->id}}" @if($patient->id == $session->SSHN_PTNT_ID)
                                            selected
                                            @endif
                                            >{{$patient->PTNT_NAME}}
                                        </option>
                                        @endforeach
                                    </select>
                                    <small class="text-danger">{{$errors->first('patientID')}}</small>
                                </div>

                                <div class="form-group">
                                    <label>Date*</label>
                                    <div class="input-group mb-3">
                                        <input type="date" class="form-control" placeholder="Session Day" name=sessionDate value="{{$session->SSHN_DATE->format('Y-m-d')}}" required>
                                    </div>
                                    <small class="text-danger">{{$errors->first('sessionDate')}}</small>
                                </div>

                                <div class="form-group">
                                    <label>Start Time*</label>
                                    <div class="input-group mb-3">
                                        <input type="time" class="form-control" placeholder="Session Start Time" value="{{$session->SSHN_STRT_TIME}}" name=sessionStartTime required>
                                    </div>
                                    <small class="text-danger">{{$errors->first('sessionStartTime')}}</small>
                                </div>

                                <div class="form-group">
                                    <label>End Time*</label>
                                    <div class="input-group mb-3">
                                        <input type="time" class="form-control" placeholder="Session End Time" value="{{$session->SSHN_END_TIME}}" name=sessionEndTime required>
                                    </div>
                                    <small class="text-danger">{{$errors->first('sessionEndTime')}}</small>
                                </div>

                                <div class="form-group">
                                    <label>Extra Notes</label>
                                    <div class="input-group mb-3">
                                        <textarea class="form-control" rows="2" name="sessionComment">{{$session->SSHN_TEXT}}</textarea>
                                    </div>
                                    <small class="text-danger">{{$errors->first('sessionComment')}}</small>
                                </div>
                                <button type="submit" class="btn btn-success mr-2">Edit Session</button>

                            </form>
                            @else
                            <p class="text-muted">Old Session Info can't be modified</p>
                            @endif
                            <hr>

                            <input type="hidden" value="{{$session->id}}">
                            <button type="button" class="btn btn-danger mr-2" onclick="confirmAndGoTo('{{url($deleteSessionURL)}}', 'delete this session and all its info')">Delete Session</button>

                        </div>
                    </div>
                </div>

                <div class="tab-pane" id="balancelogs" role="tabpanel">
                    <div class="card-body">
                        <h4 class="card-title">Patient's Balance Log</h4>
                        <h6 class="card-subtitle">Check all patient's balance Log</h6>

                        <ul class="list-group">
                            @foreach($session->packageLogs as $event)
                            <a href="javascript:void(0)" class="list-group-item list-group-item-action flex-column align-items-start">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1 text-dark">{{$event->PKLG_TTLE}}</h5>
                                    <small>{{$event->user->DASH_USNM}} on {{$event->created_at}}</small>
                                </div>
                                <p class="mb-1">{{$event->PKLG_CMNT}}</p>
                            </a>
                            @endforeach
                  
                        </ul>
                    </div>
                </div>
                @endif

                <div class="tab-pane" id="log" role="tabpanel">
                    <div class="card-body">
                        <h4 class="card-title">Session history</h4>
                        <h6 class="card-subtitle">Check all session changes & events</h6>
                        <ul class="list-group">
                            @foreach($session->logs as $event)
                            <a href="javascript:void(0)" class="list-group-item list-group-item-action flex-column align-items-start">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1 text-dark">{{$event->user->DASH_USNM}}</h5>
                                    <small>{{$event->created_at}}</small>
                                </div>
                                <p class="mb-1">{{$event->LOG_TEXT}}</p>
                            </a>
                            @endforeach
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- Column -->
</div>

<script src="{{ asset('assets/node_modules/jquery/jquery-3.2.1.min.js') }}"></script>
<script src="{{ asset('assets/node_modules/datatables/datatables.min.js') }}"></script>

<script>
    function loadServices(row){
        device = $('#device' + row).val();

        var http = new XMLHttpRequest();
        var url = "{{url($getServicesAPI)}}";
        http.open('POST', url);
        http.setRequestHeader("Accept", "application/json");

        var formdata = new FormData();
        formdata.append('deviceID',device);
        formdata.append('patientID', {{$session->SSHN_PTNT_ID}});
        formdata.append('_token','{{ csrf_token() }}');

        

        http.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            try {        
                services = JSON.parse(this.responseText);
                showServices(row, services)
            } catch(e){
                console.log(e); 
            }
        } 
        };
        http.send(formdata, true);

    }

    function showServices(row, services){
        $('#service' + row).html("") //clearing the select
        $('#service' + row).removeAttr('disabled')
        services.forEach(service => {
            console.log(service)
            var newOption = new Option(service.serviceName, service.id, false, false);
            $('#service' + row).append(newOption).trigger('change');
        });

    }


    function IsNumeric(n) {
        return !isNaN(parseFloat(n)) && isFinite(n);
    }

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

    function setAsDone() {

        var dateInput = document.createElement("input");
        dateInput.type = "date";
        dateInput.className = "form-control"
        dateInput.placeholder = "Followup Date"
        dateInput.value = "{{((new DateTime() )->add(new DateInterval('P7D')))->format('Y-m-d')}}"


        Swal.fire({
                text: "Are you sure you want to set the session as done?",
                icon: "warning",
                showCancelButton: true,
            }).then( (isConfirm) => {
                if(isConfirm.value){
                    //     inputValue = await Swal.fire({
                    //     text: "Add a follow up?",
                    //     icon: "warning",
                    //     input: "text",
                    //     inputValue: "{{((new DateTime() )->add(new DateInterval('P7D')))->format('Y-m-d')}}",
                    //     inputLabel: 'Date',
                    //     showCancelButton: true,
                    //     cancelButtonText: "No",
                    //     confirmButtonText: "Yes",
                    // });
                    window.location.href = '{{url($setSessionDoneUrl)}}';
                }
            })
        }

</script>

<script>
    $(function () {
            $(function () {

                var table = $('#itemsTable').DataTable({
                    "displayLength": 25,
                    dom: 'Bfrtip',
                    buttons: [
                        {
                            extend: 'excel',
                            title: 'Flawless',
                            footer: true,
                        }
                    ]
                });
            })
        })
</script>
@endsection


@section('js_content')

<script>
    @if($session->canEditServices())
    var room = {{$i}};

    function addService() {
   
        var objTo = document.getElementById('dynamicContainer')
        var divtest = document.createElement("div");
        divtest.setAttribute("class", "row removeclass" + room);

        var concatString = ' <input type="hidden" value="0" name="isCollected[' + room + ']">\
                            <input type="hidden" value="off" name="isDoctor[' + room + ']">';

        concatString += '<div class="mr-3"></div>\
                        <div class="col-1">\
                            <div class="bt-switch justify-content-end">\
                                <input type="checkbox" name=isDoctor[' + room + '] data-size="medium" data-on-color="primary" data-off-color="info"\
                                data-on-text="Doctor" {{(Auth::user()->isDoctor()) ?"checked":""}}\
                                data-off-text="Clinic" id="isDoctor' + room + '" @if(Auth::user()->isDoctor()) readonly @endif>\
                            </div>\
                        </div>'


        concatString +=   '<div class="col-lg-3">\
                                    <div class="input-group mb-2">\
                                        <select name=device[' + room + '] class="form-control select2 custom-select" style="width:100%" id="device' + room + '" onchange="loadServices(' + room + ')" required>\
                                                <option disabled hidden selected value="" class="text-muted">Device</option>';
                                                @foreach($devices as $device)
                                                concatString +=   '<option value="{{ $device->id }}" > {{$device->DVIC_NAME}} </option>';
                                                @endforeach
        concatString +=                 '</select>\
                                    </div>\
                                </div>'

        concatString +=   '<div class="col-2">\
                            <div class="input-group mb-2">\
                                <select name=service[' + room + '] class="form-control select2 custom-select" style="width:100%" id="service' + room + '" onchange="checkUnit(' + room + ')" disabled required>\
                                </select>\
                            </div>\
                        </div>'
                        
        concatString += '<div class="col-3">\
                            <div class="input-group mb-3">\
                                <input id="note' + room + '" value="" type="text"  \class="form-control" placeholder="Note" name=note[' + room + '] >\
                            </div>\
                        </div>'

        concatString +='<div class="col-2">\
                            <div class="input-group mb-3">\
                                <input id="unit' + room + '" type="number" step="0.01" class="form-control amount" placeholder="Unit" name=unit[' + room + ']>\
                                    <div class="input-group-append">\
                                        <button class="btn btn-danger" type="button" onclick="removeService('+room+');"><i class="fa fa-minus"></i></button>\
                                    </div>\
                            </div>\
                        </div>'

   
        
        divtest.innerHTML = concatString;
        
        objTo.appendChild(divtest);
        $(".select2").select2()
        $(".bt-switch input[type='checkbox'], .bt-switch input[type='radio']").bootstrapSwitch();
                                
        room++
    }

    function removeService(rid) {
        $('.removeclass' + rid).remove();
    }
    @endif

    function checkUnit(rid) {
        rowName = $('#service' + rid).select2('data')[0].text
      
        if(rowName=="Pulse"){
            $('#unit' + rid).removeAttr('readonly')
            $('#unit' + rid).val('0')
            console.log(   $('#unit' + rid))
        } else {
            $('#unit' + rid).attr('readonly' , 'true')
            $('#unit' + rid).val('1') 
        }
    }

   function IsJsonString(str) {
        try {
            JSON.parse(str);
        } catch (e) {
            return false;
        }
        return true;
    }
</script>
@endsection