<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;

class ServiceController extends DashboardController
{
    public function serviceDetails($namespace, $name)
    {

        $cluster = $this->getCluster();

        $namespaces = $cluster->getAllNamespaces();

        $service = $cluster->getServiceByName($name, $namespace);

        $age = $this->getAge($service);

//        TODO CURL ENDPOINTS
        $endpoints = [];

        $selector = $service->getSelectors();

        $labelSelector = '';

        $keyLabel = array_keys($selector);

        for ($i=0;$i < count($selector);$i++) {
            if ($i == count($selector) - 1)
                $labelSelector .= $keyLabel[$i] . '%3D' .$selector[$keyLabel[$i]];
            else
                $labelSelector .= $keyLabel[$i] . '%3D' .$selector[$keyLabel[$i]] . ',';
        }

        $pods = $cluster->getAllPods($namespace, ['labelSelector'=>$labelSelector]);

        $ingresses_all = $cluster->getAllIngresses($namespace);

        $ingresses = [];

        foreach ($ingresses_all as $ingresses_one) {
            foreach ($ingresses_one->getSpec('rules') as $rule) {
                foreach ($rule['http']['paths'] as $path) {
                    if (!strcmp($service->getName(), $path['backend']['service']['name'])) {
                        $ingresses[] = $ingresses_one;
                    }
                }
            }
        }

        $events = $service->getEvents();

        return view('services.service', compact('namespaces', 'service', 'age', 'endpoints', 'pods', 'ingresses', 'events'));
    }
}
