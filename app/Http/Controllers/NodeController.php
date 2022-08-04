<?php

namespace App\Http\Controllers;

class NodeController extends DashboardController
{
    public function nodeDetails($name)
    {
        $cluster = $this->getCluster();

        $namespaces = $cluster->getAllNamespaces();

        $node = $cluster->getNodeByName($name, '');

        $age = '1days';

        $podCount = [];

        $pods = $cluster->getAllPodsFromAllNamespaces();

        foreach ($pods as $pod) {
            if ($pod->getSpec('nodeName') === $node->getName()) {
                $podCount[] = $pod;
            }
        }


        return view('cluster.node', compact('namespaces', 'node', 'age', 'podCount'));
    }
}
