@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Starting Balance</h5>
                <div class="d-flex m-t-30 m-b-20 no-block align-items-center">
                    <span class="display-6 text-info"><i class=" ti-agenda"></i></span>
                    <a href="javscript:void(0)" class="link display-6 ml-auto">{{number_format($startingBalance)}}</a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Total Paid</h5>
                <div class="d-flex m-t-30 m-b-20 no-block align-items-center">
                    <span class="display-6 text-danger"><i class="fas fa-hand-holding-usd"></i></span>
                    <a href="javscript:void(0)" class="link display-6 ml-auto">{{number_format($paidToday)}}</a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Total Collected</h5>
                <div class="d-flex m-t-30 m-b-20 no-block align-items-center">
                    <span class="display-6 text-success"><i class=" far fa-money-bill-alt"></i></span>
                    <a href="javscript:void(0)" class="link display-6 ml-auto">{{number_format($collectedToday)}}</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Balance</h5>
                <div class="d-flex m-t-30 m-b-20 no-block align-items-center">
                    <span class="display-6 text-primary"><i class="ti-money"></i></span>
                    <a href="javscript:void(0)" class="link display-6 ml-auto">{{number_format($balance)}}</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Cash Account</h4>
                <h6 class="card-subtitle">Check out all transactions and cash entries </h6>
                <!-- Nav tabs -->
                <div class="vtabs">
                    <ul class="nav nav-tabs tabs-vertical" role="tablist">
                        <li class="nav-item ">
                            <a class="nav-link active" data-toggle="tab" href="#today" role="tab" aria-selected="false">
                                <span class="hidden-sm-up"><i class="ti-calendar"></i></span>
                                <span class="hidden-xs-down">Today</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#trans" role="tab" aria-selected="false">
                                <span class="hidden-sm-up">
                                    <i class="ti-book"></i></span>
                                <span class="hidden-xs-down">Last 300</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#pay" role="tab" aria-selected="false">
                                <span class="hidden-sm-up">
                                    <i class="fas fa-hand-holding-usd"></i></span>
                                <span class="hidden-xs-down">Add Payment</span>
                            </a>
                        </li>
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div class="tab-pane active" id="today" role="tabpanel">
                            <div class="row">
                                <div class="col-12">
                                    <x-datatable id="myTable2" :title="$todayTitle" :subtitle="$todaySubtitle" :cols="$todayCols" :items="$todayTrans" :atts="$todayAtts" />
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="trans" role="tabpanel">
                            <div class="row">
                                <div class="col-12">
                                    <x-datatable id="myTable" :title="$transTitle" :subtitle="$transSubtitle" :cols="$transCols" :items="$trans" :atts="$transAtts" />
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane" id="pay" role="tabpanel">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <h4 class="card-title">{{ $formTitle }}</h4>
                                            <form class="form pt-3" method="post" action="{{ url($formURL) }}" enctype="multipart/form-data">
                                                @csrf

                                                <div class="form-group">
                                                    <label>Title*</label>
                                                    <div class="input-group mb-3">
                                                        <input type="text" class="form-control" placeholder="Transaction Title" name=title >
                                                    </div>
                                                    <small class="text-danger">{{$errors->first('title')}}</small>
                                                </div>

                                                <div class="form-group">
                                                    <label>In*</label>
                                                    <div class="input-group mb-3">
                                                        <input type="number" step=0.01 class="form-control" placeholder="Cash In Amount" name=in value="0" required>
                                                    </div>
                                                    <small class="text-danger">{{$errors->first('in')}}</small>
                                                </div>

                                                <div class="form-group">
                                                    <label>Out*</label>
                                                    <div class="input-group mb-3">
                                                        <input type="number" step=0.01 class="form-control" placeholder="Cash Out Amount" name=out value="0" required>
                                                    </div>
                                                    <small class="text-danger">{{$errors->first('out')}}</small>
                                                </div>

                                                <div class="form-group">
                                                    <label>Comment</label>
                                                    <div class="input-group mb-3">
                                                        <textarea class="form-control" rows="2" name="comment"></textarea>
                                                    </div>

                                                    <small class="text-danger">{{$errors->first('comment')}}</small>
                                                </div>

                                                <button type="submit" class="btn btn-success mr-2">Add Payment</button>

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

@endsection