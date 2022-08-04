<?php

namespace App\Http\Controllers;

class RoleController extends DashboardController
{
    public function roleDetails($namespace, $name)
    {

        $cluster = $this->getCluster();

        $namespaces = $cluster->getAllNamespaces();

        $role = $cluster->getRoleByName($name, $namespace);

        $age = '1days';

        return view('cluster.role', compact('namespaces', 'role', 'age'));
    }
}
