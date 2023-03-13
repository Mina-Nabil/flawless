@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">{{$cardTitle}}</h4>
                <h6 class="card-subtitle">{{$cardSubtitle}}</h6>
                <div class="table-responsive m-t-5">
                    <table id="attendanceTable" class="table color-bordered-table table-striped full-color-table full-info-table hover-table" data-display-length='-1' data-order="[]">
                        <thead>
                            <tr>
                                <th>Branch</th>
                                <th>Call On</th>
                                <th>Patient</th>
                                <th>Caller</th>
                                <th>On</th>
                                <th>Status</th>
                                {{-- <th>Comment</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $item)
                            <tr>
                                <td>{{$item->branch->BRCH_NAME}}</td>
                                <td>{{$item->FLUP_DATE->format('d-M-Y')}}</td>
                                <td><a href="{{$item->patient->profileURL()}}">{{$item->patient->PTNT_NAME}}</td>
                                <td>{{$item->caller->DASH_USNM ?? ""}}</td>
                                <td>{{$item->FLUP_CALL ?? ""}}</td>
                                <td>
                                    <button id="status{{$item->id}}" @switch($item->FLUP_STTS)
                                        @case($states[0])
                                        <?php $class="label label-info " ?>
                                        @break
                                        @case($states[1])
                                        <?php $class="label label-success " ?>
                                        @break
                                        @case($states[2])
                                        <?php $class="label label-danger " ?>
                                        @break
                                        @endswitch
                                        class="{{$class}} open-setFollowup"
                                        @if($canCall)
                                        href="javascript:void(0)" data-toggle="modal" data-target="#set-status-modal"
                                        data-id="{{$item->id}}"
                                        @else
                                        disabled
                                        @endif
                                        >{{ $item->FLUP_STTS }}
                                    </button>

                                </td>

                                {{-- <td>
                                    <button type="button" style="padding:.1rem" class="btn btn-secondary" data-container="body" data-toggle="popover" data-placement="bottom" data-content="{{$item->FLUP_TEXT }}
                                        Session Date: {{$item->session->SSHN_DATE->format('d-M-Y')}}
                                        Session Note: {{$item->session->SSHN_TEXT}}" data-original-title="Comment">
                                        <div style="display: none">{{$item->FLUP_TEXT }}
                                            Session Date: {{$item->session->SSHN_DATE->format('d-M-Y')}}
                                            Session Note: {{$item->session->SSHN_TEXT}}</div><i class="far fa-list-alt"></i>
                                    </button>
                                </td> --}}
                            </tr>
                            @endforeach
                            </body>

                    </table>

                </div>
            </div>
        </div>
    </div>
</div>

@if($canCall)
<div id="set-status-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Followup Call</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                {{-- <form action="{{url($setFollowupsURL)}}" method="POST"> --}}
                    @csrf
                    <input type="hidden" name=id id="followupID">

                    <div class="form-group">
                        <label>Satisfied?</label>
                        <div class="bt-switch">
                            <div>
                                <input type="checkbox" data-size="large" data-on-color="success" data-off-color="danger" data-on-text="Yes" id="isSatisfied" data-off-text="No" name="isCommission"
                                    onchange="switchChanged()" checked>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Comment</label>
                        <div class="input-group mb-3">
                            <textarea class="form-control" rows="2" id=commentText name="comment"></textarea>
                        </div>
                    </div>

                    <button type="button" onclick="setFollowup()" class="btn btn-success mr-2">Set Status</button>
                    {{--
                </form> --}}
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@section('js_content')
<script>
    $(document).on("click", ".open-setFollowup", function () {
        var accessData = $(this).data('id');
        $(".modal-body #followupID").val( accessData );
    });

    function switchChanged(){
        var isSet = $('#isSatisfied').is(':checked')
        console.log(isSet)
        if(isSet){
            $('#sessionDateDiv').css("display", "block")
        } else {
            $('#sessionDateDiv').css("display", "none")
        }
    }

    function setFollowup(){
        var id          =   $('#followupID').val();
        var isSatisfied   =   $('#isSatisfied').is(':checked');
        var comment     =   $('#commentText').val();

        // var date        =   $('#sessionDate').val();
        // var startTime   =   $('#sessionStartTime').val();
        // var endTime     =   $('#sessionEndTime').val();

        var formData = new FormData();
        formData.append('_token','{{ csrf_token() }}');
        formData.append("id", id)
        formData.append("status", isSatisfied)
        formData.append("comment", comment)

        // formData.append("sessionDate", date)
        // formData.append("sessionStartTime", startTime)
        // formData.append("sessionEndTime", endTime)

        var url = "{{url($setFollowupsURL)}}";

        var http = new XMLHttpRequest();
        http.open("POST", url);
        http.setRequestHeader("Accept", "application/json");

        http.onreadystatechange = function (){
        if(this.readyState==4 && this.status == 200 && IsNumeric(this.responseText) ){
            Swal.fire({
                title: "Success!",
                text: "Data Saved successfully",
                icon: "success"
            });
            if(isSatisfied){
                setFollowupConfirmed(id)
            } else {
                setFollowupCancelled(id)
            }
        
            } else if(this.readyState=4 && this.status == 422 && isJson(this.responseText)) {
                try {
                    var errors = JSON.parse(this.responseText)
                    var errorMesage = "" ;
                    
                    if(errors.errors["status"]){
                        errorMesage += errors.errors.status + " " ;
                    }

                    // if(errors.errors["sessionDate"]){
                    //     errorMesage += errors.errors.sessionDate + " " ;
                    // }

                    // if(errors.errors["sessionStartTime"]){
                    //     errorMesage += errors.errors.sessionStartTime + " " ;
                    // }

                    // if(errors.errors["sessionEndTime"]){
                    //     errorMesage += errors.errors.sessionEndTime + " " ;
                    // }
                
                    errorMesage = errorMesage.replace('status', 'Followup Status')
   

                    Swal.fire({
                        title: "Error!",
                        text: errorMesage ,
                        icon: "error"
                    })
                } catch (r){
                    console.log(r)
                    Swal.fire({
                        title: "Error!",
                        text: "Oops Something went wrong! Please try again",
                        icon: "error"
                    })
                }
                } else {
                    Swal.fire({
                        title: "Error!",
                        text: "Oops Something went wrong! Please try again",
                        icon: "error"
                    })
                }
            }

            http.send(formData)
        }


    function setFollowupConfirmed(id){
        $('#status' + id).attr("class", "label label-success")
        $('#status' + id).removeAttr("data-toggle")
        $('#status' + id).html("Satisfied")
    }


    function setFollowupCancelled(id){
        $('#status' + id).attr("class", "label label-danger")
        $('#status' + id).removeAttr("data-toggle")
        $('#status' + id).html("7azeen")
    }

    $(function () {
        $(function () {
            var table = $('#attendanceTable').DataTable({
                "displayLength": 25,
                dom: 'Bfrtip',
                buttons: [
                    @if(Auth::user()->isOwner())
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