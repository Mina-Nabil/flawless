@extends('layouts.app')

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-3">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Services</h5>
                <div class="d-flex m-30 no-block align-items-center">
                    <span class="display-6 text-primary"><i class="far fa-calendar-plus"></i></span>
                    <a href="javascript:void(0)" class="link display-6 ml-auto">{{$servicesCount}}</a>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Paid</h6>
                <div class="d-flex m-t-30 m-b-20 no-block align-items-center">
                    <span class="display-6 text-danger"><i class="fas fa-hand-holding-usd"></i></span>
                    <a href="javascript:void(0)" class="link display-6 ml-auto">{{number_format($totalPaid)}}</a>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Shifts</h5>
                <div class="d-flex m-t-30 m-b-20 no-block align-items-center">
                    <span class="display-6 text-success"><i class=" far fa-calendar-plus"></i></span>
                    <a href="javascript:void(0)" class="link display-6 ml-auto">{{$totalShifts}}</a>
                </div>
            </div>
        </div>
    </div>


    <div class="col-lg-9">
        <div class=row>
            <div class="col-lg-12">

                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{$tableTitle}}</h4>
                        <h6 class="card-subtitle">{{$tableSubtitle}}</h6>

                        <hr>
                        <div class="table-responsive m-t-5">
                            <table id="sessionsTable" class="table color-bordered-table table-striped full-color-table full-info-table hover-table" data-display-length='10' data-order="[]">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Patient</th>
                                        <th>Status</th>
                                        <th>Service</th>
                                        <th>Paid</th>
                                    </tr>
                                </thead>
                                <tbody id="sessionTableBody">
                                    @foreach ($services as $service)
                                    <a href="javascript:void(0)">
                                        <tr>
                                            <td>{{$service->SSHN_DATE}}</td>
                                            <td>{{$service->PTNT_NAME}}</td>
                                            <td>
                                                <a href="{{ url('sessions/details/' . $service->SHIT_SSHN_ID)}}">
                                                    @switch($service->SSHN_STTS)
                                                    @case("New")
                                                    <?php $class="label label-info" ?>
                                                    @break
                                                    @case("Pending Payment")
                                                    <?php $class="label label-warning" ?>
                                                    @break
                                                    @case("Cancelled")
                                                    <?php $class="label label-danger" ?>
                                                    @break
                                                    @case("Done")
                                                    <?php $class="label label-success" ?>
                                                    @break
                                                    @endswitch
                                                    <button class="{{$class}}">{{ $service->SSHN_STTS }}</button>
                                                </a>
                                            </td>
                                            <td>
                                                {{$service->DVIC_NAME}} - {{$service->PLIT_TYPE}}
                                                @if($service->PLIT_TYPE=="Area") ({{$service->AREA_NAME}}) @endif</p>
                                            </td>

                                            <td>{{number_format($service->SHIT_TOTL)}}</td>
                                        </tr>
                                    </a>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Attendance Report</h4>
                        <div class="table-responsive m-t-5">
                            <table id="attendanceTable" class="table color-bordered-table table-striped full-color-table full-info-table hover-table" data-display-length='10' data-order="[]">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Shifts</th>
                                        <th>Comment</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($attendance as $attendaya)
                                    <tr>
                                        <td>{{$attendaya->ATND_DATE->format('d-M-Y')}}</td>
                                        <td>
                                            <button id="attendanceState{{$attendaya->id}}" @switch($attendaya->ATND_STTS)
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
                                                class="{{$class}} "
                                                disabled
                                                >{{ $attendaya->ATND_STTS }}
                                            </button>

                                        </td>
                                        <td>{{$attendaya->ATND_SHFT}}</td>
                                        <td>
                                            <button type="button" style="padding:.1rem" class="btn btn-secondary" data-container="body" data-toggle="popover" data-placement="bottom"
                                                data-content="{{$attendaya->ATND_CMNT }}" data-original-title="Comment">
                                                <div style="display: none">{{$attendaya->ATND_CMNT }}</div><i class="far fa-list-alt"></i>
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
    </div>

</div>
<script src="{{ asset('assets/node_modules/jquery/jquery-3.2.1.min.js') }}"></script>
<script src="{{ asset('assets/node_modules/datatables/datatables.min.js') }}"></script>
<script>
    $(function () {
            $(function () {

                var table = $('#sessionsTable').DataTable({
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