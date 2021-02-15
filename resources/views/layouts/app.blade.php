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
    <title>{{  config('app.name', 'PetMatch') }}</title>
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
    <link href="{{ asset('assets/node_modules/select2/dist/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Oregano" />
    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Oswald" />

    <!-- Sweet Alert Notification -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
</head>

<body class="skin-default fixed-layout">
    <!-- ============================================================== -->
    <!-- Preloader - style you can find in spinners.css -->
    <!-- ============================================================== -->
    <div class="preloader">
        <div class="loader">
            <div class="loader__figure"></div>
            <p class="loader__label">{{ config('app.name', 'Laravel') }}</p>
        </div>
    </div>
    <!-- ============================================================== -->
    <!-- Main wrapper - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <div id="main-wrapper">
        <!-- ============================================================== -->
        <!-- Topbar header - style you can find in pages.scss -->
        <!-- ============================================================== -->
        <header class="topbar ">
            <nav class="navbar top-navbar navbar-expand-md navbar-dark">
                <!-- ============================================================== -->
                <!-- Logo -->
                <!-- ============================================================== -->
                <div class="navbar-header">
                    <a class="navbar-brand" href="{{route('home')}}">
                        <!-- Logo icon --><b>
                            <!--You can put here icon as well // <i class="wi wi-sunset"></i> //-->
                            <!-- Dark Logo icon -->
                            {{-- <img src="{{ asset('images/dark-logo.png') }}" height=40px alt="homepage" class="dark-logo" /> --}}
                            <!-- Light Logo icon -->
                            {{-- <img src="{{ asset('images/light-logo.png') }}" height=40px alt="homepage" class="light-logo" /> --}}
                        </b>
                        <!--End Logo icon -->
                        <!-- Logo text --><span>
                            <!-- dark Logo text -->
                            {{-- <img src="{{ asset('images/dark-text.png') }}" height=40px alt="homepage" class="dark-logo" /> --}}
                            <!-- Light Logo text -->
                            {{-- <img src="{{ asset('images/light-text.png') }}" height=40px class="light-logo" alt="homepage" /></span> </a> --}}
                </div>
                <!-- ============================================================== -->
                <!-- End Logo -->
                <!-- ============================================================== -->
                <div class="navbar-collapse">
                    <!-- ============================================================== -->
                    <!-- toggle and nav items -->
                    <!-- ============================================================== -->
                    <ul class="navbar-nav mr-auto">
                        <!-- This is  -->
                        <li class="nav-item"> <a class="nav-link nav-toggler d-block d-md-none waves-effect waves-dark" href="javascript:void(0)"><i class="ti-menu"></i></a> </li>
                        <li class="nav-item"> <a class="nav-link sidebartoggler d-none d-lg-block d-md-block waves-effect waves-dark" href="javascript:void(0)"><i class="icon-menu"></i></a> </li>
                        <!-- ============================================================== -->
                        <!-- Search -->
                        <!-- ============================================================== -->
                        <li class="nav-item">
                            <form class="app-search d-none d-md-block d-lg-block">
                                <input type="text" class="form-control" placeholder="Search & enter">
                            </form>
                        </li>
                    </ul>
                    <!-- ============================================================== -->
                    <!-- User profile and search -->
                    <!-- ============================================================== -->
                    <ul class="navbar-nav my-lg-0">
                        <!-- ============================================================== -->
                        <!-- Comment -->
                        <!-- ============================================================== -->
                        <li class="nav-item right-side-toggle"> <a class="nav-link  waves-effect waves-light" href="javascript:void(0)"><i class="ti-settings"></i></a></li>
                    </ul>
                </div>
            </nav>
        </header>
        <!-- ============================================================== -->
        <!-- End Topbar header -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
        <aside class="left-sidebar">
            <!-- Sidebar scroll-->
            <div class="scroll-sidebar">
                <!-- User Profile-->
                <div class="user-profile">
                    <div class="user-pro-body">
                        @if(isset(Auth::user()->image))
                        <div><img src="{{ asset( 'storage/'. Auth::user()->image ) }} " alt="user-img" class="img-circle"></div>
                        @else
                        <div><img src="{{ asset('assets/images/users/def-user.png') }} " alt="user-img" class="img-circle"></div>
                        @endif
                        <div class="dropdown">
                            <a href="javascript:void(0)" class="dropdown-toggle u-dropdown link hide-menu" data-toggle="dropdown" role="button" aria-haspopup="true"
                                aria-expanded="false">{{ Auth::user()->DASH_USNM }} <span class="caret"></span></a>
                            <div class="dropdown-menu animated flipInY">
                                <!-- text-->
                                <a href="{{route('logout')}}" class="dropdown-item"><i class="fa fa-power-off"></i>
                                    Logout</a>
                                <!-- text-->
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Sidebar navigation-->
                <nav class="sidebar-nav">
                    <ul id="sidebarnav">

                        <li> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="fas fa-list"></i><span class="hide-menu">الحسابات</span></a>
                            <ul aria-expanded="false" class="collapse">
                                <li>
                                    <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)">الخزينه</a>
                                    <ul aria-expanded="false" class="collapse">
                                        <li><a href="{{url('cash/show')}}">رصيد</a></li>
                                        <li><a href="{{url('cash/prepare/report')}}">كشف حساب</a></li>
                                        <li><a href="{{url('cash/add')}}">اضافه</a></li>
                                    </ul>
                                </li>
                                <li>
                                    <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)">البنك</a>
                                    <ul aria-expanded="false" class="collapse">
                                        <li><a href="{{url('bank/show')}}">رصيد</a></li>
                                        <li><a href="{{url('bank/prepare/report')}}">كشف حساب</a></li>
                                        <li><a href="{{url('bank/add')}}">اضافه</a></li>
                                    </ul>
                                </li>
                                <li><a href="{{url('transtype/show')}}">اعدادات</a></li>
                            </ul>
                        </li>

                        <li>
                            <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="fas fa-newspaper"></i><span class="hide-menu">حساب
                                    عام</span></a>
                            <ul aria-expanded="false" class="collapse">
                                <li><a href="{{url('ledger/show')}}">رصيد</a></li>
                                <li><a href="{{url('ledger/prepare/report')}}">كشف حساب</a></li>
                                <li><a href="{{url('ledger/add')}}">اضافه</a></li>
                                <li><a href="{{url('ledger/types/show')}}">انواع حسابات</a></li>
                            </ul>
                        </li>

                        <li> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="icon-people"></i><span class="hide-menu">الموردين</span></a>
                            <ul aria-expanded="false" class="collapse">
                                <li><a href="{{url('suppliers/trans/prepare')}}">كشف حساب</a></li>
                                <li><a href="{{url('suppliers/trans/quick')}}">تعاملات</a></li>
                                <li><a href="{{url('suppliers/show')}}">اجماليات موردين</a></li>
                                <li><a href="{{url('suppliers/add')}}">اضافه مورد</a></li>
                                <li><a href="{{url('suppliers/trans/add')}}">عمليه جديده</a></li>
                                <li><a href="{{url('suppliers/types/show')}}">انواع</a></li>
                            </ul>
                        </li>

                        <li> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="ti ti-package"></i><span class="hide-menu">مخزن
                                    قماش</span></a>
                            <ul aria-expanded="false" class="collapse">
                                <li><a href="{{url('rawinventory/show')}}">متاح قماش</a></li>
                                <li><a href="{{url('rawinventory/tran')}}">مجمع تعاملات</a></li>
                                <li><a href="{{url('rawinventory/add')}}">وارد جديد</a></li>
                                <li><a href="{{url('raw/tran/add')}}">عمليه جديده</a></li>

                                <li>
                                    <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)">اعدادات</a>
                                    <ul aria-expanded="false" class="collapse">
                                        <li><a href="{{url('models/show')}}">متاح مجمع موديلات</a></li>
                                        <li><a href="{{url('types/show')}}">اصناف خامات</a></li>
                                        <li><a href="{{url('colors/show')}}">الوان</a></li>
                                        <li><a href="{{url('raw/show')}}">انواع خامات</a></li>
                                    </ul>
                                </li>

                            </ul>
                        </li>


                        <li> <a href="{{url('raw/prod/show')}}" aria-expanded="false"><i class=" fas fa-cogs"></i><span class="hide-menu">انتاج </span></a>

                        </li>

                        <li> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="icon-people"></i><span class="hide-menu">Market</span></a>
                            <ul aria-expanded="false" class="collapse">
                                <li><a href="{{url('shops/show')}}">Shops</a></li>
                                <li><a href="{{url('products/show')}}">Products</a></li>
                                <li><a href="{{url('vets/show')}}">Vets</a></li>
                            </ul>
                        </li>

                        <li> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="fas fa-barcode"></i><span class="hide-menu">Settings</span></a>
                            <ul aria-expanded="false" class="collapse">
                                <li>
                                <li><a href="{{url('breeds/show')}}">Breeds</a></li>
                                <li><a href="{{url('cities/show')}}">Cities</a></li>
                        </li>
                    </ul>
                    </li>

                    <li> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="fas fa-boxes"></i><span class="hide-menu">Users</span></a>
                        <ul aria-expanded="false" class="collapse">
                            <li><a href="{{url('users/show')}}">All App Users</a></li>
                            <li><a href="{{url('users/show/1')}}">Pet Owners</a></li>
                            <li><a href="{{url('users/show/2')}}">Trainers</a></li>
                            <li><a href="{{url('users/add')}}">Add New User</a></li>
                        </ul>
                    </li>


                    <li> <a href="{{url('dash/users/all')}}" ><i class=" fas fa-users"></i>Admins</a>
                      
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
                        <h4 class="text-themecolor" style="font-family: 'Oregano' ; font-size:33px">PetMatch Dashboard</h4>
                    </div>
                    <div class="col-md-7 align-self-center text-right">
                        <div class="d-flex justify-content-end align-items-center">
                            <a style="font-family: 'Oswald'" href="{{url('clients/trans/add')}}" class="btn btn-info d-none d-lg-block m-l-15"><i class="fa fa-plus-circle"></i> Add Pet</a>
                            <a style="font-family: 'Oswald'" href="{{url('suppliers/trans/add')}}" class="btn btn-info d-none d-lg-block m-l-15"><i class="fa fa-plus-circle"></i> Add User</a>
                            <a style="font-family: 'Oswald'" href="{{url('sales/add')}}" class="btn btn-info d-none d-lg-block m-l-15"><i class="fa fa-plus-circle"></i> Add Shop
                            </a>
                            <a style="font-family: 'Oswald'" href="{{url('rawinventory/add')}}" class="btn btn-info d-none d-lg-block m-l-15"><i class="fa fa-plus-circle"></i> Add Vet
                            </a>
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
                <!-- End PAge Content -->
                <!-- ============================================================== -->
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
                                <li><a href="javascript:void(0)" data-skin="skin-default" class="default-theme working">1</a></li>
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
                                <li><a href="javascript:void(0)" data-skin="skin-blue-dark" class="blue-dark-theme">10</a></li>
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
            © 2019 {{config('app.name', 'PetMatch')}} by mSquareApps
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
        // $(function () {
        //     $(function () {

        //         var table = $('#myTable').DataTable({
        //             "displayLength": 25,
        //             dom: 'Bfrtip',
        //             buttons: [
        //                 {
        //                     extend: 'print',
        //                     text: 'Print',
        //                     title: 'Veneto',
        //                     footer: true,
        //                     messageTop: "Date: " + formatted,
        //                     customize: function (win) {
        //                         $(win.document.body)
        //                             .prepend('<center><img src="{{asset('images / dark - logo.png')}}" style="position:absolute; margin: auto; ; margin-top: 460px ; left: 0; right: 0; opacity:0.2" /></center>')
        //                             .css('font-size', '24px')

        //                         //$('#stampHeader' ).addClass( 'stampHeader' );
        //                         $(win.document.body).find('table')
        //                             .css('border', 'solid')
        //                             .css('margin-top', '20px')
        //                             .css('font-size', 'inherit');
        //                         $(win.document.body).find('th')
        //                             .css('border', 'solid')
        //                             .css('border', '!important')
        //                             .css('border-width', '1px')
        //                             .css('font-size', 'inherit')
        //                         $(win.document.body).find('td')
        //                             .css('border', 'solid')
        //                             .css('border', '!important')
        //                             .css('border-width', '1px');
        //                         $(win.document.body).find('tr')
        //                             .css('border', 'solid')
        //                             .css('border', '!important')
        //                             .css('border-width', '1px')
        //                     }
        //                 }, {
        //                     extend: 'excel',
        //                     title: 'Veneto',
        //                     footer: true,

        //                 }
        //             ]
        //         });
        //         var table = $('#myTable2').DataTable({
        //             "displayLength": 25,
        //             dom: 'Bfrtip',
        //             buttons: [
        //                 {
        //                     extend: 'print',
        //                     text: 'Print',
        //                     title: 'Veneto',
        //                     footer: true,
        //                     messageTop: "Date: " + formatted,
        //                     customize: function (win) {
        //                         $(win.document.body)
        //                             .prepend('<center><img src="{{asset('images / dark - logo.png')}}" style="position:absolute; margin: auto; ; margin-top: 460px ; left: 0; right: 0; opacity:0.2" /></center>')
        //                             .css('font-size', '24px')

        //                         //$('#stampHeader' ).addClass( 'stampHeader' );
        //                         $(win.document.body).find('table')
        //                             .css('border', 'solid')
        //                             .css('margin-top', '20px')
        //                             .css('font-size', 'inherit');
        //                         $(win.document.body).find('th')
        //                             .css('border', 'solid')
        //                             .css('border', '!important')
        //                             .css('border-width', '1px')
        //                             .css('font-size', 'inherit')
        //                         $(win.document.body).find('td')
        //                             .css('border', 'solid')
        //                             .css('border', '!important')
        //                             .css('border-width', '1px');
        //                         $(win.document.body).find('tr')
        //                             .css('border', 'solid')
        //                             .css('border', '!important')
        //                             .css('border-width', '1px')
        //                     }
        //                 }, {
        //                     extend: 'excel',
        //                     title: 'Veneto',
        //                     footer: true,

        //                 }
        //             ]
        //         });
        //         // Order by the grouping
        //         $('#example tbody').on('click', 'tr.group', function () {
        //             var currentOrder = table.order()[0];
        //             if (currentOrder[0] === 2 && currentOrder[1] === 'asc') {
        //                 table.order([2, 'desc']).draw();
        //             } else {
        //                 table.order([2, 'asc']).draw();
        //             }
        //         });
        //     });
        // });


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
</body>

</html>