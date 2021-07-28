<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ Session::token() }}">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/dark-logo.png') }}">
    <title>{{  config('app.name', 'Flawless') }}</title>
    <!-- Custom CSS -->
    <link href="{{asset('dist/css/style.min.css')}}" rel="stylesheet">
    <link href="{{asset('dist/css/style.min.css')}}" media=print rel="stylesheet">
    <!-- Datatable CSS -->
    <link href="{{ asset('assets/node_modules/datatables/media/css/dataTables.bootstrap4.css') }}" rel="stylesheet">
    <!-- BT Switch -->
    <link href="{{ asset('dist/css/pages/bootstrap-switch.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/node_modules/bootstrap-switch/bootstrap-switch.min.css') }}" rel="stylesheet">
    <!-- Form CSS -->
    <link href="{{ asset('dist/css/pages/file-upload.css')}}" rel="stylesheet">
    <link href="{{ asset('assets/node_modules/dropify/dist/css/dropify.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/node_modules/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/node_modules/toast-master/css/jquery.toast.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/node_modules/select2/dist/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Oregano" />
    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Oswald" />
    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Signika" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <script>
        Pusher.logToConsole = false;

        var pusher = new Pusher('a5d146a0877f34f3f89d', {
            cluster: 'eu'
        });

        var channel = pusher.subscribe('flawless-channel');

        channel.bind('my-event-' + '{{Auth::user()->id}}', function(data) {
         
            Swal.fire({
                title: "Message From " + data.from,
                text: JSON.stringify(data.message)});
        });
    </script>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
    @yield("header")
</head>

<body class="horizontal-nav skin-megna fixed-layout">
    <!-- ============================================================== -->
    <!-- Preloader - style you can find in spinners.css -->
    <!-- ============================================================== -->
    <div class="preloader">
        <div class="loader">
            <div class="loader__figure"></div>
            <p class="loader__label">{{ env('APP_NAME', 'Laravel') }}</p>
        </div>
    </div>
    <!-- ============================================================== -->
    <!-- Main wrapper - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <div id="main-wrapper">
        <!-- ============================================================== -->
        <!-- Topbar header - style you can find in pages.scss -->
        <!-- ============================================================== -->

        <!-- ============================================================== -->
        <!-- End Topbar header -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
        <aside class="left-sidebar">
            <!-- Sidebar scroll-->
            <div class="scroll-sidebar">

                <!-- Sidebar navigation-->
                <nav class="sidebar-nav">

                    <ul id="sidebarnav">
                        <nav class="navbar top-navbar navbar-expand-md navbar-dark">
                            <li class="nav-item"> <a class="nav-link nav-toggler d-block d-md-none waves-effect waves-dark" href="javascript:void(0)"><i class="ti-menu"></i></a> </li>
                        </nav>

                        <div class="navbar-header">
                            <a class="navbar-brand" href="{{route('home')}}">
                                <img src="{{ asset('images/logo.png') }}" height=60px alt="homepage" class="dark-logo" />
                            </a>
                        </div>

                        <li class="ml-auto"> <a class="waves-effect waves-dark" href="{{url('sessions/query')}}" aria-expanded="false"><i class="fas fa-list"></i>Sessions</a>

                            {{-- <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="fas fa-list"></i><span
                                    class="hide-menu">Sessions</span></a>
                            <ul aria-expanded="false" class="collapse">
                                <li><a href="{{url('sessions/query')}}">Show</a></li>
                    </ul> --}}
                    </li>
                    @if(Auth::user()->isAdmin())
                    <li> <a class="waves-effect waves-dark" href="{{url('calendar')}}" aria-expanded="false"><i class="fas fa-calendar-alt"></i>Calendar</a>
                    </li>
                    @endif
                    @if(Auth::user()->isAdmin())
                    <li> <a class="waves-effect waves-dark" href="{{url('patients/home')}}" aria-expanded="false"><i class="icon-people"></i>Patients</a>
                    </li>
                    @endif
                    @if(Auth::user()->isOwner())
                    <li>
                        <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="fas fa-newspaper"></i>Accounts</a>
                        <ul aria-expanded="false" class="collapse">
                            <li><a href="{{url('cash/home')}}">Cash</a></li>
                            <li><a href="{{url('visa/home')}}">Visa</a></li>
                        </ul>
                    </li>
                    @endif
                    @if(Auth::user()->isOwner())
                    <li> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="fas fa-chart-bar"></i><span class="hide-menu">Reports</span></a>
                        <ul aria-expanded="false" class="collapse">
                            <li><a href="{{url('reports/revenue')}}">Revenue</a></li>
                            <li>
                                <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)">Accounts</a>
                                <ul aria-expanded="false" class="collapse">
                                    <li><a href="{{url('reports/cash')}}">Cash</a></li>
                                    <li><a href="{{url('reports/visa')}}">Visa</a></li>
                                </ul>
                            </li>
                            <li><a href="{{url('reports/doctors')}}">Doctors</a></li>
                            <li><a href="{{url('attendance/query')}}">Attendance</a></li>
                            <li><a href="{{url('followups/query')}}">Follow-Ups</a></li>
                            <li><a href="{{url('feedbacks/query')}}">Feedbacks</a></li>
                            <li><a href="{{url('reports/devices')}}">Devices Revenue</a></li>
                            <li><a href="{{url('reports/missing')}}">Where is My Patient?</a></li>
                        </ul>
                    </li>
                    @elseif(Auth::user()->isDoctor())
                    <li> <a class="waves-effect waves-dark" href="{{url('reports/doctors')}}" aria-expanded="false"><i class="fas fa-chart-bar"></i>Doctor Report</a>
                        @endif
                        @if(Auth::user()->isAdmin())
                    <li> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="fas fa-cog"></i><span class="hide-menu">Settings</span></a>
                        <ul aria-expanded="false" class="collapse">
                            <li><a href="{{url('settings/devices')}}">Devices & Areas</a></li>
                            <li><a href="{{url('settings/pricelists')}}">Price Lists</a></li>
                            <li><a href="{{url('dash/users/3')}}">Owners</a></li>
                            <li><a href="{{url('dash/users/2')}}">Doctors</a></li>
                            <li><a href="{{url('dash/users/1')}}">Admins</a></li>
                            <li><a href="{{url('channels/home')}}">Channels</a></li>

                        </ul>
                    </li>
                    @endif
                    <li class="ml-auto" style="width: 320px">
                        <form class="app-search d-none d-md-block d-lg-block" action="{{$searchURL}}" method="POST">
                            @csrf
                            <input type="text" name=searchVal class="form-control" placeholder="Find patients by their Name or Mobile #">
                        </form>
                    </li>

                    <li class="ml-auto  ">
                        <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false">

                            @if(isset(Auth::user()->DASH_IMGE))
                            <img src="{{ asset( 'storage/'. Auth::user()->DASH_IMGE ) }} " class="img-circle" alt="user-img" style="height: 25px; width: 25px">
                            @else
                            <img src="{{ asset('assets/images/users/def-user.png') }} " class="img-circle" alt="user-img" style="height: 25px; width: 25px">
                            @endif


                            <span> &nbsp;{{Auth::user()->DASH_USNM}} &nbsp;</i></span>
                        </a>
                        <ul aria-expanded="false" class="collapse">
                            <!-- text-->
                            <a href="{{route('logout')}}"><i class="fa fa-power-off"></i>
                                Logout</a>
                            <!-- text-->
                        </ul>
                    </li>

                    </ul>

                </nav>
                <!-- End Sidebar navigation -->
            </div>
            <!-- End Sidebar scroll-->
        </aside>
        <!-- ============================================================== -->
        <!-- End Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Page wrapper  -->
        <!-- ============================================================== -->
        <div class="page-wrapper">
            <!-- ============================================================== -->
            <!-- Container fluid  -->
            <!-- ============================================================== -->
            <div class="container-fluid">
                <!-- ============================================================== -->
                <!-- Bread crumb and right sidebar toggle -->
                <!-- ============================================================== -->
                <div class="row page-titles">

                    <div class="col-md-5 align-self-center">
                        <div class=row>
                            <ul class="navbar-nav m-10">
                                <!-- This is  -->
                                <li class="nav-item"> <a class="nav-link nav-toggler d-block d-md-none waves-effect waves-dark" href="javascript:void(0)"><i class="ti-menu"></i></a> </li>

                            </ul>
                            <h4 class="text-themecolor m-10" style="font-family: 'Signika' ; font-size:33px">{{$title}}</h4>
                        </div>
                    </div>
                    <div class="col-md-7 align-self-center text-right">
                        <div class="d-flex justify-content-end align-items-center">
                            @if(Auth::user()->isAdmin())
                            <a style="font-family: 'Oswald'" href="javascript:void(0)" data-toggle="modal" data-target="#add-session-modal" class="btn btn-info m-b-5 m-l-15 addSessionButton"><i
                                    class="fa fa-plus-circle"></i> Book a Session</a>
                            <a style="font-family: 'Oswald'" href="javascript:void(0)" data-toggle="modal" data-target="#add-patient-modal" class="btn btn-info m-b-5 m-l-15"><i
                                    class="fa fa-plus-circle"></i> Add Patient</a>
                            @endif
                            <a style="font-family: 'Oswald'" href="javascript:void(0)" data-toggle="modal" data-target="#send-message" class="btn btn-info m-b-5 m-l-15"><i
                                    class="fa fa-plus-circle"></i> Send Msg. </a>
                            <a style="font-family: 'Oswald'" href="javascript:void(0)" data-toggle="modal" data-target="#add-attendance" class="btn btn-info m-b-5 m-l-15"><i
                                    class="fa fa-plus-circle"></i> Attendance </a>

                        </div>
                    </div>
                </div>
                <!-- ============================================================== -->
                <!-- End Bread crumb and right sidebar toggle -->
                <!-- ============================================================== -->
                <!-- ============================================================== -->
                <!-- Start Page Content -->
                <!-- ============================================================== -->
                @yield('content')
                <!-- ============================================================== -->
                <!-- MAIN MODALS -->
                <!-- ============================================================== -->
                <!-- ============================================================== -->
                <!-- ADD NEW PATIENT -->
                <!-- ============================================================== -->
                <div id="add-patient-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">Add New Patient</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            </div>
                            <div class="modal-body">

                                <div class="form-group">
                                    <label>Name*</label>
                                    <div class="input-group mb-3">
                                        <input type="text" id="patientNameModal" class="form-control" placeholder="Patient Name" name=name required>
                                    </div>
                                    <small class="text-danger">{{$errors->first('name')}}</small>
                                </div>

                                <div class="form-group">
                                    <label>Mobile*</label>
                                    <div class="input-group mb-3">
                                        <input type="text" id="patientMobnModal" class="form-control" placeholder="Patient Mobile Number" name=mobn required>
                                    </div>
                                    <small class="text-danger">{{$errors->first('mobn')}}</small>
                                </div>

                                <div class="form-group">
                                    <label>Pricelist*</label>
                                    <select class="select2 form-control  col-md-12 mb-3 modalSelect2" style="width:100%" id=listIDModal>
                                        @foreach($allPricelists as $list)
                                        <option value="{{$list->id}}" @if($list->PRLS_DFLT) selected @endif >
                                            {{$list->PRLS_NAME}} @if($list->PRLS_DFLT)(Default)@endif
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Channels*</label>
                                    <select class="select2 form-control  col-md-12 mb-3 modalSelect2" style="width:100%" id=channelIDModal>
                                        @foreach($channels as $channel)
                                        <option value="{{$channel->id}}">{{$channel->CHNL_NAME}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Balance*</label>
                                    <div class="input-group mb-3">
                                        <input type="number" id="patientBlncModal" step=0.01 class="form-control" placeholder="Patient Balance" name=balance value="0" required>
                                    </div>
                                    <small class="text-muted">In case patient owes us or we owe him money, default is 0</small>
                                    <small class="text-danger">{{$errors->first('balance')}}</small>
                                </div>

                                <div class="form-group">
                                    <label>Address</label>
                                    <div class="input-group mb-3">
                                        <textarea class="form-control" rows="2" name="adrs" id="patientAdrsModal"></textarea>
                                    </div>

                                    <small class="text-danger">{{$errors->first('adrs')}}</small>
                                </div>

                                <button type="button" onclick="addNewPatient(true)" class="btn btn-success mr-2">Add Patient</button>

                            </div>
                        </div>
                    </div>
                </div>

                <div id="add-attendance" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">Add Attendance</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            </div>
                            <div class="modal-body">

                                <div class="form-group">
                                    <label>Doctor</label>
                                    <select class="select2 form-control  col-md-12 mb-3 modalSelect2" style="width:100%" id="attendanceModalDoctor">
                                        @foreach($doctors as $doctor)
                                        <option value="{{$doctor->id}}"> {{$doctor->DASH_USNM}} </option>
                                        @endforeach
                                    </select>
                                </div>


                                <div class="form-group" style="display: block">
                                    <label>Shifts</label>
                                    <div class="input-group mb-3">
                                        <input type="number" min=0 max=2 step=1 placeholder="1 Or 2 Shifts" value=1 class="form-control" id=numberOfShiftsModal>
                                    </div>
                                </div>


                                <div class="form-group">
                                    <label>Date</label>
                                    <div class="input-group mb-3">
                                        <input type="date" value="{{date('Y-m-d')}}" id="attendanceModalDate" class="form-control" placeholder="Attendance Date" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Comment</label>
                                    <div class="input-group mb-3">
                                        <textarea class="form-control" rows="2" id="attendanceModalComment"></textarea>
                                    </div>
                                </div>

                                <button type="button" onclick="addAttendance()" class="btn btn-success mr-2">Add Attendance</button>

                            </div>
                        </div>
                    </div>
                </div>

                <div id="send-message" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">Send Message</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            </div>
                            <div class="modal-body">

                                <div class="form-group">
                                    <label>User</label>
                                    <select class="select2 form-control  col-md-12 mb-3 modalSelect2" style="width:100%" id="msgModalUser">
                                        @foreach($allUsers as $user)
                                        <option value="{{$user->id}}"> {{$user->DASH_USNM}} </option>
                                        @endforeach
                                    </select>
                                </div>


                                <div class="form-group">
                                    <label>Message</label>
                                    <div class="input-group mb-3">
                                        <textarea class="form-control" rows="2" id="msgModalMessage"></textarea>
                                    </div>
                                </div>

                                <button type="button" onclick="sendMessage()" class="btn btn-success mr-2">Send Message</button>

                            </div>
                        </div>
                    </div>
                </div>

                <!-- ============================================================== -->
                <!-- ADD NEW SESSION -->
                <!-- ============================================================== -->
                <div id="add-session-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">Book a Session</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            </div>
                            <div class="modal-body">

                                <div class="form-group col-md-12 m-t-0">
                                    <h5>Patients</h5>
                                    <select class="select2 form-control modalSelect2" style="width:100%" name=patientID id=patientSel>

                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Date*</label>
                                    <div class="input-group mb-3">
                                        <input type="date" id="sessionDate" class="form-control" placeholder="Session Day" name=sessionDate required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Start Time*</label>
                                    <div class="input-group mb-3">
                                        <input type="time" id="sessionStartTime" class="form-control" placeholder="Session Start Time" name=sessionStartTime required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>End Time*</label>
                                    <div class="input-group mb-3">
                                        <input type="time" id="sessionEndTime" class="form-control" placeholder="Session End Time" name=sessionEndTime required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Extra Notes</label>
                                    <div class="input-group mb-3">
                                        <textarea class="form-control" rows="2" name="sessionComment" id="sessionComment"></textarea>
                                    </div>
                                </div>

                                <button type="button" onclick="addNewSession()" class="btn btn-success mr-2">Add Session</button>

                            </div>
                        </div>
                    </div>
                </div>
                <!-- ============================================================== -->
                <!-- Right sidebar -->
                <!-- ============================================================== -->
                <!-- .right-sidebar -->
                <div class="right-sidebar">
                    <div class="slimscrollright">
                        <div class="rpanel-title"> Service Panel <span><i class="ti-close right-side-toggle"></i></span>
                        </div>
                        <div class="r-panel-body">
                            <ul id="themecolors" class="m-t-20">
                                <li><b>With Light sidebar</b></li>
                                <li><a href="javascript:void(0)" data-skin="skin-default" class="default-theme">1</a></li>
                                <li><a href="javascript:void(0)" data-skin="skin-green" class="green-theme">2</a></li>
                                <li><a href="javascript:void(0)" data-skin="skin-red" class="red-theme">3</a></li>
                                <li><a href="javascript:void(0)" data-skin="skin-blue" class="blue-theme">4</a></li>
                                <li><a href="javascript:void(0)" data-skin="skin-purple" class="purple-theme">5</a></li>
                                <li><a href="javascript:void(0)" data-skin="skin-megna" class="megna-theme">6</a></li>
                                <li class="d-block m-t-30"><b>With Dark sidebar</b></li>
                                <li><a href="javascript:void(0)" data-skin="skin-default-dark" class="default-dark-theme ">7</a></li>
                                <li><a href="javascript:void(0)" data-skin="skin-green-dark" class="green-dark-theme">8</a></li>
                                <li><a href="javascript:void(0)" data-skin="skin-red-dark" class="red-dark-theme">9</a>
                                </li>
                                <li><a href="javascript:void(0)" data-skin="skin-blue-dark" class="blue-dark-theme working">10</a></li>
                                <li><a href="javascript:void(0)" data-skin="skin-purple-dark" class="purple-dark-theme">11</a></li>
                                <li><a href="javascript:void(0)" data-skin="skin-megna-dark" class="megna-dark-theme ">12</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- ============================================================== -->
                <!-- End Right sidebar -->
                <!-- ============================================================== -->
            </div>
            <!-- ============================================================== -->
            <!-- End Container fluid  -->
            <!-- ============================================================== -->
        </div>
        <!-- ============================================================== -->
        <!-- End Page wrapper  -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- footer -->
        <!-- ============================================================== -->
        <footer class="footer">
            © 2021 {{config('app.name', 'Flawless')}} by mSquareApps
        </footer>
        <!-- ============================================================== -->
        <!-- End footer -->
        <!-- ============================================================== -->
    </div>
    <!-- ============================================================== -->
    <!-- End Wrapper -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- All Jquery -->
    <!-- ============================================================== -->
    <script src="{{ asset('assets/node_modules/jquery/jquery-3.2.1.min.js') }}"></script>
    <!-- Bootstrap tether Core JavaScript -->
    <script src="{{ asset('assets/node_modules/popper/popper.min.js') }}"></script>
    <script src="{{ asset('assets/node_modules/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <!-- slimscrollbar scrollbar JavaScript -->
    <script src="{{ asset('dist/js/perfect-scrollbar.jquery.min.js') }}"></script>
    <!--Wave Effects -->
    <script src="{{ asset('dist/js/waves.js') }}"></script>
    <!--Menu sidebar -->
    <script src="{{ asset('dist/js/sidebarmenu.js') }}"></script>
    <!--stickey kit -->
    <script src="{{ asset('assets/node_modules/sticky-kit-master/dist/sticky-kit.min.js') }}"></script>
    <script src="{{ asset('assets/node_modules/sparkline/jquery.sparkline.min.js') }}"></script>
    <!--Custom JavaScript -->
    <script src="{{ asset('dist/js/custom.min.js') }}"></script>
    <!-- This is data table -->
    <script src="{{ asset('assets/node_modules/datatables/datatables.min.js') }}"></script>
    <!-- This is for printing invoices -->
    <script src="{{ asset('dist/js/pages/jquery.PrintArea.js') }}" type="text/JavaScript"></script>
    <!-- This is for the bt switch -->
    <script src="{{ asset('assets/node_modules/bootstrap-switch/bootstrap-switch.min.js') }}"></script>
    <script src="{{ asset('assets/node_modules/toast-master/js/jquery.toast.js') }}"></script>

    <script type="text/javascript">
        $(".bt-switch input[type='checkbox'], .bt-switch input[type='radio']").bootstrapSwitch();
        var radioswitch = function () {
            var bt = function () {
                $(".radio-switch").on("switch-change", function () {
                    $(".radio-switch").bootstrapSwitch("toggleRadioState")
                }), $(".radio-switch").on("switch-change", function () {
                    $(".radio-switch").bootstrapSwitch("toggleRadioStateAllowUncheck")
                }), $(".radio-switch").on("switch-change", function () {
                    $(".radio-switch").bootstrapSwitch("toggleRadioStateAllowUncheck", !1)
                })
            };
            return {
                init: function () {
                    bt()
                }
            }
        }();
        $(document).ready(function () {
            radioswitch.init()
        });
    </script>
    <!-- start - This is for export functionality only -->
    <script src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"></script>

    <!-- End export script -->
    <!-- Form JS -->
    <script src="{{ asset('dist/js/pages/jasny-bootstrap.js') }}"></script>
    <script src="{{ asset('assets/node_modules/dropify/dist/js/dropify.min.js')}}"></script>
    <script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}" type="text/javascript">

    </script>

    <!-- Start Table Search Script -->
    <script>
        $(document).ready(function () {
            $("#print").click(function () {
                var mode = 'iframe'; //popup
                var close = mode == "popup";
                var options = {
                    mode: mode,
                    popClose: close
                };
                $("div.printableArea").printArea(options);
            });
        });


        $(function () {

            $(function () {
                var table = $('#example').DataTable({
                    "columnDefs": [{
                        "visible": false,
                        "targets": 2
                    }],
                    "order": [
                    ],
                    "displayLength": 25,
                    "drawCallback": function (settings) {
                        var api = this.api();
                        var rows = api.rows({
                            page: 'current'
                        }).nodes();
                        var last = null;
                        api.column(2, {
                            page: 'current'
                        }).data().each(function (group, i) {
                            if (last !== group) {
                                $(rows).eq(i).before('<tr class="group"><td colspan="5">' + group + '</td></tr>');
                                last = group;
                            }
                        });
                    }
                });
                // Order by the grouping
                $('#example tbody').on('click', 'tr.group', function () {
                    var currentOrder = table.order()[0];
                    if (currentOrder[0] === 2 && currentOrder[1] === 'asc') {
                        table.order([2, 'desc']).draw();
                    } else {
                        table.order([2, 'asc']).draw();
                    }
                });
            });
        });
        const d = new Date();
        const year = d.getFullYear(); // 2019
        const day = d.getDay();
        const month = d.getMonth();
        const formatted = day + "/" + month + "/" + year;
     

        $(' .buttons-print, .buttons-pdf, .buttons-excel').addClass('btn btn-info mr-1');
        // File Upload JS
        $(document).ready(function () {
            // Basic
            $('.dropify').dropify();
            // Translated
            $('.dropify-fr').dropify({
                messages: {
                    default: 'Glissez-déposez un fichier ici ou cliquez',
                    replace: 'Glissez-déposez un fichier ou cliquez pour remplacer',
                    remove: 'Supprimer',
                    error: 'Désolé, le fichier trop volumineux'
                }
            });
            // Used events
            var drEvent = $('#input-file-events').dropify();
            drEvent.on('dropify.beforeClear', function (event, element) {
                return confirm("Do you really want to delete \"" + element.file.name + "\" ?");
            });
            drEvent.on('dropify.afterClear', function (event, element) {
                alert('File deleted');
            });
            drEvent.on('dropify.errors', function (event, element) {
                console.log('Has Errors');
            });
            var drDestroy = $('#input-file-to-destroy').dropify();
            drDestroy = drDestroy.data('dropify')
            $('#toggleDropify').on('click', function (e) {
                e.preventDefault();
                if (drDestroy.isDropified()) {
                    drDestroy.destroy();
                } else {
                    drDestroy.init();
                }
            })
        });

        $(function () {
            // For select 2
            $(".select2").select2();

            $("#patientSel").select2({
                dropdownParent: $("#add-session-modal")
            });

            $("#listIDModal").select2({
                dropdownParent: $("#add-patient-modal")
            });

            $("#channelIDModal").select2({
                dropdownParent: $("#add-patient-modal")
            });

            $(".ajax").select2({
                ajax: {
                    url: "https://api.github.com/search/repositories",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term, // search term
                            page: params.page
                        };
                    },
                    processResults: function (data, params) {
                        // parse the results into the format expected by Select2
                        // since we are using custom formatting functions we do not need to
                        // alter the remote JSON data, except to indicate that infinite
                        // scrolling can be used
                        params.page = params.page || 1;
                        return {
                            results: data.items,
                            pagination: {
                                more: (params.page * 30) < data.total_count
                            }
                        };
                    },
                    cache: true
                },
                escapeMarkup: function (markup) {
                    return markup;
                }, // let our custom formatter work
                minimumInputLength: 1,
                //templateResult: formatRepo, // omitted for brevity, see the source of this page
                //templateSelection: formatRepoSelection // omitted for brevity, see the source of this page
            });
        });

  
    </script>
    <!-- End Table Search Script -->
    <script>
        function addNewPatient(isModal) {
            if(isModal){
                var name = $('#patientNameModal').val();
                var adrs = $('#patientAdrsModal').val();
                var mobn = $('#patientMobnModal').val();
                var balance = $('#patientBlncModal').val();
                var listID = $('#listIDModal').val();
                var channelID = $('#channelIDModal').val();
            } else {
                var name = $('#patientName').val();
                var adrs = $('#patientAdrs').val();
                var mobn = $('#patientMobn').val();
                var balance = $('#patientBlnc').val();
                var listID = $('#listID').val();
                var channelID = $('#channelID').val();
            }

            var formData = new FormData();
            formData.append('_token','{{ csrf_token() }}');
            formData.append("name", name)
            formData.append("adrs", adrs)
            formData.append("balance", balance)
            formData.append("mobn", mobn)
            formData.append("listID", listID)
            formData.append("channelID", channelID)

            var url = "{{$addPatientFormURL}}";

            var http = new XMLHttpRequest();
            http.open("POST", url);
            http.setRequestHeader("Accept", "application/json");

            http.onreadystatechange = function (){
            if(this.readyState==4 && this.status == 200 && IsNumeric(this.responseText) ){
                Swal.fire({
                    title: "Success!",
                    text: "Patient added successfully",
                    icon: "success"
                })
                    addPatientToTable(this.responseText, name, mobn, balance, adrs);  
                    resetPatientForm();  
                    $('#add-patient-modal').modal('toggle');   
            } else if(this.readyState==4 && this.status == 422 && isJson(this.responseText)) {
                try {
                    var errors = JSON.parse(this.responseText)
                    var errorMesage = "" ;
                    if(errors.errors["name"]){
                        errorMesage += errors.errors.name + " " ;
                    }
                    if(errors.errors["mobn"]){
                        errorMesage += errors.errors["mobn"] + " " ;
                    }

                    errorMesage = errorMesage.replace('mobn', 'Mobile Number')
                    errorMesage = errorMesage.replace('name', 'Patient Name')

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
                } else if(this.readyState==4) {
                    Swal.fire({
                        title: "Error!",
                        text: "Oops Something went wrong! Please try again",
                        icon: "error"
                    })
                }
            }

            http.send(formData)

        }

        function addPatientToTable(id, name, mobile, balance, address){
            var patientTable = $('#patientsTableBody')
            console.log(patientTable);
            $('#patientsTableBody').prepend("<tr>\
                  <td>" + id + "</td>\
                  <td><a href='{{url('patients/profile')}}/" + id + "' > " + name + "</a></td>\
                  <td>" + mobile + "</td>\
                  <td>" + balance + "</td>\
                  <td>\
                    <button type='button' style='padding:.1rem' class='btn btn-secondary' data-container='body' data-toggle='popover' data-placement='bottom'\
                        data-content='"+ address +"' data-original-title='Address:'> <i class='far fa-list-alt'></i>\
                    </button>\
                </td>\
                <td>Just Now</td>\
            </tr>\
            ")
        }

        function resetPatientForm(){
            var name = $('#patientName').val("");
            var adrs = $('#patientAdrs').val("");
            var mobn = $('#patientMobn').val("");
            var balance = $('#patientBlnc').val("0");
            var name = $('#patientNameModal').val("");
            var adrs = $('#patientAdrsModal').val("");
            var mobn = $('#patientMobnModal').val("");
            var balance = $('#patientBlncModal').val("0");
        }

        function addNewSession(){
            var id    = $('#patientSel').val();
            var date    = $('#sessionDate').val();
            var start   = $('#sessionStartTime').val();
            var end     = $('#sessionEndTime').val();
            var comment = $('#sessionComment').val();

            var formData = new FormData();
            formData.append('_token','{{ csrf_token() }}');
            formData.append("patientID", id)
            formData.append("sesDate", date)
            formData.append("start", start)
            formData.append("end", end)
            formData.append("comment", comment)

            var url = "{{$addSessionFormURL}}";

            var http = new XMLHttpRequest();
            http.open("POST", url);
            http.setRequestHeader("Accept", "application/json");

            http.onreadystatechange = function (){
            if(this.readyState==4 && this.status == 200 && IsNumeric(this.responseText) ){
                Swal.fire({
                    title: "Success!",
                    text: "Session added successfully",
                    icon: "success"
                })
                // append sessions table   
                resetSessionsForm()
            } else if(this.readyState==4 && this.status == 422 && isJson(this.responseText)) {
                try {
                    var errors = JSON.parse(this.responseText)
                    var errorMesage = "" ;
                    if(errors.errors["patientID"]){
                        errorMesage += errors.errors.patientID + " " ;
                    }
                    if(errors.errors["sesDate"]){
                        errorMesage += errors.errors["sesDate"] + " " ;
                    }
                    if(errors.errors["start"]){
                        errorMesage += errors.errors["start"] + " " ;
                    }
                    if(errors.errors["end"]){
                        errorMesage += errors.errors["end"] + " " ;
                    }

                    errorMesage = errorMesage.replace('sesDate', 'Session Date')
                    errorMesage = errorMesage.replace('patientID', 'Patient ID')
                    errorMesage = errorMesage.replace('start', 'Start Time')
                    errorMesage = errorMesage.replace('end', 'End Time')

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
                } else if(this.readyState==4){
                    Swal.fire({
                        title: "Error!",
                        text: "Oops Something went wrong! Please try again",
                        icon: "error"
                    })
                }
            }

            http.send(formData)
        }

        function resetSessionsForm(){
            $('#patientSel').val("");
            $('#sessionDate').val("");
            $('#sessionStartTime').val("");
            $('#sessionEndTime').val("");
            $('#sessionComment').val("");

        }

        var patients = [];
        var patientsProxy = new Proxy(patients, {
            set: function (target, key, value){
                patients = value;
                loadPatients();
                return true;
            }
        })

        function loadPatients(){
            $('#patientSel').html("null");

            patients.forEach(patient => {
                patOption = new Option(patient.PTNT_NAME, patient.id, false, false)
                $('#patientSel').append(patOption).trigger('change')
            });
           
        }

        function addAttendance(){
            var doctor      =   $('#attendanceModalDoctor').val();
            var date        =   $('#attendanceModalDate').val();
            var shifts      =   $('#numberOfShiftsModal').val();
            var comment     =   $('#attendanceModalComment').val();

            var formData = new FormData();
            formData.append('_token','{{ csrf_token() }}');
            formData.append("doctorID", doctor)
            formData.append("date", date)
            formData.append("shifts", shifts)
            formData.append("comment", comment)

            var url = "{{$addAttendanceURL}}";

            var http = new XMLHttpRequest();
            http.open("POST", url);
            http.setRequestHeader("Accept", "application/json");

            http.onreadystatechange = function (){
            if(this.readyState==4 && this.status == 200 && IsNumeric(this.responseText) ){
                Swal.fire({
                    title: "Success!",
                    text: "Attendance added successfully",
                    icon: "success"
                }) 
           
            } else if(this.readyState==4 && this.status == 422 && isJson(this.responseText)) {
                try {
                    var errors = JSON.parse(this.responseText)
                    var errorMesage = "" ;
                    if(errors.errors["doctorID"]){
                        errorMesage += errors.errors.doctorID + " " ;
                    }
                    if(errors.errors["date"]){
                        errorMesage += errors.errors["date"] + " " ;
                    }
                 
                    errorMesage = errorMesage.replace('date', 'Attendance Date')
                    errorMesage = errorMesage.replace('doctorID', 'Doctor ID')

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
                } else if(this.readyState==4){
                    Swal.fire({
                        title: "Error!",
                        text: "Oops Something went wrong! Please try again",
                        icon: "error"
                    })
                }
            }

            http.send(formData)
        }

        function sendMessage(){
            var user    =   $('#msgModalUser').val();
            var msg     =   $('#msgModalMessage').val();

            var formData = new FormData();
            formData.append('_token','{{ csrf_token() }}');
            formData.append("userID", user)
            formData.append("from", '{{Auth::user()->id}}')
            formData.append("message", msg)

            var url = "{{$sendMessageURL}}";

            var http = new XMLHttpRequest();
            http.open("POST", url);
            http.setRequestHeader("Accept", "application/json");

            http.onreadystatechange = function (){
            if(this.readyState==4 && this.status == 200 && IsNumeric(this.responseText) ){
                Swal.fire({
                    title: "Success!",
                    text: "Message sent successfully",
                    icon: "success"
                }) 
           
            } else if(this.readyState==4 && this.status == 422 && isJson(this.responseText)) {
                try {
                    var errors = JSON.parse(this.responseText)
                    var errorMesage = "" ;
                    if(errors.errors["userID"]){
                        errorMesage += errors.errors.userID + " " ;
                    }
                    if(errors.errors["message"]){
                        errorMesage += errors.errors["message"] + " " ;
                    }
                 
                    errorMesage = errorMesage.replace('message', 'Message')
                    errorMesage = errorMesage.replace('userID', 'User')

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
                } else if(this.readyState==4) {
                    Swal.fire({
                        title: "Error!",
                        text: "Oops Something went wrong! Please try again",
                        icon: "error"
                    })
                }
            }

            http.send(formData)
        }


        function addFollowup(){
            var patient     =   $('#followupModalPatient').val();
            var date        =   $('#followupModalDate').val();
            var comment     =   $('#followupModalComment').val();

            var formData = new FormData();
            formData.append('_token','{{ csrf_token() }}');
            formData.append("patientID", patient)
            formData.append("date", date)
            formData.append("comment", comment)

            var url = "{{$addFollowupURL}}";

            var http = new XMLHttpRequest();
            http.open("POST", url);
            http.setRequestHeader("Accept", "application/json");

            http.onreadystatechange = function (){
            if(this.readyState==4 && this.status == 200 && IsNumeric(this.responseText) ){
                Swal.fire({
                    title: "Success!",
                    text: "Followup added successfully",
                    icon: "success"
                }) 
           
            } else if(this.readyState==4 && this.status == 422 && isJson(this.responseText)) {
                try {
                    var errors = JSON.parse(this.responseText)
                    var errorMesage = "" ;
                    if(errors.errors["patientID"]){
                        errorMesage += errors.errors.patientID + " " ;
                    }
                    if(errors.errors["date"]){
                        errorMesage += errors.errors["date"] + " " ;
                    }
                 
                    errorMesage = errorMesage.replace('date', 'Follow Up Date')
                    errorMesage = errorMesage.replace('patientID', 'Patient')

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
                } else if(this.readyState==4) {
                    Swal.fire({
                        title: "Error!",
                        text: "Oops Something went wrong! Please try again",
                        icon: "error"
                    })
                }
            }

            http.send(formData)
        }

        function getPatientsArray() {
            var patients = []; 

            var url = "{{$getPatientsURL}}";

            var http = new XMLHttpRequest();
            http.open("GET", url);
            http.setRequestHeader("Accept", "application/json");

            http.onreadystatechange = function (){
                if(this.readyState==4 && this.status == 200 && isJson(this.responseText) ){
                    patientsProxy.patients = JSON.parse(this.responseText)
                }
            }

            http.send()

        }

        function IsNumeric(n) {
            return !isNaN(parseFloat(n)) && isFinite(n);
        }

        function isJson(str) {
            try {
                JSON.parse(str);
            } catch (e) {
                return false;
            }
            return true;
        }

        $(document).on("click", ".addSessionButton", function () {
  
            getPatientsArray();

        });



    </script>

    @yield('js_content')
    @stack('scripts')
</body>

</html>