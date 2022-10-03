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

        $endpoints = $this->curlAPI(DashboardController::$api_url.'/apis/discovery.k8s.io/v1beta1/namespaces/'.$ingress->getNamespace().'/endpointslices')['items'];

        $ep = [];
        foreach ($ingress->getRules() as $rule) {
            foreach ($rule['http']['paths'] as $path) {
                foreach ($endpoints as $endpoint) {
                    foreach ($endpoint['metadata']['ownerReferences']??[] as $ownerRef) {
                        if ($ownerRef['kind'] === 'Service' && $ownerRef['name'] === $path['backend']['service']['name']) {
                            $ep[] = $endpoint;
                        }
                    }
                }
            }
        }
        return view('services.ingress', compact('namespaces', 'ingress', 'age', 'events', 'ep'));
    }
}
