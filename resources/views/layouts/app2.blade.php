<!DOCTYPE html>
<html dir="ltr" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="keywords"
          content="wrappixel, admin dashboard, html css dashboard, web dashboard, bootstrap 5 admin, bootstrap 5, css3 dashboard, bootstrap 5 dashboard, Ample lite admin bootstrap 5 dashboard, frontend, responsive bootstrap 5 admin template, Ample admin lite dashboard bootstrap 5 dashboard template">
    <meta name="description"
          content="Ample Admin Lite is powerful and clean admin dashboard template, inpired from Bootstrap Framework">
    <meta name="robots" content="noindex,nofollow">
    <title>Container-App Deployment&Management</title>
    <link rel="canonical" href="https://www.wrappixel.com/templates/ample-admin-lite/" />
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{asset('plugins/images/favicon.png')}}">
    <!-- Custom CSS -->
    <link href="{{asset('plugins/bower_components/chartist/dist/chartist.min.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('plugins/bower_components/chartist-plugin-tooltips/dist/chartist-plugin-tooltip.css')}}">
    <!-- Custom CSS -->
    <link href="{{asset('css/style.min.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('DataTables/datatables.css')}}">


    <style>
        ::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }

        /* Track */
        ::-webkit-scrollbar-track {
            box-shadow: inset 0 0 5px grey;
            border-radius: 10px;
        }

        /* Handle */
        ::-webkit-scrollbar-thumb {
            background: #000000AA;
            border-radius: 10px;
        }

        #editor, #jsonEditor, .aceEditor {
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            font-size: 14px;
        }

        #editorContainer {
            height: 60vh;
        }

        td {
            max-width: 10vw;
            overflow: auto;
        }

    </style>
</head>

<body>
<!-- ============================================================== -->
<!-- Preloader - style you can find in spinners.css -->
<!-- ============================================================== -->
<div class="preloader">
    <div class="lds-ripple">
        <div class="lds-pos"></div>
        <div class="lds-pos"></div>
    </div>
</div>
<!-- ============================================================== -->
<!-- Main wrapper - style you can find in pages.scss -->
<!-- ============================================================== -->
<div id="main-wrapper" data-layout="vertical" data-navbarbg="skin5" data-sidebartype="full"
     data-sidebar-position="absolute" data-header-position="absolute" data-boxed-layout="full">
    <!-- ============================================================== -->
    <!-- Topbar header - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <header class="topbar" data-navbarbg="skin5">
        <nav class="navbar top-navbar navbar-expand-md navbar-dark">
            <div class="navbar-header" data-logobg="skin6">
                <!-- ============================================================== -->
                <!-- Logo -->
                <!-- ============================================================== -->
                <a class="navbar-brand" href="{{ route('dashboard') }}" style="background: #2f323e; font-size: 16px">
                    <!-- Logo icon -->
{{--                    <b class="logo-icon">--}}
{{--                        <!-- Dark Logo icon -->--}}
{{--                        <img src="plugins/images/logo-icon.png" alt="homepage" />--}}
{{--                    </b>--}}
                    <!--End Logo icon -->
                    <!-- Logo text -->
{{--                    <span class="logo-text">--}}
{{--                            <!-- dark Logo text -->--}}
{{--                            <img src="plugins/images/logo-text.png" alt="homepage" />--}}
{{--                        </span>--}}
                    Container-App Deploy&Manage
                </a>
                <!-- ============================================================== -->
                <!-- End Logo -->
                <!-- ============================================================== -->
                <!-- ============================================================== -->
                <!-- toggle and nav items -->
                <!-- ============================================================== -->
                <a class="nav-toggler waves-effect waves-light text-dark d-block d-md-none"
                   href="javascript:void(0)"><i class="ti-menu ti-close"></i></a>
            </div>

            <div style="margin-left: 20px; display: inline-flex">
                <span class="text-white badge bg-primary" style="font-size: 1rem">Cluster: {{$selected_cluster_name??'Not Selected'}}</span>
            </div>


            <div style="margin-left: 20px; width: 23vw; display: inline-flex">
                <span class="text-white m-auto" style="padding-right: 8px">namespace:</span>
                <select id="selectedNamespace" name="selectedNamespace" class="form-select ml-3" aria-label="Namespace Select" onchange="onSelectNamespace(this)">
{{--                    <option selected value="default">default</option>--}}
{{--                    <option value="1">One</option>--}}
                    @foreach($namespaces as $ns)
                        @if(!is_string($ns))
                            <option value="{{$ns->getName()}}" {{!strcmp($_GET['namespace']??"default", $ns->getName()) ? 'selected' : ''}}>{{$ns->getName()}}</option>
                        @else
                            <option value="#">not selected</option>
                        @endif
                    @endforeach
                        <option value="all" {{!strcmp($_GET['namespace']??"no", "all") ? 'selected' : ''}}>ALL</option>

                </select>
            </div>
            <div style="margin-left: 20px; width: 20vw;">
                <a href="{{ route('create') }}" class="btn btn-success">Create</a>
            </div>
            <!-- ============================================================== -->
            <!-- End Logo -->
            <!-- ============================================================== -->
            <div class="navbar-collapse collapse" id="navbarSupportedContent" data-navbarbg="skin5">

                <!-- ============================================================== -->
                <!-- Right side toggle and nav items -->
                <!-- ============================================================== -->
                <ul class="navbar-nav ms-auto d-flex align-items-center">

                    <!-- ============================================================== -->
                    <!-- Search -->
                    <!-- ============================================================== -->
{{--                    <li class=" in">--}}
{{--                        <form role="search" class="app-search d-none d-md-block me-3">--}}
{{--                            <input type="text" placeholder="Search..." class="form-control mt-0">--}}
{{--                            <a href="" class="active">--}}
{{--                                <i class="fa fa-search"></i>--}}
{{--                            </a>--}}
{{--                        </form>--}}
{{--                    </li>--}}
                    <!-- ============================================================== -->
                    <!-- User profile and search -->
                    <!-- ============================================================== -->
                    @guest
                        @if (Route::has('login'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                        @endif

                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                            </li>
                        @endif
                    @else
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->name }}
                            </a>

                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <a href="{{route('edit-cluster')}}" class="dropdown-item">
                                    Edit Cluster
                                </a>
                                <a href="{{route('select-cluster')}}" class="dropdown-item">
                                    Select Cluster
                                </a>

                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    @endguest
{{--                    <li>--}}
{{--                        <a class="profile-pic" href="#">--}}
{{--                            <img src="plugins/images/users/varun.jpg" alt="user-img" width="36"--}}
{{--                                 class="img-circle"><span class="text-white font-medium">Steave</span></a>--}}
{{--                    </li>--}}
                    <!-- ============================================================== -->
                    <!-- User profile and search -->
                    <!-- ============================================================== -->
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
    <aside class="left-sidebar" data-sidebarbg="skin6" style="height: 100vh; overflow-x: hidden; overflow-y: auto">
        <!-- Sidebar scroll-->
        <div class="scroll-sidebar">
            <!-- Sidebar navigation-->
            <nav class="sidebar-nav">
                <ul id="sidebarnav">
                    <!-- User Profile-->
                    <li class="sidebar-item pt-2">
                        <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('dashboard', ['namespace'=>$_GET['namespace']??'default']) }}"
                           aria-expanded="false">
                            <i class="fa fa-briefcase" aria-hidden="true"></i>
                            <span class="hide-menu">Workloads</span>
                        </a>
                        <ul>
                            <li class="sidebar-item">
                                <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('dashboard', ['namespace'=>$_GET['namespace']??'default'])}}#deployment_table"
                                   aria-expanded="false" style="padding-left: 30px">
                                    <span class="hide-menu"><i class="fa fa-angle-right" aria-hidden="true"></i>Deployments</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('dashboard', ['namespace'=>$_GET['namespace']??'default']) }}#daemonsets_table"
                                   aria-expanded="false" style="padding-left: 30px">
                                    <span class="hide-menu"><i class="fa fa-angle-right" aria-hidden="true"></i>Daemon sets</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('dashboard', ['namespace'=>$_GET['namespace']??'default']) }}#jobs_table"
                                   aria-expanded="false" style="padding-left: 30px">
                                    <span class="hide-menu"><i class="fa fa-angle-right" aria-hidden="true"></i>Jobs</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('dashboard', ['namespace'=>$_GET['namespace']??'default']) }}#cronjobs_table"
                                   aria-expanded="false" style="padding-left: 30px">
                                    <span class="hide-menu"><i class="fa fa-angle-right" aria-hidden="true"></i>Cron Jobs</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('dashboard', ['namespace'=>$_GET['namespace']??'default']) }}#pods_table"
                                   aria-expanded="false" style="padding-left: 30px">
                                    <span class="hide-menu"><i class="fa fa-angle-right" aria-hidden="true"></i>Pods</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('dashboard', ['namespace'=>$_GET['namespace']??'default']) }}#replicasets_table"
                                   aria-expanded="false" style="padding-left: 30px">
                                    <span class="hide-menu"><i class="fa fa-angle-right" aria-hidden="true"></i>Replica Sets</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('dashboard', ['namespace'=>$_GET['namespace']??'default']) }}#statefulsets_table"
                                   aria-expanded="false" style="padding-left: 30px">
                                    <span class="hide-menu"><i class="fa fa-angle-right" aria-hidden="true"></i>Stateful Sets</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('service', ['namespace'=>$_GET['namespace']??'default']) }}"
                           aria-expanded="false">
                            <i class="fa fa-globe" aria-hidden="true"></i>
                            <span class="hide-menu">Service</span>
                        </a>
                        <ul>
                            <li class="sidebar-item">
                                <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('service', ['namespace'=>$_GET['namespace']??'default']) }}#services_table"
                                   aria-expanded="false" style="padding-left: 30px">
                                    <span class="hide-menu"><i class="fa fa-angle-right" aria-hidden="true"></i>Services</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('service', ['namespace'=>$_GET['namespace']??'default']) }}#ingresses_table"
                                   aria-expanded="false" style="padding-left: 30px">
                                    <span class="hide-menu"><i class="fa fa-angle-right" aria-hidden="true"></i>Ingresses</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('service', ['namespace'=>$_GET['namespace']??'default']) }}#ingressclasses_table"
                                   aria-expanded="false" style="padding-left: 30px">
                                    <span class="hide-menu"><i class="fa fa-angle-right" aria-hidden="true"></i>Ingress Classes</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('config_storage', ['namespace'=>$_GET['namespace']??'default']) }}"
                           aria-expanded="false">
                            <i class="fa fa-database" aria-hidden="true"></i>
                            <span class="hide-menu">Config and Storage</span>
                        </a>
                        <ul>
                            <li class="sidebar-item">
                                <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('config_storage', ['namespace'=>$_GET['namespace']??'default']) }}#configmaps_table"
                                   aria-expanded="false" style="padding-left: 30px">
                                    <span class="hide-menu"><i class="fa fa-angle-right" aria-hidden="true"></i>Config Maps</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('config_storage', ['namespace'=>$_GET['namespace']??'default']) }}#secrets_table"
                                   aria-expanded="false" style="padding-left: 30px">
                                    <span class="hide-menu"><i class="fa fa-angle-right" aria-hidden="true"></i>Secrets</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('config_storage', ['namespace'=>$_GET['namespace']??'default']) }}#pvcs_table"
                                   aria-expanded="false" style="padding-left: 30px">
                                    <span class="hide-menu"><i class="fa fa-angle-right" aria-hidden="true"></i>Persistent Volume Claims</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('config_storage', ['namespace'=>$_GET['namespace']??'default']) }}#storageclasses_table"
                                   aria-expanded="false" style="padding-left: 30px">
                                    <span class="hide-menu"><i class="fa fa-angle-right" aria-hidden="true"></i>Storage Classes</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('cluster', ['namespace'=>$_GET['namespace']??'default']) }}"
                           aria-expanded="false">
                            <i class="fa fa-star" aria-hidden="true"></i>
                            <span class="hide-menu">Cluster</span>
                        </a>
                        <ul>
                            <li class="sidebar-item">
                                <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('cluster', ['namespace'=>$_GET['namespace']??'default']) }}#namespaces_table"
                                   aria-expanded="false" style="padding-left: 30px">
                                    <span class="hide-menu"><i class="fa fa-angle-right" aria-hidden="true"></i>Namespaces</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('cluster', ['namespace'=>$_GET['namespace']??'default']) }}#nodes_table"
                                   aria-expanded="false" style="padding-left: 30px">
                                    <span class="hide-menu"><i class="fa fa-angle-right" aria-hidden="true"></i>Nodes</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('cluster', ['namespace'=>$_GET['namespace']??'default']) }}#persistentvolumes_table"
                                   aria-expanded="false" style="padding-left: 30px">
                                    <span class="hide-menu"><i class="fa fa-angle-right" aria-hidden="true"></i>Persistent Volumes</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('cluster', ['namespace'=>$_GET['namespace']??'default']) }}#clusterRoles_table"
                                   aria-expanded="false" style="padding-left: 30px">
                                    <span class="hide-menu"><i class="fa fa-angle-right" aria-hidden="true"></i>Cluster Roles</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('cluster', ['namespace'=>$_GET['namespace']??'default']) }}#clusterRoleBindings_table"
                                   aria-expanded="false" style="padding-left: 30px">
                                    <span class="hide-menu"><i class="fa fa-angle-right" aria-hidden="true"></i>Cluster Roles Bindings</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('cluster', ['namespace'=>$_GET['namespace']??'default']) }}#events_table"
                                   aria-expanded="false" style="padding-left: 30px">
                                    <span class="hide-menu"><i class="fa fa-angle-right" aria-hidden="true"></i>Events</span>
                                </a>
                            </li>
{{--                            <li class="sidebar-item">--}}
{{--                                <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('cluster', ['namespace'=>$_GET['namespace']??'default']) }}#networkPolicies_table"--}}
{{--                                   aria-expanded="false" style="padding-left: 30px">--}}
{{--                                    <span class="hide-menu"><i class="fa fa-angle-right" aria-hidden="true"></i>Network Policies</span>--}}
{{--                                </a>--}}
{{--                            </li>--}}
                            <li class="sidebar-item">
                                <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('cluster', ['namespace'=>$_GET['namespace']??'default']) }}#serviceAccounts_table"
                                   aria-expanded="false" style="padding-left: 30px">
                                    <span class="hide-menu"><i class="fa fa-angle-right" aria-hidden="true"></i>Service Accounts</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('cluster', ['namespace'=>$_GET['namespace']??'default']) }}#roles_table"
                                   aria-expanded="false" style="padding-left: 30px">
                                    <span class="hide-menu"><i class="fa fa-angle-right" aria-hidden="true"></i>Roles</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('cluster', ['namespace'=>$_GET['namespace']??'default']) }}#roleBindings_table"
                                   aria-expanded="false" style="padding-left: 30px">
                                    <span class="hide-menu"><i class="fa fa-angle-right" aria-hidden="true"></i>Role Bindings</span>
                                </a>
                            </li>
                        </ul>
                    </li>
{{--                    <li class="sidebar-item">--}}
{{--                        <a class="sidebar-link waves-effect waves-dark sidebar-link" href="map-google.html"--}}
{{--                           aria-expanded="false">--}}
{{--                            <i class="fa fa-globe" aria-hidden="true"></i>--}}
{{--                            <span class="hide-menu">Google Map</span>--}}
{{--                        </a>--}}
{{--                    </li>--}}
{{--                    <li class="sidebar-item">--}}
{{--                        <a class="sidebar-link waves-effect waves-dark sidebar-link" href="blank.html"--}}
{{--                           aria-expanded="false">--}}
{{--                            <i class="fa fa-columns" aria-hidden="true"></i>--}}
{{--                            <span class="hide-menu">Blank Page</span>--}}
{{--                        </a>--}}
{{--                    </li>--}}
{{--                    <li class="sidebar-item">--}}
{{--                        <a class="sidebar-link waves-effect waves-dark sidebar-link" href="404.html"--}}
{{--                           aria-expanded="false">--}}
{{--                            <i class="fa fa-info-circle" aria-hidden="true"></i>--}}
{{--                            <span class="hide-menu">Error 404</span>--}}
{{--                        </a>--}}
{{--                    </li>--}}
{{--                    <li class="text-center p-20 upgrade-btn">--}}
{{--                        <a href="https://www.wrappixel.com/templates/ampleadmin/"--}}
{{--                           class="btn d-grid btn-danger text-white" target="_blank">--}}
{{--                            Upgrade to Pro</a>--}}
{{--                    </li>--}}
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
    <div class="page-wrapper overflow-auto" style="height: calc(100vh - 60px)">
        <!-- ============================================================== -->
        <!-- Bread crumb and right sidebar toggle -->
        <!-- ============================================================== -->
        @include('result.alert')

        @yield('content')
        <!-- ============================================================== -->
        <!-- End Container fluid  -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- footer -->
        <!-- ============================================================== -->
{{--        <footer class="footer text-center"> 2021 © Ample Admin brought to you by <a--}}
{{--                href="https://www.wrappixel.com/">wrappixel.com</a>--}}
{{--        </footer>--}}
        <!-- ============================================================== -->
        <!-- End footer -->
        <!-- ============================================================== -->
    </div>
    <!-- ============================================================== -->
    <!-- End Page wrapper  -->
    <!-- ============================================================== -->
</div>
<!-- ============================================================== -->
<!-- End Wrapper -->
<!-- ============================================================== -->
<!-- ============================================================== -->

<!-- All Jquery -->
<!-- ============================================================== -->
<script src="{{asset('plugins/bower_components/jquery/dist/jquery.min.js')}}"></script>
<!-- Bootstrap tether Core JavaScript -->
<script src="{{ asset('plugins/bower_components/popper.js/dist/popper.min.js') }}"></script>
<script src="{{asset('bootstrap/dist/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset('js/app-style-switcher.js')}}"></script>
<script src="{{asset('plugins/bower_components/jquery-sparkline/jquery.sparkline.min.js')}}"></script>
<!--Wave Effects -->
<script src="{{asset('js/waves.js')}}"></script>
<!--Menu sidebar -->
<script src="{{asset('js/sidebarmenu.js')}}"></script>
<!--Custom JavaScript -->
<script src="{{asset('js/custom.js')}}"></script>
<!--This page JavaScript -->
<!--chartis chart-->
<script src="{{asset('plugins/bower_components/chartist/dist/chartist.min.js')}}"></script>
<script src="{{asset('plugins/bower_components/chartist-plugin-tooltips/dist/chartist-plugin-tooltip.min.js')}}"></script>
<script src="{{asset('js/pages/dashboards/dashboard1.js')}}"></script>
<script src="{{ asset('js/src-noconflict/ace.js') }}"></script>
<script src="{{asset('DataTables/datatables.js')}}"></script>
{{--<script src="{{ asset('js/prism.js') }}"></script>--}}

<script>


    function onSelectNamespace(e) {
        let selectNamespace = e.value;

        window.location.href = `?namespace=${selectNamespace}`
    }

    $(document).ready(function () {
        $('.dashboard').DataTable({
            ordering: false,
        });
    });
</script>


@yield('js')

</body>

</html>
