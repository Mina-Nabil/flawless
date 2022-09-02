@extends('layouts.app')

@section('content')

<div class=row>
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Filters</h4>
                <form method="GET">
                    <div class=row>
                        @if(session('branch')==0)
                        <div class="col-lg-4 form-group">
                            <label>Branch</label>
                            <select class="select2 form-control  col-md-12 mb-3" style="width:100%" name=branchID>
                                <option disabled selected>Select a branch</option>
                                @foreach($branches as $branch)
                                <option value="{{$branch->id}}" @if($branch->id == $selectedBranch) selected @endif
                                    > {{$branch->BRCH_NAME}}</option>
                                @endforeach
                            </select>
                        </div>
                        @elseif(session('branch')>0)
                        <input type="hidden" value="{{session('branch')}}" name=branchID readonly />
                        @else
                        <p class="text-danger">Unable to find branch! Please select branch</p>
                        @endif

                        <div class="col-lg-4 form-group">
                            <label>From*</label>
                            <div class="input-group mb-3">
                                <input type="date" class="form-control" name=from value="{{$selectedFrom ?? date('Y-m-d')}}" required>
                            </div>
                        </div>

                        <div class="col-lg-4 form-group">
                            <label>To*</label>
                            <div class="input-group mb-3">
                                <input type="date" class="form-control" name=to value="{{$selectedTo ?? date('Y-m-d')}}" required>
                            </div>
                        </div>

                        <div class="col-lg-4 form-group">
                            <label>Doctor</label>
                            <select class="select2 form-control  col-md-12 mb-3" style="width:100%" name=doctorID>
                                <option disabled selected>Select a doctor</option>
                                @foreach($doctors as $doctor)
                                <option value="{{$doctor->id}}" @if($doctor->id == $selectedDoctor) selected @endif
                                    > {{$doctor->DASH_USNM}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-4 form-group">
                            <label>Shift</label>
                            <select class="select2 form-control  col-md-12 mb-3" style="width:100%" name=shift>
                                <option disabled selected>Select a shift</option>
                                @foreach($shifts as $shift)
                                <option value="{{$shift}}" @if($shift==$selectedShift) selected @endif> {{$shift}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success mr-2">Filter</button>

            </div>
            </form>
        </div>
    </div>
</div>


<div class=row>
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Loaded Schedule Schedule</h4>

                <div class="table-responsive m-t-5">
                    <table data-toggle="table" data-mobile-responsive="true" class="table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Shift</th>
                                <th>Branch</th>
                                <th>Doctor</th>
                            </tr>
                        </thead>
                        <tbody id="sessionTableBody">
                            @if($branchesCount>0)
                            @foreach ($days as $date => $rec)

                            <tr>

                                <td rowspan={{$rec['dayRowSpan']}}>{{$date}}</td>
                                <td rowspan={{$rec['shift1RowSpan']}}>Shift 1</td>
                                @if(!$rec['shift1Count'])
                                <td>N/A</td>
                                <td>N/A</td>
                                @else
                                @foreach($rec['shift1'] as $shift)
                                @if ($loop->first)
                                <td>{{$shift['branch']->BRCH_NAME ?? "N/A"}}</td>
                                <td>{{$shift['doctor']->DASH_USNM ?? "N/A"}} @if( isset($shift['exception']) && $shift['exception']) (Exception)@endif</td>
                                @endif
                                @endforeach
                                @endif
                            </tr>
                            @if($rec['shift1Count']>1)
                            @foreach($rec['shift1'] as $shift)
                            @if (!$loop->first)
                            <tr>
                                <td>{{$shift['branch']->BRCH_NAME ?? $branch->BRCH_NAME}}</td>
                                <td>{{$shift['doctor']->DASH_USNM ?? "N/A"}} @if( isset($shift['exception']) && $shift['exception']) (Exception)@endif</td>
                            </tr>
                            @endif
                            @endforeach
                            @endif

                            <tr>
                                <td rowspan={{$rec['shift2RowSpan']}}>Shift 2</td>
                                @if(!$rec['shift2Count'])
                                <td>N/A</td>
                                <td>N/A</td>
                                @else
                                @foreach($rec['shift2'] as $shift)
                                @if ($loop->first)
                                <td>{{$shift['branch']->BRCH_NAME ?? "N/A"}}</td>
                                <td>{{$shift['doctor']->DASH_USNM ?? "N/A"}} @if( isset($shift['exception']) && $shift['exception']) (Exception)@endif</td>
                                @endif
                                @endforeach
                            </tr>

                            @foreach($rec['shift2'] as $shift)
                            @if (!$loop->first)
                            <tr>
                                <td>{{$shift['branch']->BRCH_NAME ?? $branch->BRCH_NAME}}</td>
                                <td>{{$shift['doctor']->DASH_USNM ?? "N/A"}} @if( isset($shift['exception']) && $shift['exception']) (Exception)@endif</td>
                            </tr>
                            @endif
                            @endforeach

                            @endif
                            @endforeach
                            @endif

                        </tbody>
                    </table>
                </div>




            </div>
        </div>
    </div>
</div>

@endsection