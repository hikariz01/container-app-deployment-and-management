<?php

namespace App\Http\Controllers;

use RenokiCo\PhpK8s\KubernetesCluster;
use RenokiCo\PhpK8s\K8s;

class DashboardController extends Controller
{
    private function getCluster($url) {
        $cluster = KubernetesCluster::fromUrl($url);
        $cluster->loadTokenFromFile(storage_path('app/k8s_auth/token.txt'));
        $cluster->withCaCertificate(storage_path('app/k8s_auth/test.ca.crt'));

        return $cluster;
    }

    public function index()
    {
        $cluster = $this->getCluster('https://192.168.10.220:6443');
        $deployments = $cluster->getAllDeploymentsFromAllNamespaces(['default']);

        $daemonsets = $cluster->getAllDaemonSetsFromAllNamespaces((['default']));

        $jobs = $cluster->getAllJobsFromAllNamespaces(['default']);

        $cronjobs = $cluster->getAllCronjobsFromAllNamespaces(['default']);

        $pods = $cluster->getAllPodsFromAllNamespaces(['default']);


        /**
        $replicasets = curl
         */

        // TODO: curl REPLICASET

        $statefulsets = $cluster->getAllStatefulSetsFromAllNamespaces(['default']);
//        $namespaces = $cluster->getAllNamespaces();
//
//        $i = 0;
//        view('layouts.app2', compact('namespaces'), compact('i'));

//        dd($pods[0]->toArray()['status']['hostIP']);

        return view('workloads', compact('deployments', 'daemonsets', 'jobs', 'cronjobs', 'pods', 'statefulsets'));
    }
}
