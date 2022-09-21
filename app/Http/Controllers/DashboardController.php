<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Dflydev\DotAccessData\Data;
use Illuminate\Http\Request;
use RenokiCo\PhpK8s\KubernetesCluster;
use RenokiCo\PhpK8s\K8s;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Yaml\Yaml;


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


    public function curlAPI($endpoint) {
        return Http::withHeaders(
            ['Authorization'=>'Bearer '.env('KUBE_API_TOKEN')],
        )->withoutVerifying()->get($endpoint)->json();
    }



    public function getAge($resource) {
        if (is_array($resource)) {
            $age = Carbon::now()->diffInDays(Carbon::createFromTimeString($resource['metadata']['creationTimestamp'], 'UTC'));
        }
        else {
            $age = Carbon::now()->diffInDays(Carbon::createFromTimeString($resource->toArray()['metadata']['creationTimestamp'], 'UTC'));
        }
        if ($age > 1) {
            $age .= ' days';
        }
        else {
            $age .= ' day';
        }
        return $age;
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

        $replicasets = $this->curlAPI(($this->getNs() != '') ? 'https://192.168.10.220:6443/apis/apps/v1/namespaces/'.$this->getNs().'/replicasets' : 'https://192.168.10.220:6443/apis/apps/v1/replicasets')['items'];

        $statefulsets = $cluster->getAllStatefulSets($this->getNs());

//        $i = 0;
//        view('layouts.app2', compact('namespaces'), compact('i'));

//        dd($pods[0]->toArray()['status']['hostIP']);
        $deploymentDataArr = [];
        $daemonsetDataArr = [];
        $jobDataArr = [];
        $cronjobDataArr = [];
        $podDataArr = [];
        $replicasetDataArr = [];
        $statefulsetDataArr = [];
        foreach ($deployments as $deployment) {
            $deploymentDataArr[$deployment->getNamespace().$deployment->getName()] = Yaml::dump($deployment->toArray(), 100, 2);
        }
        foreach ($daemonsets as $daemonset) {
            $daemonsetDataArr[$daemonset->getNamespace().$daemonset->getName()] = Yaml::dump($daemonset->toArray(), 100, 2);
        }
        foreach ($jobs as $job) {
            $jobDataArr[$job->getNamespace().$job->getName()] = Yaml::dump($job->toArray(), 100, 2);
        }
        foreach ($cronjobs as $cronjob) {
            $cronjobDataArr[$cronjob->getNamespace().$cronjob->getName()] = Yaml::dump($cronjob->toArray(), 100, 2);
        }
        foreach ($pods as $pod) {
            $podDataArr[$pod->getNamespace().$pod->getName()] = Yaml::dump($pod->toArray(), 100, 2);
        }
        foreach ($statefulsets as $statefulset) {
            $statefulsetDataArr[$statefulset->getNamespace().$statefulset->getName()] = Yaml::dump($statefulset->toArray(), 100, 2);
        }
        foreach ($replicasets as $replicaset) {
            $replicaset = array_merge(['apiVersion'=>'apps/v1', 'kind'=>'ReplicaSet'], $replicaset);
            $replicasetDataArr[$replicaset['metadata']['namespace'].$replicaset['metadata']['name']] = Yaml::dump($replicaset, 100, 2);
        }


        return view('workloads', compact('namespaces','deployments', 'daemonsets', 'jobs', 'cronjobs', 'pods', 'statefulsets', 'replicasets',
            'deploymentDataArr', 'daemonsetDataArr', 'jobDataArr', 'cronjobDataArr', 'podDataArr', 'replicasetDataArr', 'statefulsetDataArr'));
    }

    public function service(Request $request) {

        $cluster = $this->getCluster();

        $namespaces = $cluster->getAllNamespaces();

        $this->selectedNamespace($request);

        $services = $cluster->getAllServices($this->getNs());

        $ingresses = $cluster->getAllIngresses($this->getNs());


        // TODO: $ingressclasses = CURL เอง

        $ingressclasses = $this->curlAPI(env('KUBE_API_SERVER').'/apis/networking.k8s.io/v1/ingressclasses')['items'];


        $serviceDataArr = [];
        $ingressDataArr = [];
        $ingressclassDataArr = [];
        foreach ($services as $service) {
            $serviceDataArr[$service->getNamespace().$service->getName()] = Yaml::dump($service->toArray(), 100, 2);
        }
        foreach ($ingresses as $ingress) {
            $ingressDataArr[$ingress->getNamespace().$ingress->getName()] = Yaml::dump($ingress->toArray(), 100, 2);
        }
        foreach ($ingressclasses as $ingressclass) {
            $ingressclass = array_merge(['apiVersion'=>'apps/v1', 'kind'=>'ReplicaSet'], $ingressclass);
            $ingressclassDataArr[$ingressclass['metadata']['namespace'].$ingressclass['metadata']['name']] = Yaml::dump($ingressclass, 100, 2);
        }


        return view('service', compact('namespaces', 'services', 'ingresses', 'ingressclasses',
            'serviceDataArr', 'ingressclassDataArr', 'ingressDataArr'));
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

        $pods = $cluster->getAllPodsFromAllNamespaces();

        $podCount = [];

        $podPercent = [];

        foreach ($nodes as $node) {
            $podCount[$node->getName()] = [];
            $podPercent[$node->getName()] = 0.0;
        }

        foreach ($nodes as $node) {
            foreach ($pods as $pod) {
                if ($pod->getSpec('nodeName') === $node->getName()) {
                    $podCount[$node->getName()][] = $pod->getName();
                }
            }

//            foreach ($podCount as $podPerPercent) {
//                $podPercent[$node->getName()] = bcdiv(floatval(count($podPerPercent)), floatval($node->getStatus('capacity')['pods']), 7) * 100;
//            }
        }

        $persistentvolumes = $cluster->getAllPersistentVolumes($this->getNs());

        $clusterRoles = $cluster->getAllClusterRoles($this->getNs());

        $clusterRoleBindings = $cluster->getAllClusterRoleBindings($this->getNs());

        $events = $cluster->getAllEvents($this->getNs());

//        $networkPolicies

//        TODO networkPolicies ใส่หรือไม่ใส่

        $serviceAccounts = $cluster->getAllServiceAccounts($this->getNs());

        $roles = $cluster->getAllRoles($this->getNs());

        $roleBindings = $cluster->getAllRoleBindings($this->getNs());

        return view('cluster', compact('namespaces', 'nodes', 'persistentvolumes', 'clusterRoles', 'clusterRoleBindings', 'events', 'serviceAccounts', 'roles', 'roleBindings', 'podCount'));

    }

//    public function edit(Request $request) {
//        $cluster = $this->getCluster();
//        $resource = $cluster->fromYaml($request->get('value'));
//        $resource->update();
//
//        //TODO make replicaset works (cURL)
//
//        $resourceTypes = ['Workloads'=>['Deployment', 'DaemonSet', 'Job', 'CronJob', 'Pod', 'ReplicaSet', 'StatefulSet'],
//            'Service'=>['Service', 'Ingress', 'IngressClass'],
//            'Config and Storage'=>['ConfigMap', 'Secret', 'PersistentVolumeClaim', 'StorageClass'],
//            'Cluster'=>['Namespace', 'PersistentVolume', 'ClusterRole', 'ClusterRoleBinding', 'ServiceAccount', 'Role', 'RoleBinding']
//        ];
//
//        if (in_array($resource->getKind(), $resourceTypes['Workloads'])) {
//            return redirect('dashboard');
//        }
//        elseif (in_array($resource->getKind(), $resourceTypes['Service'])) {
//            return redirect('service');
//        }
//        elseif (in_array($resource->getKind(), $resourceTypes['Config and Storage'])) {
//            return redirect('config_storage');
//        }
//        elseif (in_array($resource->getKind(), $resourceTypes['Cluster'])) {
//            return redirect('cluster');
//        }
//
//    }
//
//    public function delete(Request $request) {
//        dd($request);
//    }

}
