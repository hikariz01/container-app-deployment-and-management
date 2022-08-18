<?php

namespace App\Http\Controllers;

class StorageclassController extends DashboardController
{
    public function storageclassDetails($name)
    {
        $cluster = $this->getCluster();

        $namespaces = $cluster->getAllNamespaces();

        $storageclass = $cluster->getStorageClassByName($name, '');

        $age = $this->getAge($storageclass);

        $persistentvolumes_All = $cluster->getAllPersistentVolumes('');

        $persistentvolumes = [];
        foreach ($persistentvolumes_All as $pv) {
            if ($pv->getSpec('storageClassName') === $storageclass->getName()) {
                $persistentvolumes[] = $pv;
            }
        }

        return view('config_storage.storageclass', compact('namespaces', 'storageclass', 'age', 'persistentvolumes'));
    }
}
