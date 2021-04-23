@extends('layouts.app')

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-3">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Total In</h5>
                <div class="d-flex m-b-20  no-block align-items-center">
                    <span class="display-6 text-primary"><i class="fas fa-dollar-sign"></i></span>
                    <a href="javascript:void(0)" class="link display-6 ml-auto">{{number_format($sessionsTotal)}}EGP</a>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Total Discount</h6>
                <div class="d-flex m-b-20 no-block align-items-center">
                    <span class="display-6 text-danger"><i class="fas fa-hand-holding-usd"></i></span>
                    <a href="javascript:void(0)" class="link display-6 ml-auto">{{number_format($discountTotal)}}EGP</a>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Sessions Count</h5>
                <div class="d-flex m-t-30 m-b-20 no-block align-items-center">
                    <span class="display-6 text-success"><i class=" fas fa-tasks"></i></span>
                    <a href="javascript:void(0)" class="link display-6 ml-auto">{{$sessionsCount}}</a>
                </div>
            </div>
        </div>
    </div>


    <div class="col-lg-9">
        <div class=row>
            <div class="col-lg-12">

                <div class="card">
                    <div class="card-body">
                        <x-line-chart chartTitle="{{$chartTitle}}" chartSubtitle="{{$chartSubtitle}}" :graphs="$graphData" :max="$graphMax" :labels="$graphLabels" :totals="$graphTotal" />
                    </div>
                </div>
            </div>
        </div>

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
                                        <th>Doctor</th>
                                        <th>Paid</th>
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
                                            <td>{{$session->doctor->DASH_USNM}}</td>
                                            {{-- <td>
                                                @foreach ($session->items as $item)
                                                <p class="text-muted">{{$item->pricelistItem->device->DVIC_NAME}} - {{$item->pricelistItem->PLIT_TYPE}}
                                            @if($item->pricelistItem->PLIT_TYPE=="Area")
                                            ({{$item->pricelistItem->area->AREA_NAME}}) @endif</p>
                                            @endforeach
                                            </td> --}}

                                            <td>{{$session->getTotalAfterDiscount()}}</td>
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