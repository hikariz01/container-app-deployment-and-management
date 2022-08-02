<?php

namespace App\Http\Controllers;

class StatefulSetController extends DashboardController
{
    public function statefulsetDetails($namespace, $name)
    {

        $cluster = $this->getCluster();

        $namespaces = $cluster->getAllNamespaces();

        $statefulset = $cluster->getStatefulSetByName($name, $namespace);

        $age = '1days';

        $selector = $statefulset->getSelectors()['matchLabels'];

        $labelSelector = '';

        $keyLabel = array_keys($selector);

        for ($i=0;$i < count($selector);$i++) {
            if ($i == count($selector) - 1)
                $labelSelector .= $keyLabel[$i] . '%3D' .$selector[$keyLabel[$i]];
            else
                $labelSelector .= $keyLabel[$i] . '%3D' .$selector[$keyLabel[$i]] . ',';
        }

        $pods = $cluster->getAllPods($namespace, ['labelSelector'=>$labelSelector]);

        $events = $statefulset->getEvents();

        return view('workloads.statefulset', compact('namespaces', 'statefulset', 'age', 'pods', 'events'));
    }
}
