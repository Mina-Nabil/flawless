@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-2">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">New This Month</h5>
                    <div class="d-flex m-t-30 m-b-20 no-block align-items-center">
                        <span class="display-6 text-danger"><i class="fas fa-user-plus"></i></span>
                        <a href="javscript:void(0)" class="link display-6 ml-auto">{{ $thisMonth }}</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">New</h5>
                    <div class="d-flex m-t-30 m-b-20 no-block align-items-center">
                        <span class="display-6 text-info"><i class="fas fa-users"></i></span>
                        <a href="javscript:void(0)" class="link display-6 ml-auto">{{ $newCount }}</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Interested</h5>
                    <div class="d-flex m-t-30 m-b-20 no-block align-items-center">
                        <span class="display-6 text-dark"><i class="fas fa-users"></i></span>
                        <a href="javscript:void(0)" class="link display-6 ml-auto">{{ $interestedCount }}</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Patients</h5>
                    <div class="d-flex m-t-30 m-b-20 no-block align-items-center">
                        <span class="display-6 text-success"><i class="fas fa-users"></i></span>
                        <a href="javscript:void(0)" class="link display-6 ml-auto">{{ $patientsCount }}</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">All</h5>
                    <div class="d-flex m-t-30 m-b-20 no-block align-items-center">
                        <span class="display-6 text-white"><i class="fas fa-users"></i></span>
                        <a href="javscript:void(0)" class="link display-6 ml-auto">{{ $allCount }}</a>
                    </div>
                </div>
            </div>
        </div>



        <div class="col-md-2">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Follow-Ups</h5>
                    <div class="d-flex m-t-30 m-b-20 no-block align-items-center">
                        <span class="display-6 text-info"><i class="fas fa-street-view"></i></span>
                        <a target="_blank" href="{{ url('followups/leads') }}"
                            class="link display-6 ml-auto">{{ $followupsCount }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Leads Page</h4>

                    <!-- Nav tabs -->
                    <div class="vtabs">
                        <ul class="nav nav-tabs tabs-vertical" role="tablist">
                            <li class="nav-item ">
                                <a class="nav-link active" data-toggle="tab" href="#leads" role="tab"
                                    aria-selected="false">
                                    <span class="hidden-sm-up"><i class="ti-user"></i></span>
                                    <span class="hidden-xs-down">Lead</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#addLead" role="tab" aria-selected="false">
                                    <span class="hidden-sm-up">
                                        <i class="fas fa-user-plus"></i></span>
                                    <span class="hidden-xs-down">Add Lead</span>
                                </a>
                            </li>
                        </ul>
                        <!-- Tab panes -->
                        <div class="tab-content" style="min-height: 500px">
                            <div class="tab-pane active" id="leads" role="tabpanel">
                                <div class="row">
                                    @if (Auth::user()->isOwner())
                                    <div class="col-12">
                                        <div class="m-5">

                                            <a href="{{ url($download) }}" class="btn btn-info">Download Leads</a>
                                            <a href="{{ url($template) }}" class="btn btn-info">Download Import Template</a>
                                            <form method="POST" action="{{ url($import) }}" enctype="multipart/form-data">
                                                @csrf
                                                <div class="input-group m-3">
                                                    <input type="file" id="input-file-now-custom-1" name=import_file
                                                        class="dropify" />
                                                    <small class="text-danger">{{ $errors->first('import_file') }}</small>
                                                </div>
                                                <button type="submit" class="btn btn-info">Import Leads</button>
                                            </form>
                                        </div>
                                    </div>
                                    @endif
                                    <div class="col-12">
                                        <x-datatable id="leadsTable" :title="$leadsTitle" :subtitle="$leadsSubtitle" :cols="$leadsCols"
                                            :items="$leads" :atts="$leadsAtts" />
                                    </div>
                                </div>
                            </div>


                            <div class="tab-pane" id="addLead" role="tabpanel">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-body">
                                                <h4 class="card-title">{{ $addLeadsFormTitle }}</h4>
                                                <form class="form pt-3" method="post"
                                                    action="{{ url($addLeadsFormUrl) }}" enctype="multipart/form-data">
                                                    @csrf

                                                    <div class="form-group">
                                                        <label>Name*</label>
                                                        <div class="input-group mb-3">
                                                            <input type="text" class="form-control"
                                                                placeholder="Lead Name" name=name
                                                                value="{{ old('name') }}" required>
                                                        </div>
                                                        <small class="text-danger">{{ $errors->first('name') }}</small>
                                                    </div>

                                                    <div class="form-group">
                                                        <label>Mobile*</label>
                                                        <div class="input-group mb-3">
                                                            <input type="text" class="form-control"
                                                                placeholder="Mobile Number" name=mobn
                                                                value="{{ old('mobn') }}" required>
                                                        </div>
                                                        <small class="text-danger">{{ $errors->first('in') }}</small>
                                                    </div>

                                                    <div class="form-group">
                                                        <label>Promocode</label>
                                                        <div class="input-group mb-3">
                                                            <input type="text" class="form-control"
                                                                placeholder="Promocode" name=promo
                                                                value="{{ old('promo') }}">
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label>Address</label>
                                                        <div class="input-group mb-3">
                                                            <textarea class="form-control" rows="2" name="adrs" id="patientAdrs">{{ old('adrs') }}</textarea>
                                                        </div>

                                                        <small class="text-danger">{{ $errors->first('adrs') }}</small>
                                                    </div>

                                                    <div class="form-group">
                                                        <label>Note</label>
                                                        <div class="input-group mb-3">
                                                            <textarea class="form-control" rows="2" name="note">{{ old('note') }}</textarea>
                                                        </div>
                                                    </div>

                                                    <button type="submit" class="btn btn-success mr-2">Add Lead</button>

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
    {{-- ========================================================================================================== 
        Add Followup Modal 
    =============================================================================================================== --}}
    <div id="add-lead-followup" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add Lead Followup</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id=leadID />

                    <div class="form-group">
                        <label>Date</label>
                        <div class="input-group mb-3">
                            <input type="date" class="form-control" id="callDate" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Note</label>
                        <div class="input-group mb-3">
                            <textarea class="form-control" rows="2" id="leadNoteDate"></textarea>
                        </div>
                    </div>
                    <div class=row>
                        <div class=col-4>
                            <button type="button" onclick="addFollowup()" class="btn btn-success mr-2">Add
                                Followup</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- ========================================================================================================== 
        Set Status Modal 
    =============================================================================================================== --}}
    <div id="set-lead-status" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Set Lead Status</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id=leadID />

                    <div class="form-group">
                        <label>Status</label>
                        <select class="select form-control  col-md-12 mb-3" style="width:100%" id=leadStatus>
                            <option value="Interested">Interested</option>
                            <option value="Not-interested">Not-interested</option>
                            <option value="No-Answer">No-Answer</option>
                        </select>
                    </div>

                    <div class=row>
                        <div class=col-4>
                            <button type="button" onclick="setStatus()" class="btn btn-success mr-2">
                                Save</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection


@section('js_content')
    <script>
        $(document).on("click", ".openLeadFollowup", function() {
            var accessData = $(this).data('id');
            $(".modal-body #leadID").val(accessData);
        });

        $(document).on("click", ".openLeadStatus", function() {
            var accessData = $(this).data('id');
            $(".modal-body #leadID").val(accessData);
        });


        function setStatus() {
            var lead = $('#leadID').val();
            var leadStatus = $('#leadStatus').val();

            var formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append("leadID", lead)
            formData.append("status", leadStatus)

            var url = "{{ $setstatusFormUrl }}";

            var http = new XMLHttpRequest();
            http.open("POST", url);
            http.setRequestHeader("Accept", "application/json");

            http.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200 && IsNumeric(this.responseText)) {
                    $("#set-lead-status").modal('hide');
                    Swal.fire({
                        title: "Success!",
                        text: "State changed, please refresh to check changes",
                        icon: "success"
                    })

                } else if (this.readyState == 4 && this.status == 422 && isJson(this.responseText)) {
                    try {
                        var errors = JSON.parse(this.responseText)
                        var errorMesage = "";
                        if (errors.errors["leadID"]) {
                            errorMesage += errors.errors.leadID + " ";
                        }
                        if (errors.errors["message"]) {
                            errorMesage += errors.errors["message"] + " ";
                        }

                        errorMesage = errorMesage.replace('message', 'Message')
                        errorMesage = errorMesage.replace('leadID', 'Lead')

                        Swal.fire({
                            title: "Error!",
                            text: errorMesage,
                            icon: "error"
                        })
                    } catch (r) {
                        console.log(r)
                        Swal.fire({
                            title: "Error!",
                            text: "Oops Something went wrong! Please try again",
                            icon: "error"
                        })
                    }
                } else if (this.readyState == 4) {
                    Swal.fire({
                        title: "Error!",
                        text: "Oops Something went wrong! Please try again",
                        icon: "error"
                    })
                }
            }

            http.send(formData)
        }

        function addFollowup() {
            var lead = $('#leadID').val();
            var leadCallDate = $('#callDate').val();
            var leadNote = $('#leadNoteDate').val();

            var formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append("leadID", lead)
            formData.append("callDate", leadCallDate)
            formData.append("leadNote", leadNote)

            var url = "{{ $addLeadFollowup }}";

            var http = new XMLHttpRequest();
            http.open("POST", url);
            http.setRequestHeader("Accept", "application/json");

            http.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200 && IsNumeric(this.responseText)) {
                    Swal.fire({
                        title: "Success!",
                        text: "Follow up created successfully",
                        icon: "success"
                    })

                } else if (this.readyState == 4 && this.status == 422 && isJson(this.responseText)) {
                    try {
                        var errors = JSON.parse(this.responseText)
                        var errorMesage = "";
                        if (errors.errors["leadID"]) {
                            errorMesage += errors.errors.leadID + " ";
                        }
                        if (errors.errors["message"]) {
                            errorMesage += errors.errors["message"] + " ";
                        }

                        errorMesage = errorMesage.replace('message', 'Message')
                        errorMesage = errorMesage.replace('leadID', 'Lead')

                        Swal.fire({
                            title: "Error!",
                            text: errorMesage,
                            icon: "error"
                        })
                    } catch (r) {
                        console.log(r)
                        Swal.fire({
                            title: "Error!",
                            text: "Oops Something went wrong! Please try again",
                            icon: "error"
                        })
                    }
                } else if (this.readyState == 4) {
                    Swal.fire({
                        title: "Error!",
                        text: "Oops Something went wrong! Please try again",
                        icon: "error"
                    })
                }
            }

            http.send(formData)
        }
    </script>
@endsection
