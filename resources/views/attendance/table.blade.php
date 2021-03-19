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
                                    <button @switch($item->ATND_STTS)
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
                                        @endif
                                        data-id="{{$item->id}}"
                                        >{{ $item->ATND_STTS }}
                                    </button>

                                </td>

                                <td>
                                    <button type="button" style="padding:.1rem" class="btn btn-secondary" data-container="body" data-toggle="popover" data-placement="bottom"
                                        data-content="{{$item->ATND_TEXT }}" data-original-title="Comment"> <i class="far fa-list-alt"></i>
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
                <form action="{{url($setAttendanceURL)}}" method="POST">
                    @csrf
                    <input type="hidden" name=id id="attendanceID">

                    <div class="form-group">
                        <label>Status</label>
                        <select class="select form-control  col-md-12 mb-3" style="width:100%" id=listIDModal>
                            <option value="Confirmed">Confirmed</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Comment</label>
                        <div class="input-group mb-3">
                            <textarea class="form-control" rows="2" name="comment"></textarea>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success mr-2">Set Status</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@section('js_content')
<script>
    $(document).on("click", ".open-setAttendance", function () {
        var accessData = $(this).data('id');
        console.log(accessData)
        $(".modal-body #attendanceID").val( accessData );
    });

    $(function () {
            $(function () {

                var table = $('#attendanceTable').DataTable({
                    "displayLength": 25,
                    dom: 'Bfrtip',
                    buttons: [
                        {
                            extend: 'excel',
                            title: 'Flawless',
                            footer: true,
                            className: 'btn-info'
                        }
                    ]
                });
            })
        })

</script>


@endsection