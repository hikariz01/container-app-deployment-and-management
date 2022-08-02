<?php

namespace App\Http\Controllers;

class PodController extends DashboardController
{

    public function podDetails($namespace, $name)
    {

        $cluster = $this->getCluster();

        $namespaces = $cluster->getAllNamespaces();

        $pod = $cluster->getPodByName($name, $namespace);

        $age = '1days';

        $owners = [];

        foreach ($pod->getMetadata()['ownerReferences'] as $ownerRef) {
            if (!strcmp($ownerRef['kind'], 'StatefulSet')) {
                $owners[] = $cluster->getStatefulSetByName($ownerRef['name'], $namespace)->toArray();
            }
            elseif (!strcmp($ownerRef['kind'],'ReplicaSet')) {
//                $owners[] = TODO CURL REPLICASETS
            }
        }

        $pvc_names = [];

        foreach ($pod->getVolumes(false) as $volume) {
            if (!is_null($volume['persistentVolumeClaim']??null)) {
                $pvc_names[$volume['name']] = $volume['persistentVolumeClaim'];
            }
        }

        $pvcs = [];

        foreach ($pvc_names as $pvc_name) {
            $pvcs[] = $cluster->getPersistentVolumeClaimByName($pvc_name['claimName'], $namespace);
        }

        $events = $pod->getEvents();

        $containers = $pod->getContainers(false);
        $containerStatuses = $pod->getContainerStatuses(false);

        $probe = '';

        return view('workloads.pod', compact('namespaces', 'pod', 'age', 'pvcs', 'events', 'containers', 'containerStatuses', 'probe', 'pvcs', 'owners'));
    }
}
