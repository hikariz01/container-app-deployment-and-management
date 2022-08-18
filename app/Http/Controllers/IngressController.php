<?php

namespace App\Http\Controllers;


class IngressController extends DashboardController
{
    public function ingressDetails($namespace, $name)
    {

        $cluster = $this->getCluster();

        $namespaces = $cluster->getAllNamespaces();

        $ingress = $cluster->getIngressByName($name, $namespace);

        $age = $this->getAge($ingress);

        $events = $ingress->getEvents();

        return view('services.ingress', compact('namespaces', 'ingress', 'age', 'events'));
    }
}
