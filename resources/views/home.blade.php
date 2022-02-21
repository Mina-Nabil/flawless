@extends('layouts.app')

@section('content')

<div class="row justify-content-center">
    @if(Auth::user()->isAdmin())
    <div class="col-lg-3">
        <div class=row>
            <div class=col-6>
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Follow-Ups </h5>
                        <div class="d-flex m-30 no-block align-items-center">
                            <span class="display-7 text-primary"><i class="fas fa-phone"></i></span>
                            <a href="{{url('followups/home')}}" class="link display-7 ml-auto">{{$followupsCount}}</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class=col-6>
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Feedbacks </h5>
                        <div class="d-flex m-30 no-block align-items-center">
                            <span class="display-7 text-success"><i class="fas fa-phone"></i></span>
                            <a href="{{url('feedbacks/home')}}" class="link display-7 ml-auto">{{$feedbacksCount}}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Unconfirmed Attendance</h5>
                <div class="d-flex m-30 no-block align-items-center">
                    <span class="display-6 text-primary"><i class="far fa-calendar-plus"></i></span>
                    <a href="{{url('attendance/home')}}" class="link display-6 ml-auto">{{$unconfirmedCount}}</a>
                </div>
            </div>
        </div>

        <div class=row>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Paid</h6>
                        <div class="d-flex m-t-30 m-b-20 no-block align-items-center">
                            <span class="display-7 text-danger"><i class="fas fa-hand-holding-usd"></i></span>
                            <a href="{{url('cash/home')}}" class="link display-7 ml-auto">{{number_format($paidToday)}}</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Cash</h5>
                        <div class="d-flex m-t-30 m-b-20 no-block align-items-center">
                            <span class="display-7 text-success"><i class=" far fa-money-bill-alt"></i></span>
                            <a href="{{url('cash/home')}}" class="link display-7 ml-auto">{{number_format($cashIn)}}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Today's Balance</h5>
                        <div class="d-flex m-30 no-block align-items-center">
                            <span class="display-6 text-primary"><i class="ti-money"></i></span>
                            <a href="{{url('attendance/home')}}" class="link display-6 ml-auto">{{number_format($collectedToday-$paidToday)}}</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Visa In</h5>
                        <div class="d-flex m-t-30 m-b-20 no-block align-items-center">
                            <span class="display-7 text-success"><i class=" fa fa-credit-card"></i></span>
                            <a href="{{url('visa/home')}}" class="link display-7 ml-auto">{{number_format($visaIn)}}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>
    @endif
    <div class="col-lg-9">
        <div class="card">
            <div class="card-header">{{(new DateTime)->format('F Y')}} Sessions</div>
            <div class="card-body">
                <div class="row m-b-0">
                    <!-- Column -->
                    <div class="col-lg-3 col-xlg-3 col-6 ">
                        <a href="{{$newSessionsURL}}">
                            <div class="card m-b-0">
                                <div class="box bg-info text-center">
                                    <h1 class="font-light text-white">{{$newSessionsCount}}</h1>
                                    <h6 class="text-white">New</h6>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-3 col-xlg-3 col-6 ">
                        <a href="{{$todaySessionsURL}}">
                            <div class="card m-b-0">
                                <div class="box bg-dark text-center">
                                    <h1 class="font-light text-light">{{$todaySessionsCount}}</h1>
                                    <h6 class="text-light">Today</h6>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-3 col-xlg-3 col-6 ">
                        <a href="{{$pendingSessionsURL}}">
                            <div class="card m-b-0">
                                <div class="box bg-warning text-center">
                                    <h1 class="font-light text-white">{{$pendingPaymentCount}}</h1>
                                    <h6 class="text-white">Pending</h6>
                                </div>
                            </div>
                        </a>
                    </div>
                    @if(Auth::user()->isAdmin())
                    <div class="col-lg-3 col-xlg-3 col-6 ">
                        <a href="{{$doneSessionsURL}}">
                            <div class="card m-b-0">
                                <div class="box bg-success text-center">
                                    <h1 class="font-light text-white">{{$doneSessionsCount}}</h1>
                                    <h6 class="text-white">Done</h6>
                                </div>
                            </div>
                        </a>
                    </div>
                    @endif
                </div>
                <hr>
                <div class="table-responsive m-t-5">
                    <table id="sessionsTable" class="table color-bordered-table table-striped full-color-table full-info-table hover-table" data-display-length='-1' data-order="[]">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Patient</th>
                                <th>Status</th>
                                <th>Start</th>
                                <th>End</th>
                                <th>Doctor</th>
                                @if(Auth::user()->isAdmin())
                                <th>Total</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody id="sessionTableBody">
                            @foreach ($sessions as $session)
                            <a href="javascript:void(0)">
                                <tr>
                                    <td>{{$session->SSHN_DATE->format('d-M-Y')}}</td>
                                    <td>{{$session->patient->PTNT_NAME}}</td>
                                    <td>
                                        <a href="{{ url('sessions/details/' . $session->id)}}">
                                            @switch($session->SSHN_STTS)
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
                                            <button class="{{$class}}">{{ $session->SSHN_STTS }}</button>
                                        </a>
                                    </td>
                                    <td>{{$session->SSHN_STRT_TIME}}</td>
                                    <td>{{$session->SSHN_END_TIME}}</td>
                                    <td>{{$session->doctor->DASH_USNM ?? ""}}</td>
                                    @if(Auth::user()->isAdmin())
                                    <td>{{$session->getTotalAfterDiscount()}}</td>
                                    @endif
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
            })
        })
</script>
@endsection