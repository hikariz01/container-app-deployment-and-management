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
    <link rel="icon" type="image/png" sizes="16x16" href="plugins/images/favicon.png">
    <!-- Custom CSS -->
    <link href="plugins/bower_components/chartist/dist/chartist.min.css" rel="stylesheet">
    <link rel="stylesheet" href="plugins/bower_components/chartist-plugin-tooltips/dist/chartist-plugin-tooltip.css">
    <!-- Custom CSS -->
    <link href="css/style.min.css" rel="stylesheet">

    <style>
        ::-webkit-scrollbar {
            width: 5px;
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
                <a class="navbar-brand" href="dashboard.html" style="background: #2f323e; font-size: 16px">
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
            <div style="margin-left: 20px; width: 20vw; display: inline-flex">
                <span class="text-white m-auto" style="padding-right: 8px">namespace:</span>
                <select class="form-select ml-3" aria-label="Default select example">
{{--                    <option selected value="default">default</option>--}}
{{--                    <option value="1">One</option>--}}
                    @foreach($namespaces as $namespace)
                        <option value="{{$namespace->toArray()['metadata']['name']}}">{{$namespace->toArray()['metadata']['name']}}</option>
                    @endforeach
                    <option value="all">ALL</option>
                </select>
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
                    <li class=" in">
                        <form role="search" class="app-search d-none d-md-block me-3">
                            <input type="text" placeholder="Search..." class="form-control mt-0">
                            <a href="" class="active">
                                <i class="fa fa-search"></i>
                            </a>
                        </form>
                    </li>
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
                        <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('dashboard')}}"
                           aria-expanded="false">
                            <i class="far fa-clock" aria-hidden="true"></i>
                            <span class="hide-menu">Workloads</span>
                        </a>
                        <ul>
                            <li class="sidebar-item">
                                <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('dashboard')}}#deployment_table"
                                   aria-expanded="false" style="padding-left: 50px">
                                    <span class="hide-menu">> Deployments</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('dashboard') }}#daemonsets_table"
                                   aria-expanded="false" style="padding-left: 50px">
                                    <span class="hide-menu">> Daemon sets</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('dashboard') }}#jobs_table"
                                   aria-expanded="false" style="padding-left: 50px">
                                    <span class="hide-menu">> Jobs</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('dashboard') }}#cronjobs_table"
                                   aria-expanded="false" style="padding-left: 50px">
                                    <span class="hide-menu">> Cron Jobs</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('dashboard') }}#pods_table"
                                   aria-expanded="false" style="padding-left: 50px">
                                    <span class="hide-menu">> Pods</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('dashboard') }}#replicasets_table"
                                   aria-expanded="false" style="padding-left: 50px">
                                    <span class="hide-menu">> Replica Sets</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('dashboard') }}#statefulsets_table"
                                   aria-expanded="false" style="padding-left: 50px">
                                    <span class="hide-menu">> Stateful Sets</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('service') }}"
                           aria-expanded="false">
                            <i class="fa fa-user" aria-hidden="true"></i>
                            <span class="hide-menu">Service</span>
                        </a>
                        <ul>
                            <li class="sidebar-item">
                                <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('service') }}#services_table"
                                   aria-expanded="false" style="padding-left: 50px">
                                    <span class="hide-menu">> Services</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('service') }}#ingresses_table"
                                   aria-expanded="false" style="padding-left: 50px">
                                    <span class="hide-menu">> Ingresses</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('service') }}#ingressclasses_table"
                                   aria-expanded="false" style="padding-left: 50px">
                                    <span class="hide-menu">> Ingress Classes</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('config_storage') }}"
                           aria-expanded="false">
                            <i class="fa fa-table" aria-hidden="true"></i>
                            <span class="hide-menu">Config and Storage</span>
                        </a>
                        <ul>
                            <li class="sidebar-item">
                                <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('config_storage') }}#configmaps_table"
                                   aria-expanded="false" style="padding-left: 50px">
                                    <span class="hide-menu">> Config Maps</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('config_storage') }}#secrets_table"
                                   aria-expanded="false" style="padding-left: 50px">
                                    <span class="hide-menu">> Secrets</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('config_storage') }}#pvcs_table"
                                   aria-expanded="false" style="padding-left: 50px">
                                    <span class="hide-menu">> Persistent Volume Claims</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('config_storage') }}#storageclasses_table"
                                   aria-expanded="false" style="padding-left: 50px">
                                    <span class="hide-menu">> Storage Classes</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('cluster') }}"
                           aria-expanded="false">
                            <i class="fa fa-font" aria-hidden="true"></i>
                            <span class="hide-menu">Cluster</span>
                        </a>
                        <ul>
                            <li class="sidebar-item">
                                <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('cluster') }}#namespaces_table"
                                   aria-expanded="false" style="padding-left: 50px">
                                    <span class="hide-menu">> Namespaces</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('cluster') }}#nodes_table"
                                   aria-expanded="false" style="padding-left: 50px">
                                    <span class="hide-menu">> Nodes</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('cluster') }}#clusterRoles_table"
                                   aria-expanded="false" style="padding-left: 50px">
                                    <span class="hide-menu">> Cluster Roles</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('cluster') }}#clusterRoleBindings_table"
                                   aria-expanded="false" style="padding-left: 50px">
                                    <span class="hide-menu">> Cluster Roles Bindings</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('cluster') }}#events_table"
                                   aria-expanded="false" style="padding-left: 50px">
                                    <span class="hide-menu">> Events</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('cluster') }}#networkPolicies_table"
                                   aria-expanded="false" style="padding-left: 50px">
                                    <span class="hide-menu">> Network Policies</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('cluster') }}#serviceAccounts_table"
                                   aria-expanded="false" style="padding-left: 50px">
                                    <span class="hide-menu">> Service Accounts</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('cluster') }}#roles_table"
                                   aria-expanded="false" style="padding-left: 50px">
                                    <span class="hide-menu">> Roles</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('cluster') }}#roleBindings_table"
                                   aria-expanded="false" style="padding-left: 50px">
                                    <span class="hide-menu">> Role Bindings</span>
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
                    <li class="text-center p-20 upgrade-btn">
                        <a href="https://www.wrappixel.com/templates/ampleadmin/"
                           class="btn d-grid btn-danger text-white" target="_blank">
                            Upgrade to Pro</a>
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
    <div class="page-wrapper overflow-auto" style="height: calc(100vh - 60px)">
        <!-- ============================================================== -->
        <!-- Bread crumb and right sidebar toggle -->
        <!-- ============================================================== -->
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
<script src="plugins/bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap tether Core JavaScript -->
<script src="bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/app-style-switcher.js"></script>
<script src="plugins/bower_components/jquery-sparkline/jquery.sparkline.min.js"></script>
<!--Wave Effects -->
<script src="js/waves.js"></script>
<!--Menu sidebar -->
<script src="js/sidebarmenu.js"></script>
<!--Custom JavaScript -->
<script src="js/custom.js"></script>
<!--This page JavaScript -->
<!--chartis chart-->
<script src="plugins/bower_components/chartist/dist/chartist.min.js"></script>
<script src="plugins/bower_components/chartist-plugin-tooltips/dist/chartist-plugin-tooltip.min.js"></script>
<script src="js/pages/dashboards/dashboard1.js"></script>
</body>

</html>
