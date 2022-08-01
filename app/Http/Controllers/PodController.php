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

        $pvcs = null;

        $events = $pod->getEvents();

        $containers = $pod->getContainers(false);
        $containerStatuses = $pod->getContainerStatuses(false);

        $probe = '';


        return view('workloads.pod', compact('namespaces', 'pod', 'age', 'pvcs', 'events', 'containers', 'containerStatuses', 'probe'));
    }
}
