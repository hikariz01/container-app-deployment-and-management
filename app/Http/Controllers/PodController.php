<?php

namespace App\Http\Controllers;

class PodController extends DashboardController
{

    public function podDetails($namespace, $name)
    {

        $cluster = $this->getCluster();

        $namespaces = $cluster->getAllNamespaces();

        $pod = $cluster->getPodByName($name, $namespace);

        $age = $this->getAge($pod);

        $owners = [];

        foreach ($pod->getMetadata()['ownerReferences'] as $ownerRef) {
            if (!strcmp($ownerRef['kind'], 'StatefulSet')) {
                $owners[] = $cluster->getStatefulSetByName($ownerRef['name'], $namespace)->toArray();
            }
            elseif (!strcmp($ownerRef['kind'],'ReplicaSet')) {
//                TODO CHECK IF AVAILABLE
                $owners[] = $this->curlAPI(env('KUBE_API_SERVER').'/apis/apps/v1/namespaces/'.$namespace.'/replicasets/'.$ownerRef['name']);
            }
        }

        $ownersAge = [];

        foreach ($owners as $owner) {
            $ownersAge[] = $this->getAge($owner);
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

        return view('workloads.pod', compact('namespaces', 'pod', 'age', 'pvcs', 'events', 'containers', 'containerStatuses', 'probe', 'pvcs', 'owners', 'ownersAge'));
    }
}
