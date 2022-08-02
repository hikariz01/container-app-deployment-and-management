<?php

namespace App\Http\Controllers;

use Dflydev\DotAccessData\Data;
use Illuminate\Http\Request;
use RenokiCo\PhpK8s\KubernetesCluster;
use RenokiCo\PhpK8s\K8s;


class DashboardController extends Controller
{
    public function getCluster($url="https://192.168.10.220:6443") {
        $cluster = KubernetesCluster::fromUrl($url);
        $cluster->loadTokenFromFile(storage_path('app/k8s_auth/token.txt'));
        $cluster->withCaCertificate(storage_path('app/k8s_auth/test.ca.crt'));

        //https://192.168.10.220:6443

        //C:/Users/hikar/.minikube/ca.crt (storage_path('app/k8s_auth/test.ca.crt'))
        return $cluster;
    }

//    public function getNodeNameByIP($ip) {
//        $cluster = $this->getCluster();
//
//        $nodes = $cluster->getAllNodes('');
//
//        $name = '';
//        foreach ($nodes as $node) {
//            if (!strcmp($node->toArray()['status']['addresses'][0]['address'], $ip)) {
//                $name = $node->getName();
//            }
//        }
//        return $name;
//    }


    private string $ns = "default";

    /**
     * @return string
     */
    public function getNs(): string
    {
        return $this->ns;
    }

    /**
     * @param string $ns
     */
    public function setNs(string $ns): void
    {
        $this->ns = $ns;
    }

    public function selectedNamespace(Request $request) {

        if($request->has('namespace'))
            if ($request->get('namespace') == "all")
                $this->setNs('');
            else
                $this->setNs($request->input('namespace'));
        else
            $this->setNs('default');

        // fetch your namespace
        return $this->getNs();

    }


    public function index(Request $request)
    {

        //https://127.0.0.1:59099 https://192.168.10.220:6443
        $cluster = $this->getCluster();

        $namespaces = $cluster->getAllNamespaces();

        $this->selectedNamespace($request);

        $deployments = $cluster->getAllDeployments($this->getNs());

        $daemonsets = $cluster->getAllDaemonSets($this->getNs());

        $jobs = $cluster->getAllJobs($this->getNs());

        $cronjobs = $cluster->getAllCronjobs($this->getNs());

        $pods = $cluster->getAllPods($this->getNs());

//        dd($cluster->getCronjobByName('hello', 'default')->getMetadata());

        /**
        $replicasets = curl
         */

        // TODO: curl REPLICASET

        $statefulsets = $cluster->getAllStatefulSets($this->getNs());

//        $i = 0;
//        view('layouts.app2', compact('namespaces'), compact('i'));

//        dd($pods[0]->toArray()['status']['hostIP']);

        return view('workloads', compact('namespaces','deployments', 'daemonsets', 'jobs', 'cronjobs', 'pods', 'statefulsets'));
    }

    public function service(Request $request) {

        $cluster = $this->getCluster();

        $namespaces = $cluster->getAllNamespaces();

        $this->selectedNamespace($request);

        $services = $cluster->getAllServices($this->getNs());

        $ingresses = $cluster->getAllIngresses($this->getNs());


        // TODO: $ingressclasses = CURL เอง


        return view('service', compact('namespaces', 'services', 'ingresses'));
    }

    public function config_storage(Request $request) {

        $cluster = $this->getCluster();

        $namespaces = $cluster->getAllNamespaces();

        $this->selectedNamespace($request);

        $configmaps = $cluster->getAllConfigmaps($this->getNs());

        $secrets = $cluster->getAllSecrets($this->getNs());

        $pvcs = $cluster->getAllPersistentVolumeClaims($this->getNs());

        $storageclasses = $cluster->getAllStorageClasses($this->getNs());

        return view('config_storage', compact('namespaces', 'configmaps', 'secrets', 'pvcs', 'storageclasses'));
    }

    public function cluster(Request $request) {

        $cluster = $this->getCluster();

        $namespaces = $cluster->getAllNamespaces();

        $this->selectedNamespace($request);

        $nodes = $cluster->getAllNodes($this->getNs());

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

        $clusterRoles = $cluster->getAllClusterRoles($this->getNs());

        $clusterRoleBindings = $cluster->getAllClusterRoleBindings($this->getNs());

        $events = $cluster->getAllEvents($this->getNs());

//        $networkPolicies

        $serviceAccounts = $cluster->getAllServiceAccounts($this->getNs());

        $roles = $cluster->getAllRoles($this->getNs());

        $roleBindings = $cluster->getAllRoleBindings($this->getNs());

        return view('cluster', compact('namespaces', 'nodes', 'clusterRoles', 'clusterRoleBindings', 'events', 'serviceAccounts', 'roles', 'roleBindings'));

    }

}
