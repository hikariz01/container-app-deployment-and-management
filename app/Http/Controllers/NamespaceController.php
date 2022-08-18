<?php

namespace App\Http\Controllers;

class NamespaceController extends DashboardController
{
    public function namespaceDetails($name) {

        $cluster = $this->getCluster();

        $namespaces = $cluster->getAllNamespaces();

        $namespace = $cluster->getNamespaceByName($name, '');

        $age = $this->getAge($namespace);

        $quotas = [];
        $limits = [];
//        TODO CURL resourcequotas and limitrange

        $events = $namespace->getEvents();


        return view('cluster.namespace', compact('namespaces', 'namespace', 'age', 'events', 'quotas', 'limits'));
    }
}
