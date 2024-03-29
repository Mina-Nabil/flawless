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
                                <th>Date</th>
                                <th>Doctor</th>
                                @if($showConfirmed)
                                <th>Approver</th>
                                @endif
                                <th>Status</th>
                                <th>Shifts</th>
                                <th>Comment</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $item)
                            <tr>
                                <td>{{$item->ATND_DATE->format('d-M-Y')}}</td>
                                <td>{{$item->doctor->DASH_USNM}}</td>
                                @if($showConfirmed)
                                <td>{{$item->accepter->DASH_USNM ?? ""}}</td>
                                @endif
                                <td>
                                    <button id="attendanceState{{$item->id}}" @switch($item->ATND_STTS)
                                        @case("New")
                                        <?php $class="label label-info " ?>
                                        @break
                                        @case("Confirmed")
                                        <?php $class="label label-success " ?>
                                        @break
                                        @case("Cancelled")
                                        <?php $class="label label-danger " ?>
                                        @break
                                        @endswitch
                                        class="{{$class}} open-setAttendance"
                                        @if($canChange)
                                        href="javascript:void(0)" data-toggle="modal" data-target="#set-status-modal"
                                        data-id="{{$item->id}}"
                                        @else
                                        disabled
                                        @endif
                                        >{{ $item->ATND_STTS }}
                                    </button>

                                </td>
                                <td>{{$item->ATND_SHFT}}</td>
                                <td>
                                    <button type="button" style="padding:.1rem" class="btn btn-secondary" data-container="body" data-toggle="popover" data-placement="bottom" data-content="{{$item->ATND_CMNT }}"
                                        data-original-title="Comment">
                                        <div style="display: none">{{$item->ATND_CMNT }}</div><i class="far fa-list-alt"></i>
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

@if($canChange)
<div id="set-status-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Set Status</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                {{-- <form action="{{url($setAttendanceURL)}}" method="POST"> --}}
                    @csrf
                    <input type="hidden" name=id id="attendanceID">

                    <div class="form-group">
                        <label>Status</label>
                        <select class="select form-control  col-md-12 mb-3" style="width:100%" name=status id=attendanceStateSel>
                            <option value="Confirmed">Confirmed</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>

                    <div class="form-group" style="display: block">
                        <label>Shifts</label>
                        <div class="input-group mb-3">
                            <input type="number" min=0 max=2 step=1 placeholder="1 Or 2 Shifts" value=1 class="form-control" id=numberOfShifts>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Comment</label>
                        <div class="input-group mb-3">
                            <textarea class="form-control" rows="2" id=attendanceComment name="comment"></textarea>
                        </div>
                    </div>

                    <button type="button" onclick="setAttendance()" class="btn btn-success mr-2">Set Status</button>
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
    @if($canChange)
    function setAttendance(){
            var id          =   $('#attendanceID').val();
            var state       =   $('#attendanceStateSel').val();
            var shifts       =   $('#numberOfShifts').val();
            var comment     =   $('#attendanceComment').val();

            var formData = new FormData();
            formData.append('_token','{{ csrf_token() }}');
            formData.append("id", id)
            formData.append("status", state)
            formData.append("shifts", shifts)
            formData.append("comment", comment)

            var url = "{{url($setAttendanceURL)}}";

            var http = new XMLHttpRequest();
            http.open("POST", url);
            http.setRequestHeader("Accept", "application/json");

            http.onreadystatechange = function (){
            if(this.readyState==4 && this.status == 200 && IsNumeric(this.responseText) ){
                Swal.fire({
                    title: "Success!",
                    text: "Attendance set successfully",
                    icon: "success"
                });
                if(state=="Confirmed"){
                    setAttendanceConfirmed(id)
                } else {
                    setAttendanceCancelled(id)
                }
           
            } else if(this.readyState=4 && this.status == 422 && isJson(this.responseText)) {
                try {
                    var errors = JSON.parse(this.responseText)
                    var errorMesage = "" ;
                    if(errors.errors["status"]){
                        errorMesage += errors.errors.status + " " ;
                    }
             
                    errorMesage = errorMesage.replace('status', 'Attendance Status')

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

        function setAttendanceConfirmed(id){
            $('#attendanceState' + id).attr("class", "label label-success")
            $('#attendanceState' + id).removeAttr("data-toggle")
            $('#attendanceState' + id).html("Confirmed")
        }


        function setAttendanceCancelled(id){
            $('#attendanceState' + id).attr("class", "label label-danger")
            $('#attendanceState' + id).removeAttr("data-toggle")
            $('#attendanceState' + id).html("Cancelled")
        }


        $(document).on("click", ".open-setAttendance", function () {
            var accessData = $(this).data('id');
            console.log(accessData)
            $(".modal-body #attendanceID").val( accessData );
        });
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