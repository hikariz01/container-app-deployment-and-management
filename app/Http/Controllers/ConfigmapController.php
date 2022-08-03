<?php

namespace App\Http\Controllers;

class ConfigmapController extends DashboardController
{
    public function configmapDetails($namespace, $name)
    {
        $cluster = $this->getCluster();

        $namespaces = $cluster->getAllNamespaces();

        $configmap = $cluster->getConfigmapByName($name, $namespace);

        $age = '1days';

        return view('config_storage.configmap', compact('namespaces', 'configmap', 'age'));
    }
}
