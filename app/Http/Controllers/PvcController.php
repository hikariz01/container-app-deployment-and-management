<?php

namespace App\Http\Controllers;

class PvcController extends DashboardController
{
    public function pvcDetails($namespace, $name)
    {

        $cluster = $this->getCluster();

        $namespaces = $cluster->getAllNamespaces();

        $pvc = $cluster->getPersistentVolumeClaimByName($name, $namespace);

        $age = '1days';

        return view('config_storage.pvc', compact('namespaces', 'pvc', 'age'));
    }
}
