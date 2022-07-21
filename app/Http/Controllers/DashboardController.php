<?php

namespace App\Http\Controllers;

use RenokiCo\PhpK8s\KubernetesCluster;
use RenokiCo\PhpK8s\K8s;

class DashboardController extends Controller
{
    private function getCluster($url="https://192.168.10.220:6443") {
        $cluster = KubernetesCluster::fromUrl($url);
        $cluster->loadTokenFromFile(storage_path('app/k8s_auth/token.txt'));
        $cluster->withCaCertificate(storage_path('app/k8s_auth/test.ca.crt'));

        //C:/Users/hikar/.minikube/ca.crt (storage_path('app/k8s_auth/test.ca.crt'))
        return $cluster;
    }

    public function index($ns='default')
    {

        //https://127.0.0.1:59099 https://192.168.10.220:6443
        $cluster = $this->getCluster();

        $namespaces = $cluster->getAllNamespaces();

        $deployments = $cluster->getAllDeployments($ns);

        $daemonsets = $cluster->getAllDaemonSets($ns);

        $jobs = $cluster->getAllJobs($ns);

        $cronjobs = $cluster->getAllCronjobs($ns);

        $pods = $cluster->getAllPods($ns);


        /**
        $replicasets = curl
         */

        // TODO: curl REPLICASET

        $statefulsets = $cluster->getAllStatefulSets($ns);

//        $i = 0;
//        view('layouts.app2', compact('namespaces'), compact('i'));

//        dd($pods[0]->toArray()['status']['hostIP']);

        return view('workloads', compact('namespaces','deployments', 'daemonsets', 'jobs', 'cronjobs', 'pods', 'statefulsets'));
    }

    public function service($ns='default') {

        $cluster = $this->getCluster();

        $namespaces = $cluster->getAllNamespaces();

        $services = $cluster->getAllServices($ns);

        $ingresses = $cluster->getAllIngresses($ns);


        // TODO: $ingressclasses = CURL เอง

        return view('service', compact('namespaces', 'services', 'ingresses'));
    }

    public function config_storage($ns='default') {

        $cluster = $this->getCluster();

        $namespaces = $cluster->getAllNamespaces();

        $configmaps = $cluster->getAllConfigmaps($ns);

        $secrets = $cluster->getAllSecrets($ns);

        $pvcs = $cluster->getAllPersistentVolumeClaims($ns);

        $storageclasses = $cluster->getAllStorageClasses($ns);

        return view('config_storage', compact('namespaces', 'configmaps', 'secrets', 'pvcs', 'storageclasses'));
    }

    public function cluster($ns='default') {

        $cluster = $this->getCluster();

        $namespaces = $cluster->getAllNamespaces();

        $nodes = $cluster->getAllNodes($ns);

//        $pods = $cluster->getAllPodsFromAllNamespaces();
//
//        $kubeCluster = [];
//
//        foreach ($nodes as $node) {
//            $kubeCluster[$node->toArray()['status']['addresses'][0]['address']] = 0;
//        }
//        $kubeCluster['none'] = 0;
//
//        foreach ($pods as $pod) {
//            $kubeCluster[$pod->toArray()['status']['hostIP']??'none'] = $kubeCluster[$pod->toArray()['status']['hostIP']] + 1;
//        }

        $clusterRoles = $cluster->getAllClusterRoles($ns);

        $clusterRoleBindings = $cluster->getAllClusterRoleBindings($ns);

        $events = $cluster->getAllEvents($ns);

//        $networkPolicies

        $serviceAccounts = $cluster->getAllServiceAccounts($ns);

        $roles = $cluster->getAllRoles($ns);

        $roleBindings = $cluster->getAllRoleBindings($ns);

        return view('cluster', compact('namespaces', 'nodes', 'clusterRoles', 'clusterRoleBindings', 'events', 'serviceAccounts', 'roles', 'roleBindings'));

    }
}
