@extends('layouts.app')


@section('content')

    <div class="row">
        <!-- Column -->
        <div class="col-lg-4 col-xlg-3 col-md-5">
            <div class="card">
                <div class="card-body">
                    <small class="text-muted">Patient Code </small>
                    <h6>{{ $patient->id }}</h6>
                    <small class="text-muted">Patient Name </small>
                    <h6>{{ $patient->PTNT_NAME }}</h6>
                    <small class="text-muted p-t-30 db">Phone</small>
                    <h6>{{ $patient->PTNT_MOBN }}</h6>
                    <small class="text-muted p-t-30 db">Number of sessions</small>
                    <h6>{{ $patient->sessions_count }}</h6>
                    <small class="text-muted p-t-30 db">Patient Note</small>
                    <h6>{{ $patient->PTNT_NOTE }}</h6>
                    <small class="text-muted p-t-30 db">Packages</small>
                    @foreach ($patient->packageItems as $item)
                        @if ($item->PTPK_QNTY > 0)
                            <h6>{{ $item->pricelistItem->item_name }} : {{ $item->PTPK_QNTY }}</h6>
                        @endif
                    @endforeach
                    <small class="text-muted p-t-30 db">Since</small>
                    <h6>{{ $patient->created_at->format('Y-M-d') }}</h6>
                    <small class="text-muted p-t-30 db">Pricelist</small>
                    <h6>{{ $patient->pricelist->PRLS_NAME ?? '' }}</h6>
                    <small class="text-muted p-t-30 db">Channel</small>
                    <h6>{{ $patient->channel->CHNL_NAME ?? '' }}</h6>
                    <small class="text-muted p-t-30 db">Location</small>
                    <h6>{{ $patient->location->LOCT_NAME ?? '' }}</h6>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Paid</h5>
                    <div class="d-flex m-t-30 m-b-20 no-block align-items-center">
                        <span class="display-5 text-success"><i class=" ti-money"></i></span>
                        <a href="javscript:void(0)"
                            class="link display-5 ml-auto">{{ number_format($patient->totalPaid()) }}</a>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Current Balance</h5>
                    <div class="d-flex m-t-30 m-b-20 no-block align-items-center">
                        <span class="display-5 text-success"><i class=" ti-book"></i></span>
                        <a href="javscript:void(0)"
                            class="link display-5 ml-auto">{{ number_format($patient->PTNT_BLNC, 2) }}</a>
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
                    <li class="nav-item"> <a class="nav-link active" data-toggle="tab" href="#history"
                            role="tab">Sessions</a> </li>
                    <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#services" role="tab">Services</a>
                    </li>
                    <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#notes" role="tab">Notes</a>
                    </li>
                    <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#packages" role="tab">Packages</a>
                    </li>
                    <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#paid" role="tab">Account</a>
                    </li>
                    <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#pay" role="tab">Add
                            Payment</a> </li>
                    {{-- <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#addBalance" role="tab">Add Balance</a> </li> --}}
                    <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#settings" role="tab">Info</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#balancelogs" role="tab">
                            Balance Log</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#followups" role="tab">Follow-ups</a>
                    </li>
                </ul>
                <!-- Tab panes -->
                <div class="tab-content">

                    <!--Sessions tab-->
                    <div class="tab-pane active" id="history" role="tabpanel">
                        <div class="card-body">
                            <h4 class="card-title">Patient's Sessions History</h4>
                            <h6 class="card-subtitle">Total Money Paid: {{ $patient->totalPaid() }}, Discount offered:
                                {{ $patient->totalDiscount() }}</h6>
                            <div class="col-12">
                                <div class="table-responsive m-t-5">
                                    <table id="sessionsTable"
                                        class="table color-bordered-table table-striped full-color-table full-info-table hover-table"
                                        data-display-length='-1' data-order="[]">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Doctor</th>
                                                <th>Status</th>
                                                <th>Start</th>
                                                <th>End</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody id="sessionTableBody">
                                            @foreach ($patient->sessions as $session)
                                                <a href="javascript:void(0)">
                                                    <tr>
                                                        <td>{{ $session->SSHN_DATE->format('d-M-Y') }}</td>
                                                        <td>{{ $session->doctor->DASH_USNM ?? '' }}</td>
                                                        <td>
                                                            <a href="{{ url('sessions/details/' . $session->id) }}">
                                                                @switch($session->SSHN_STTS)
                                                                    @case('New')
                                                                        <?php $class = 'label label-info'; ?>
                                                                    @break

                                                                    @case('Pending Payment')
                                                                        <?php $class = 'label label-warning'; ?>
                                                                    @break

                                                                    @case('Cancelled')
                                                                        <?php $class = 'label label-danger'; ?>
                                                                    @break

                                                                    @case('Done')
                                                                        <?php $class = 'label label-success'; ?>
                                                                    @break
                                                                @endswitch
                                                                <button
                                                                    class="{{ $class }}">{{ $session->SSHN_STTS }}</button>
                                                            </a>
                                                        </td>
                                                        <td>{{ $session->SSHN_STRT_TIME }}</td>
                                                        <td>{{ $session->SSHN_END_TIME }}</td>
                                                        <td>{{ $session->getTotalAfterDiscount() }}</td>
                                                    </tr>
                                                </a>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!--Item Bought tab-->
                    <div class="tab-pane" id="services" role="tabpanel">
                        <div class="card-body">
                            <h4 class="card-title">Patient's Services</h4>
                            <h6 class="card-subtitle">All Services applied to the Patient</h6>
                            <div class="col-12">
                                <x-datatable id="patientServicesTable" :title="$title ?? 'Services History'" :subtitle="$subTitle ?? ''"
                                    :cols="$servicesCols" :items="$servicesList" :atts="$servicesAtts" :cardTitle="false" />
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane" id="notes" role="tabpanel">
                        <div class="card-body">
                            <h4 class="card-title">Patient's Notes</h4>
                            <h6 class="card-subtitle">Patient's Notes added by all users</h6>
                            <div class="col-12">
                                <form class="form pt-3" method="post" action="{{ $setNoteURL }}">
                                    @csrf
                                    <input name=id type="hidden" value="{{ $patient->id }}">
                                    <div class="form-group">
                                        <label>Note</label>
                                        <div class="input-group mb-3">
                                            <textarea class="form-control" rows="8" name="note">{{ old('note') }}</textarea>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-success mr-2">Submit</button>
                                </form>
                            </div>
                            <hr>
                            <h4 class="card-title">Patient's Notes</h4>
                            <ul class="list-group">
                                @foreach ($patient->notes as $note)
                                    <a href="javascript:void(0)"
                                        class="list-group-item list-group-item-action flex-column align-items-start">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h5 class="mb-1 text-dark">{{ $note->user->DASH_USNM }}</h5>
                                            <div>
                                                <small>Added on {{ $note->created_at }}</small>

                                                @if (Auth::user()->isOwner())
                                                    @if ($note->deleted_at)
                                                        Deleted on {{ $note->deleted_at }}
                                                        <button class="btn btn-success fas fa-trash-restore ml-2"
                                                            onclick="window.location.href='{{ $restoreNoteURL . '/' . $note->id }}'"></button>
                                                    @endif
                                                    <button class="btn btn-danger fas fa-trash-alt ml-2"
                                                        onclick="window.location.href='{{ $deleteNoteURL . '/' . $note->id }}'"></button>
                                                @else
                                                    <button class="btn btn-danger fas fa-eye-slash ml-2"
                                                        onclick="window.location.href='{{ $deleteNoteURL . '/' . $note->id }}'"></button>
                                                @endif

                                            </div>
                                        </div>
                                        <p class="mb-1">{{ $note->PNOT_NOTE }}</p>
                                    </a>
                                @endforeach
                            </ul>

                        </div>
                    </div>


                    <!--Patient Packages tab-->
                    <div class="tab-pane" id="packages" role="tabpanel">
                        <div class="card-body">
                            <div class=row>
                                <div class="mb-10 col-3 ">
                                    <button type="button" class="btn btn-success" onclick="addService()"
                                        @if (!Auth::user()->canAdmin()) disabled @endif>Add Service</button><br><br>
                                </div>
                            </div>
                            <form class="form " method="post" action="{{ url($addPackagesURL) }}">
                                @csrf
                                <input name=id value="{{ $patient->id }}" type="hidden">
                                <div class="form-group">
                                    <div class="col-3 d-flex align-items-center">
                                        <div class="custom-control custom-radio mr-5">
                                            <input type="radio" id="customRadio1" name="cashRadio"
                                                class="custom-control-input" value=cash required>
                                            <label class="custom-control-label" for="customRadio1">Cash</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" id="customRadio2" name="cashRadio"
                                                class="custom-control-input" value=visa required>
                                            <label class="custom-control-label" for="customRadio2">Visa</label>
                                        </div>
                                    </div>
                                </div>
                                @if (session('branch') == 0)
                                    <div class="col-lg-12 form-group">
                                        <label>Branch</label>
                                        <select class="select2 form-control  col-md-12 mb-3" style="width:100%"
                                            name=branchID>
                                            @foreach ($branches as $branch)
                                                <option value="{{ $branch->id }}"> {{ $branch->BRCH_NAME }}</option>
                                            @endforeach
                                        </select>
                                        <small class="text-danger">{{ $errors->first('branchID') }}</small>
                                    </div>
                                @elseif(session('branch') > 0)
                                    <input type="hidden" value="{{ session('branch') }}" name=branchID />
                                @else
                                    <p class="text-danger">Unable to find branch! Please select branch</p>
                                @endif
                                <div class=row>
                                    <div class="col-12">
                                        <div id="dynamicContainer">
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-success"
                                    @if (!Auth::user()->canAdmin()) disabled @endif>Submit</button><br><br>
                            </form>
                            <hr>
                            <div class=row>
                                <h4 class="card-title col-12">Patient Packages</h4>
                                <h6 class="card-subtitle col-12">All Packages extra packages owned by the Patient</h6>
                                <div class="col-12">
                                    <x-datatable id="patientPackagesTable" :title="'Packages'" :subtitle="''"
                                        :cols="$packagesCols" :items="$packagesList" :atts="$packagesAtts" :cardTitle="false" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane" id="paid" role="tabpanel">
                        <div class="row">
                            <div class="col-12">
                                <x-datatable id="patientPaymentsTable" :title="$payTitle" :subtitle="$paySubtitle"
                                    :cols="$payCols" :items="$pays" :atts="$payAtts" />
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane" id="pay" role="tabpanel">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="card-title">{{ $payFormTitle }}</h4>
                                        <form class="form pt-3" method="post" action="{{ url($payFormURL) }}"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <input type=hidden name=patientID
                                                value="{{ isset($patient) ? $patient->id : '' }}">
                                            <input type="hidden" name="goToHome" value="0">
                                            @if (session('branch') == 0)
                                                <div class="col-12 form-group">
                                                    <label>Branch</label>
                                                    <select class="select2 form-control  col-md-12 mb-3"
                                                        style="width:100%" name=branchID>
                                                        @foreach ($branches as $branch)
                                                            <option value="{{ $branch->id }}"> {{ $branch->BRCH_NAME }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <small class="text-danger">{{ $errors->first('branchID') }}</small>
                                                </div>
                                            @elseif(session('branch') > 0)
                                                <input type="hidden" value="{{ session('branch') }}" name=branchID />
                                            @else
                                                <p class="text-danger">Unable to find branch! Please select branch</p>
                                            @endif
                                            <div class="form-group">
                                                <label>Amount*</label>
                                                <div class="input-group mb-3">
                                                    <input type="number" step=0.01 class="form-control"
                                                        placeholder="Payment Amount" name=amount value="0" required>
                                                </div>
                                                <small class="text-danger">{{ $errors->first('amount') }}</small>
                                            </div>
                                            <div class="form-group">
                                                <div class="ml-10 col-9 d-flex">
                                                    <div class="bt-switch">
                                                        <div>
                                                            <input type="checkbox" data-size="medium"
                                                                data-on-color="success" data-off-color="primary"
                                                                data-on-text="Visa" data-off-text="Cash" name="isVisa">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label>Comment</label>
                                                <div class="input-group mb-3">
                                                    <textarea class="form-control" rows="2" name="comment" required></textarea>
                                                </div>

                                                <small class="text-danger">{{ $errors->first('comment') }}</small>
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
                            <h4 class="card-title">Edit {{ $patient->PTNT_NAME }}'s Info & Pricelist</h4>
                            <form class="form pt-3" method="post" action="{{ url($formURL) }}"
                                enctype="multipart/form-data">
                                @csrf
                                <input type=hidden name=id value="{{ isset($patient) ? $patient->id : '' }}">
                                <div class="form-group">
                                    <label>Full Name*</label>
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" placeholder="Full Name" name=name
                                            value="{{ isset($patient) ? $patient->PTNT_NAME : old('name') }}" required>
                                    </div>
                                    <small class="text-danger">{{ $errors->first('name') }}</small>
                                </div>


                                <div class="form-group">
                                    <label>Mobile Number*</label>
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" placeholder="Mobile Number" name=mobn
                                            value="{{ isset($patient) ? $patient->PTNT_MOBN : old('mob') }}" required>
                                    </div>
                                    <small class="text-danger">{{ $errors->first('mobn') }}</small>
                                </div>


                                <div class="form-group">
                                    <label>Pricelist*</label>
                                    <select class="select form-control  col-md-12 mb-3" style="width:100%" name=listID
                                        @if (!Auth::user()->isOwner()) readonly @endif>
                                        @foreach ($allPricelists as $list)
                                            <option value="{{ $list->id }}"
                                                @if ($list->id == $patient->PTNT_PRLS_ID) selected @elseif(!Auth::user()->isOwner()) disabled @endif>
                                                {{ $list->PRLS_NAME }} @if ($list->PRLS_DFLT)
                                                    (Default)
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Channel</label>
                                    <select class="select2 form-control  col-md-12 mb-3" style="width:100%" name=channelID
                                        @if (!Auth::user()->isOwner()) readonly @endif>
                                        @foreach ($channels as $channel)
                                            <option value="{{ $channel->id }}"
                                                @if ($channel->id == $patient->PTNT_CHNL_ID) selected @endif>
                                                {{ $channel->CHNL_NAME }} </option>
                                        @endforeach
                                    </select>
                                    <small class="text-danger">{{ $errors->first('channelID') }}</small>
                                </div>

                                <div class="form-group">
                                    <label>Location</label>
                                    <select class="select2 form-control  col-md-12 mb-3" style="width:100%"
                                        name=locationID>
                                        @foreach ($locations as $location)
                                            <option value="{{ $location->id }}"
                                                @if ($location->id == $patient->PTNT_LOCT_ID) selected @endif>
                                                {{ $location->LOCT_NAME }} </option>
                                        @endforeach
                                    </select>
                                    <small class="text-danger">{{ $errors->first('locationID') }}</small>
                                </div>

                                <div class="form-group">
                                    <label>Balance</label>
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" placeholder="Patient Balance"
                                            name=balance
                                            value="{{ isset($patient) ? $patient->PTNT_BLNC : old('balance') }}" required
                                            @if (!Auth::user()->isOwner()) readonly @endif>
                                    </div>
                                    <small class="text-danger">{{ $errors->first('balance') }}</small>
                                </div>

                                <div class="form-group">
                                    <label>Address</label>
                                    <div class="input-group mb-3">
                                        <textarea class="form-control" rows="3" name="address">{{ isset($patient) ? $patient->PTNT_ADRS : old('address') }}</textarea>
                                    </div>
                                    <small class="text-danger">{{ $errors->first('balance') }}</small>
                                </div>

                                <div class="form-group">
                                    <label>Note</label>
                                    <div class="input-group mb-3">
                                        <textarea class="form-control" rows="2" name="note">{{ isset($patient) ? $patient->PTNT_NOTE : old('note') }}</textarea>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-success mr-2">Submit</button>
                            </form>
                        </div>
                    </div>

                    <div class="tab-pane" id="log" role="tabpanel">
                        <div class="card-body">
                            <h4 class="card-title">Balance Logs</h4>
                            <h6 class="card-subtitle">Check all balance changes</h6>
                            <ul class="list-group">
                                @foreach ($patient->balanceLogs as $log)
                                    <a href="javascript:void(0)"
                                        class="list-group-item list-group-item-action flex-column align-items-start">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h5 class="mb-1 text-dark">{{ $log->user->DASH_USNM }}</h5>
                                            <small>{{ $log->created_at }}</small>
                                        </div>
                                        <p class="mb-1">{{ $log->BLLG_TTLE }}</p>
                                        <small>
                                            {{ $log->BLLG_CMNT }}
                                        </small>
                                    </a>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <div class="tab-pane" id="balancelogs" role="tabpanel">
                        <div class="card-body">
                            <h4 class="card-title">Patient's Balance Log</h4>
                            <h6 class="card-subtitle">Check all patient's balance Log</h6>

                            <ul class="list-group">
                                @foreach ($patient->packageLogs as $event)
                                    <a href="{{ $event->PKLG_SSHN_ID ? url('sessions/details/' . $event->PKLG_SSHN_ID) : 'javascript:void(0)' }}"
                                        @if ($event->PKLG_SSHN_ID) target=_blank @endif
                                        class="list-group-item
                                list-group-item-action flex-column align-items-start">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h5 class="mb-1 text-dark">{{ $event->PKLG_TTLE }}</h5>
                                            <small>{{ $event->user->DASH_USNM }} on {{ $event->created_at }}</small>
                                        </div>
                                        <p class="mb-1">{{ $event->PKLG_CMNT }}</p>
                                    </a>
                                @endforeach

                            </ul>
                        </div>
                    </div>

                    <!--Item Bought tab-->
                    <div class="tab-pane" id="followups" role="tabpanel">
                        <div class="card-body">
                            <h4 class="card-title">Patient's Followups</h4>
                            <h6 class="card-subtitle">All followups related to the Patient</h6>
                            <div class="col-12">
                                <x-datatable id="patientFollowupsTable" :title="$title ?? 'Followups History'" :subtitle="$subTitle ?? ''" :cols="$followupsCols" :items="$followupsList" :atts="$followupsAtts" :cardTitle="false" />
                            </div>
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
        $(function() {
            $(function() {

                var table = $('#sessionsTable').DataTable({
                    "displayLength": 25,
                    dom: 'Bfrtip',
                    buttons: [
                        @if (Auth::user()->isOwner())
                            {
                                extend: 'excel',
                                title: 'Flawless',
                                footer: true,
                                className: 'btn-info'
                            }
                        @endif
                    ]
                });
            })
        })
    </script>
@endsection

@section('js_content')
    <script type="text/javascript" src="{{ asset('assets/node_modules/multiselect/js/jquery.multi-select.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}">
    </script>
    <script type="text/javascript"
        src="{{ asset('assets/node_modules/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js') }}"></script>


    <script>
        function loadServices(row) {
            device = $('#device' + row).val();

            var http = new XMLHttpRequest();
            var url = "{{ url($getServicesAPI) }}";
            http.open('POST', url);
            http.setRequestHeader("Accept", "application/json");

            var formdata = new FormData();
            formdata.append('deviceID', device);
            formdata.append('patientID', {{ $patient->id }});
            formdata.append('_token', '{{ csrf_token() }}');



            http.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    try {
                        services = JSON.parse(this.responseText);
                        showServices(row, services)
                    } catch (e) {
                        console.log(e);
                    }
                }
            };
            http.send(formdata, true);

        }

        function showServices(row, services) {
            $('#service' + row).html("") //clearing the select
            $('#service' + row).removeAttr('disabled')
            services.forEach(service => {
                console.log(service)
                var newOption = new Option(service.serviceName, service.id, false, false);
                $('#service' + row).append(newOption).trigger('change');
            });

        }


        var room = 1;

        function addService() {

            var objTo = document.getElementById('dynamicContainer')
            var divtest = document.createElement("div");
            divtest.setAttribute("class", "row removeclass" + room);

            var concatString = "";

            concatString +=
                '<div class="col-lg-3">\
                                                        <div class="input-group mb-2">\
                                                            <select name=device[] class="form-control select2 custom-select" style="width:100%" id="device' +
                room + '" onchange="loadServices(' + room +
                ')" required>\
                                                                    <option disabled hidden selected value="" class="text-muted">Device</option>';
            @foreach ($devices as $device)
                concatString += '<option value="{{ $device->id }}" > {{ $device->DVIC_NAME }} </option>';
            @endforeach
            concatString += '</select>\
                                                        </div>\
                                                    </div>'

            concatString +=
                '<div class="col-3">\
                                                <div class="input-group mb-2">\
                                                    <select name=service[] class="form-control select2 custom-select" style="width:100%" id="service' +
                room + '"  disabled required>\
                                                    </select>\
                                                </div>\
                                            </div>'

            concatString += '<div class="col-2">\
                                                <div class="input-group mb-3">\
                                                    <input id="price' + room + '" type="number" step="0.01" class="form-control" placeholder="Price" name=price[] required>\
                                                </div>\
                                            </div>'

            concatString += '<div class="col-2">\
                                                <div class="input-group mb-3">\
                                                    <input id="unit' + room +
                '" type="number" step="1" class="form-control amount" placeholder="Unit" name=unit[] value=1 required>\
                                                        <div class="input-group-append">\
                                                            <button class="btn btn-danger" type="button" onclick="removeService(' +
                room + ');"><i class="fa fa-minus"></i></button>\
                                                        </div>\
                                                </div>\
                                            </div>'

            divtest.innerHTML = concatString;

            objTo.appendChild(divtest);
            $(".select2").select2()

            room++
        }

        function removeService(rid) {
            $('.removeclass' + rid).remove();
        }
    </script>
@endsection
