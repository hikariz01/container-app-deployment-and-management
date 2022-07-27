<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

class DeploymentController extends DashboardController
{
    public function deploymentDetails($namespace, $name)
    {
        $cluster = $this->getCluster();

        $namespaces = $cluster->getAllNamespaces();

        if (!strcmp($namespace, 'all')) {
            foreach ($cluster->getAllDeployments('') as $dep) {
                if (!strcmp($dep->getName(), $name)) {
                    $namespace = $dep->getNamespace();
                }
            }
        }

        $deployment = $cluster->getDeploymentByName($name, $namespace);


        $age = "1days";

        $conditions = $deployment->getConditions();

        $events = $deployment->getEvents();

        return view('workloads.deployment', compact('namespaces', 'deployment', 'age', 'conditions', 'events'));
    }
}
