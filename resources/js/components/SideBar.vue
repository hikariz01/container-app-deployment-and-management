<template>
    <div class="container-fluid">
        <div class="row flex-nowrap">
            <div class="col-auto col-md-3 col-xl-2 px-sm-2 px-0 bg-dark">
                <div class="d-flex flex-column align-items-center align-items-sm-start px-3 pt-2 text-white overflow-auto" style="height: calc(100vh - 63px);">
<!--                    <a href="/" class="d-flex align-items-center pb-3 mb-md-0 me-md-auto text-white text-decoration-none">-->
<!--                        <span class="fs-5 d-none d-sm-inline">Menu</span>-->
<!--                    </a>-->
                    <ul class="nav nav-pills flex-column mb-sm-auto mb-0 align-items-center align-items-sm-start">

                        <li class="nav-item">
                            <a href="test/workloads" class="nav-link align-middle px-0">
                                <i class="fs-4 bi-house"></i> <span class="ms-1 d-none d-sm-inline">Workloads</span>
                            </a>
                            <ul class="nav flex-column ms-1">
                                <li class="w-100">
                                    <a href="test/deployments" class="nav-link px-0"> <span class="d-none d-sm-inline submenu">Deployments</span></a>
                                </li>
                                <li>
                                    <a href="test/daemonsets" class="nav-link px-0"> <span class="d-none d-sm-inline submenu">Daemon sets</span></a>
                                </li>
                                <li>
                                    <a href="test/daemonsets" class="nav-link px-0"> <span class="d-none d-sm-inline submenu">Jobs</span></a>
                                </li>
                                <li>
                                    <a href="test/daemonsets" class="nav-link px-0"> <span class="d-none d-sm-inline submenu">Cron Jobs</span></a>
                                </li>
                                <li>
                                    <a href="test/daemonsets" class="nav-link px-0"> <span class="d-none d-sm-inline submenu">Pods</span></a>
                                </li>
                                <li>
                                    <a href="test/daemonsets" class="nav-link px-0"> <span class="d-none d-sm-inline submenu">Replica Sets</span></a>
                                </li>
                                <li>
                                    <a href="test/daemonsets" class="nav-link px-0"> <span class="d-none d-sm-inline submenu">Stateful Sets</span></a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="test/service" class="nav-link px-0 align-middle">
                                <i class="fs-4 bi-speedometer2"></i> <span class="ms-1 d-none d-sm-inline">Service</span> </a>
                            <ul class="nav flex-column ms-1">
                                <li class="w-100">
                                    <a href="#" class="nav-link px-0"> <span class="d-none d-sm-inline submenu">Services</span></a>
                                </li>
                                <li>
                                    <a href="#" class="nav-link px-0"> <span class="d-none d-sm-inline submenu">Ingresses</span></a>
                                </li>
                                <li>
                                    <a href="#" class="nav-link px-0"> <span class="d-none d-sm-inline submenu">Ingress Classes</span></a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="#" class="nav-link px-0 align-middle">
                                <i class="fs-4 bi-table"></i> <span class="ms-1 d-none d-sm-inline">Config and Storage</span></a>
                            <ul class="nav flex-column ms-1">
                                <li class="w-100">
                                    <a href="#" class="nav-link px-0"> <span class="d-none d-sm-inline submenu">Config Maps</span></a>
                                </li>
                                <li>
                                    <a href="#" class="nav-link px-0"> <span class="d-none d-sm-inline submenu">Secret</span></a>
                                </li>
                                <li>
                                    <a href="#" class="nav-link px-0"> <span class="d-none d-sm-inline submenu">Persistent Volume Claims</span></a>
                                </li>
                                <li>
                                    <a href="#" class="nav-link px-0"> <span class="d-none d-sm-inline submenu">Storage Classes</span></a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="#" class="nav-link px-0 align-middle ">
                                <i class="fs-4 bi-bootstrap"></i> <span class="ms-1 d-none d-sm-inline">Cluster</span></a>
                            <ul class="nav flex-column ms-1">
                                <li class="w-100">
                                    <a href="#" class="nav-link px-0"> <span class="d-none d-sm-inline submenu">Namespaces</span></a>
                                </li>
                                <li>
                                    <a href="#" class="nav-link px-0"> <span class="d-none d-sm-inline submenu">Nodes</span></a>
                                </li>
                                <li>
                                    <a href="#" class="nav-link px-0"> <span class="d-none d-sm-inline submenu">Cluster Roles</span></a>
                                </li>
                                <li>
                                    <a href="#" class="nav-link px-0"> <span class="d-none d-sm-inline submenu">Cluster Roles Bindings</span></a>
                                </li>
                                <li>
                                    <a href="#" class="nav-link px-0"> <span class="d-none d-sm-inline submenu">Events</span></a>
                                </li>
                                <li>
                                    <a href="#" class="nav-link px-0"> <span class="d-none d-sm-inline submenu">Network Policies</span></a>
                                </li>
                                <li>
                                    <a href="#" class="nav-link px-0"> <span class="d-none d-sm-inline submenu">Service Account</span></a>
                                </li>
                                <li>
                                    <a href="#" class="nav-link px-0"> <span class="d-none d-sm-inline submenu">Roles</span></a>
                                </li>
                                <li>
                                    <a href="#" class="nav-link px-0"> <span class="d-none d-sm-inline submenu">Role Bindings</span></a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                    <hr>
<!--                    <div class="dropdown pb-4">-->
<!--                        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">-->
<!--                            <img src="https://github.com/mdo.png" alt="hugenerd" width="30" height="30" class="rounded-circle">-->
<!--                            <span class="d-none d-sm-inline mx-1">loser</span>-->
<!--                        </a>-->
<!--                        <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">-->
<!--                            <li><a class="dropdown-item" href="#">New project...</a></li>-->
<!--                            <li><a class="dropdown-item" href="#">Settings</a></li>-->
<!--                            <li><a class="dropdown-item" href="#">Profile</a></li>-->
<!--                            <li>-->
<!--                                <hr class="dropdown-divider">-->
<!--                            </li>-->
<!--                            <li><a class="dropdown-item" href="#">Sign out</a></li>-->
<!--                        </ul>-->
<!--                    </div>-->
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "SideBar"
}
</script>

<style scoped>
a {
    color: white;
}
.submenu {
    font-size: 0.9rem;
    margin-left: 10px;
}
</style>
