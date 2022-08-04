<?php

namespace App\Http\Controllers;

class ServiceAccountController extends DashboardController
{
    public function serviceaccountDetails($namespace, $name)
    {

        $cluster = $this->getCluster();

        $namespaces = $cluster->getAllNamespaces();

        $sa = $cluster->getServiceAccountByName($name, $namespace);

        $age = '1days';

        return view('cluster.serviceaccount', compact('namespaces', 'sa', 'age'));
    }
}
