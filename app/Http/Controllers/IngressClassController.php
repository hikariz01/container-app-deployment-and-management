<?php

namespace App\Http\Controllers;

class IngressClassController extends DashboardController
{
    public function ingressclassDetails($name)
    {

        $cluster = $this->getCluster();

        $namespaces = $cluster->getAllNamespaces();

        $age = '1days';

        $ingressclass = $this->curlAPI(env('KUBE_API_SERVER').'/apis/networking.k8s.io/v1/ingressclasses/'.$name);

        return view('services.ingressclass', compact('namespaces', 'age', 'ingressclass'));
    }
}
