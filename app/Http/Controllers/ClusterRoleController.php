<?php

namespace App\Http\Controllers;

class ClusterRoleController extends DashboardController
{
    public function clusterroleDetails($name)
    {

        $cluster = $this->getCluster();

        $namespaces = $cluster->getAllNamespaces();

        $clusterrole = $cluster->getClusterRoleByName($name, '');

        $age = $this->getAge($clusterrole);

        return view('cluster.clusterrole', compact('namespaces', 'clusterrole', 'age'));
    }
}
