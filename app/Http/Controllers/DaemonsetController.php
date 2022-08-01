<?php

namespace App\Http\Controllers;

class DaemonsetController extends DashboardController
{
    public function daemonsetDetails($namespace, $name)
    {
        $cluster = $this->getCluster();

        $namespaces = $cluster->getAllNamespaces();


        foreach ($cluster->getAllDaemonSets('') as $dae) {
            if (!strcmp($dae->getName(), $name)) {
                $namespace = $dae->getNamespace();
            }
        }


        $daemonset = $cluster->getDaemonSetByName($name, $namespace);

        $age = "2days";

        $selector = $daemonset->getSelectors()['matchLabels'];

        $labelSelector = '';

        $keyLabel = array_keys($selector);

        for ($i=0;$i < count($selector);$i++) {
            if ($i == count($selector) - 1)
                $labelSelector .= $keyLabel[$i] . '%3D' .$selector[$keyLabel[$i]];
            else
                $labelSelector .= $keyLabel[$i] . '%3D' .$selector[$keyLabel[$i]] . ',';
        }

        $pods = $cluster->getAllPods($namespace, ['labelSelector'=>$labelSelector]);

        $services = $cluster->getAllServices($namespace, ['labelSelector'=>$labelSelector]);

        $events = $daemonset->getEvents();

        return view('workloads.daemonset', compact('namespaces', 'daemonset', 'age', 'pods', 'services', 'events'));
    }
}
