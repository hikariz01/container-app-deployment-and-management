<?php

namespace App\Http\Controllers;

class PersistentVolumeController extends DashboardController
{
    public function pvDetails($name)
    {

        $cluster = $this->getCluster();

        $namespaces = $cluster->getAllNamespaces();

        $pv = $cluster->getPersistentVolumeByName($name, '');

        $age = $this->getAge($pv);

        $types = [];

        array_push($types, 'awsElasticBlockStore', 'azureDisk', 'azureFile', 'cephfs', 'csi', 'fc'
        , 'gcePersistentDisk', 'glusterfs', 'hostPath', 'iscsi', 'local', 'portworxVolume', 'nfs', 'rbd', 'vsphereVolume');

        return view('cluster.persistentvolume', compact('namespaces', 'pv', 'age', 'types'));
    }
}
