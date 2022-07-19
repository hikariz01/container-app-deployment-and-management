<?php

namespace App\Http\Controllers;

use RenokiCo\PhpK8s\KubernetesCluster;

class DashboardController extends Controller
{
    private function getCluster($url) {
        $cluster = KubernetesCluster::fromUrl($url);
        $cluster->loadTokenFromFile(storage_path('app/k8s_auth/token.txt'));
        $cluster->withCaCertificate('C:/Users/hikar/.minikube/ca.crt');

        return $cluster;
    }

    public function index()
    {
        $cluster = $this->getCluster('https://127.0.0.1:58926');
        $deployments = $cluster->getAllDeploymentsFromAllNamespaces(['default']);

        return view('workloads', compact('deployments'));
    }
}
