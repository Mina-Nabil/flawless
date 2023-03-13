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
                                <th>Session Date</th>
                                <th>Patient</th>
                                @if($showCaller)
                                <th>Rating</th>
                                <th>Caller</th>
                                <th>On</th>
                                @endif
                                <th>Status</th>
                                <th>Comment</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $item)
                            <tr>
                                <td>{{$item->branch->BRCH_NAME}}</td>
                                <td>{{$item->FDBK_DATE->format('d-M-Y')}}</td>
                                <td>{{$item->session->SSHN_DATE->format('d-M-Y')}}</td>
                                <td>{{$item->session->patient->PTNT_NAME}}</td>
                                @if($showCaller)
                                <td>{{$item->FDBK_OVRL ?? ""}}</td>
                                <td>{{$item->caller->DASH_USNM ?? ""}}</td>
                                <td>{{$item->FDBK_CALL ?? ""}}</td>
                                @endif
                                <td>
                                    <button id="status{{$item->id}}" @switch($item->FDBK_STTS)
                                        @case("New")
                                        <?php $class="label label-info " ?>
                                        @break
                                        @case("Called")
                                        <?php $class="label label-success " ?>
                                        @break
                                        @case("Cancelled")
                                        <?php $class="label label-danger " ?>
                                        @break
                                        @endswitch
                                        class="{{$class}} open-setFeedback"
                                        @if($canCall)
                                        href="javascript:void(0)" data-toggle="modal" data-target="#set-status-modal"
                                        data-id="{{$item->id}}"
                                        @else
                                        disabled
                                        @endif
                                        >{{ $item->FDBK_STTS }}
                                    </button>

                                </td>

                                <td>
                                    <button type="button" style="padding:.1rem" class="btn btn-secondary" data-container="body" data-toggle="popover" data-placement="bottom" data-content="{{$item->FDBK_TEXT }}" data-original-title="Comment">
                                        <div style="display: none">{{$item->FDBK_TEXT }}</div><i class="far fa-list-alt"></i>
                                    </button>
                                </td>
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
                <h4 class="modal-title">Feedback Call</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                {{-- <form action="{{url($setFeedbackURL)}}" method="POST"> --}}
                @csrf
                <input type="hidden" name=id id="feedbackID">

                <div class="form-group">
                    <label>Called Patient?</label>
                    <div class="bt-switch">
                        <div>
                            <input type="checkbox" data-size="large" data-on-color="success" data-off-color="danger" data-on-text="Yes" id="isCallSwitch" data-off-text="No" onchange="switchChanged()"
                                checked>
                        </div>
                    </div>
                </div>
                <div id="feedbackRatingDiv">
                    <div class="form-group" style="display: block">
                        <label>Patient Overall Satisfaction</label>
                        <div class="input-group mb-3">
                            <input type="number" min=0 max=10 step=1 placeholder="0 is the worst rating, 10 is the highest" class="form-control" id=feedbackOverall>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Extra Notes</label>
                    <div class="input-group mb-3">
                        <textarea class="form-control" rows="2" id=commentText name="comment"></textarea>
                    </div>
                </div>

                <button type="button" onclick="setFeedback()" class="btn btn-success mr-2">Set Status</button>
                {{-- </form> --}}
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@section('js_content')
<script>
    @if($canCall)
    $(document).on("click", ".open-setFeedback", function () {
        var accessData = $(this).data('id');
        $(".modal-body #feedbackID").val( accessData );
    });

    function switchChanged(){
        var isSet = $('#isCallSwitch').is(':checked')
        console.log(isSet)
        if(isSet){
            $('#feedbackRatingDiv').css("display", "block")
        } else {
            $('#feedbackRatingDiv').css("display", "none")
        }
    }

    function setFeedback(){
        var id          =   $('#feedbackID').val();
        var isCalled   =   $('#isCallSwitch').is(':checked');
        var comment     =   $('#commentText').val();
        var overall     =   $('#feedbackOverall').val();

        var formData = new FormData();
        formData.append('_token','{{ csrf_token() }}');
        formData.append("id", id)
        formData.append("status", isCalled)
        formData.append("overall", overall)
        formData.append("comment", comment)


        var url = "{{url($setFeedbackURL)}}";

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
            if(isCalled){
                setFeedbackConfirmed(id)
            } else {
                setFeedbackCancelled(id)
            }
        
            } else if(this.readyState=4 && this.status == 422 && isJson(this.responseText)) {
                try {
                    var errors = JSON.parse(this.responseText)
                    var errorMesage = "" ;
                    
                    if(errors.errors["status"]){
                        errorMesage += errors.errors.status + " " ;
                    }
                    
                    if(errors.errors["overall"]){
                        errorMesage += errors.errors.overall + " " ;
                    }
                
                    errorMesage = errorMesage.replace('status', 'Feedback Status')
                    errorMesage = errorMesage.replace('overall', 'Overall Rating')
   
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


    function setFeedbackConfirmed(id){
        $('#status' + id).attr("class", "label label-success")
        $('#status' + id).removeAttr("data-toggle")
        $('#status' + id).html("Called")
    }


    function setFeedbackCancelled(id){
        $('#status' + id).attr("class", "label label-danger")
        $('#status' + id).removeAttr("data-toggle")
        $('#status' + id).html("Cancelled")
    }
    @endif
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