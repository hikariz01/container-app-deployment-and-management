<?php

namespace App\Http\Controllers;

class IngressClassController extends DashboardController
{
    public function ingressclassDetails($name)
    {

        $cluster = $this->getCluster();

        $namespaces = $cluster->getAllNamespaces();

        $ingressclass = $this->curlAPI(DashboardController::$api_url.'/apis/networking.k8s.io/v1/ingressclasses/'.$name);

        $age = $this->getAge($ingressclass);

        return view('services.ingressclass', compact('namespaces', 'age', 'ingressclass'));
    }
}
