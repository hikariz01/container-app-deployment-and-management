<?php

namespace App\Http\Controllers;

class ReplicasetController extends DashboardController
{
    public function replicasetDetails($namespace, $name)
    {
        $cluster = $this->getCluster();

        $namespaces = $cluster->getAllNamespaces();

        $replicaset = $this->curlAPI('https://192.168.10.220:6443/apis/apps/v1/namespaces/'.$namespace.'/replicasets/'.$name);

        $age = $this->getAge($replicaset);

        $selector = $replicaset['spec']['selector']['matchLabels'];

        if ($selector['pod-template-hash']??null != null) {
            unset($selector['pod-template-hash']);
        }

        $labelSelector = '';

        $keyLabel = array_keys($selector);

        for ($i=0;$i < count($selector);$i++) {
            if ($i == count($selector) - 1)
                $labelSelector .= $keyLabel[$i] . '%3D' .$selector[$keyLabel[$i]];
            else
                $labelSelector .= $keyLabel[$i] . '%3D' .$selector[$keyLabel[$i]] . ',';
        }

        $pods = $cluster->getAllPods($namespace, ['labelSelector'=>$labelSelector])->toArray();

        $services = $cluster->getAllServicesFromAllNamespaces()->toArray();

        foreach ($services as $key => $service) {
            if (!(array($service['spec']['selector']??[]) === array($selector))) {
                unset($services[$key]);
            }
        }

        $events = $cluster->getAllEvents($namespace)->toArray();

        return view('workloads.replicaset', compact('namespaces', 'age', 'replicaset', 'pods', 'events', 'services'));
    }
}
