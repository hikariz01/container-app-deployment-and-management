<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DeploymentController extends DashboardController
{

    public function deploymentDetails($namespace, $name)
    {
        $cluster = $this->getCluster();

        $namespaces = $cluster->getAllNamespaces();

        $deployment = $cluster->getDeploymentByName($name, $namespace);

        $age = $this->getAge($deployment);

        $conditions = $deployment->getConditions();

        $selector = $deployment->getSelectors()['matchLabels'];

        $labelSelector = '';

        $keyLabel = array_keys($selector);

        for ($i=0;$i < count($selector);$i++) {
            if ($i == count($selector) - 1)
                $labelSelector .= $keyLabel[$i] . '%3D' .$selector[$keyLabel[$i]];
            else
                $labelSelector .= $keyLabel[$i] . '%3D' .$selector[$keyLabel[$i]] . ',';
        }

        $replicasets = $this->curlAPI(env('KUBE_API_SERVER').'/apis/apps/v1/namespaces/'.$namespace.'/replicasets?labelSelector='.$labelSelector)['items'];

        $replicasetAge = [];

        foreach ($replicasets as $replicaset) {
            $replicasetAge[] = $this->getAge($replicaset);
        }

        $events = $deployment->getEvents();

        return view('workloads.deployment', compact('namespaces', 'deployment', 'age', 'conditions', 'events','replicasets', 'replicasetAge'));
    }

}
