<?php

namespace App\Http\Controllers;

use RenokiCo\PhpK8s\KubernetesCluster;
use RenokiCo\PhpK8s\K8s;

class DashboardController extends Controller
{
    private function getCluster($url="https://127.0.0.1:59099") {
        $cluster = KubernetesCluster::fromUrl($url);
        $cluster->loadTokenFromFile(storage_path('app/k8s_auth/token.txt'));
        $cluster->withCaCertificate('C:/Users/hikar/.minikube/ca.crt');

        //C:/Users/hikar/.minikube/ca.crt (storage_path('app/k8s_auth/test.ca.crt'))
        return $cluster;
    }

    public function index()
    {

        //https://127.0.0.1:59099 https://192.168.10.220:6443
        $cluster = $this->getCluster();

        $namespaces = $cluster->getAllNamespaces();

        $deployments = $cluster->getAllDeployments('default');

        $daemonsets = $cluster->getAllDaemonSets('default');

        $jobs = $cluster->getAllJobs('default');

        $cronjobs = $cluster->getAllCronjobs('default');

        $pods = $cluster->getAllPods('default');


        /**
        $replicasets = curl
         */

        // TODO: curl REPLICASET

        $statefulsets = $cluster->getAllStatefulSets('default');
//        $namespaces = $cluster->getAllNamespaces();
//
//        $i = 0;
//        view('layouts.app2', compact('namespaces'), compact('i'));

//        dd($pods[0]->toArray()['status']['hostIP']);

        return view('workloads', compact('namespaces','deployments', 'daemonsets', 'jobs', 'cronjobs', 'pods', 'statefulsets'));
    }

    public function service() {

        $cluster = $this->getCluster();

        $namespaces = $cluster->getAllNamespaces();


        return view('service', compact('namespaces'));
    }
}
