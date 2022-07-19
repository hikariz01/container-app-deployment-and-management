<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//Use Library
use RenokiCo\PhpK8s\KubernetesCluster;

class KubeController extends Controller
{
    private function getCluster($url) {
        $cluster = KubernetesCluster::fromUrl($url);
        $cluster->loadTokenFromFile(storage_path('app/k8s_auth/token.txt'));
        $cluster->withCaCertificate('C:/Users/hikar/.minikube/ca.crt');

        return $cluster;
    }

    public function namespaces() {
        $cluster = $this->getCluster('https://127.0.0.1:58926');

        return $cluster->getAllNamespaces()->toArray();
    }
}
