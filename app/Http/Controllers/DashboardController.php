<?php

namespace App\Http\Controllers;

use App\Models\Cluster;
use Carbon\Carbon;
use Dflydev\DotAccessData\Data;
use Illuminate\Http\Request;
use RenokiCo\PhpK8s\KubernetesCluster;
use RenokiCo\PhpK8s\K8s;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Yaml\Yaml;


class DashboardController extends Controller
{

    public static $api_url = null;

    public function getCluster() {

        $clusterModel = Cluster::query()->where('id', session('cluster_id'))->firstOrFail();


        $cluster = KubernetesCluster::fromUrl($clusterModel->url);
        $cluster->withToken($clusterModel->token);

        $temp = tempnam(sys_get_temp_dir(), 'cacert');
        $temp_file = fopen($temp, 'w');
        fwrite($temp_file, $clusterModel->cacert);

        $cluster->withCaCertificate($temp);

        fclose($temp_file);

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

        $replicasets = $this->curlAPI(($this->getNs() != '') ? DashboardController::$api_url.'/apis/apps/v1/namespaces/'.$this->getNs().'/replicasets' : 'https://192.168.10.220:6443/apis/apps/v1/replicasets')['items'];

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
            if (!isset($replicaset['kind'])) {
                $replicaset = array_merge(['kind'=>'ReplicaSet'], $replicaset);
            }
            if (!isset($replicaset['apiVersion'])) {
                $replicaset = array_merge(['apiVersion'=>'apps/v1'], $replicaset);
            }
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

        $ingressclasses = $this->curlAPI(DashboardController::$api_url.'/apis/networking.k8s.io/v1/ingressclasses')['items'];


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
            if (!isset($ingressclass['kind'])) {
                $ingressclass = array_merge(['kind'=>'IngressClass'], $ingressclass);
            }
            if (!isset($ingressclass['apiVersion'])) {
                $ingressclass = array_merge(['apiVersion'=>'networking.k8s.io/v1'], $ingressclass);
            }
            $ingressclassDataArr[$ingressclass['metadata']['name']] = Yaml::dump($ingressclass, 100, 2);
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

        $configmapDataArr = [];
        $secretDataArr = [];
        $pvcDataArr = [];
        $storageclassDataArr = [];
        foreach ($configmaps as $configmap) {
            $configmapDataArr[$configmap->getNamespace().$configmap->getName()] = Yaml::dump($configmap->toArray(), 100, 2);
        }
        foreach ($secrets as $secret) {
            $secretDataArr[$secret->getNamespace().$secret->getName()] = Yaml::dump($secret->toArray(), 100, 2);
        }
        foreach ($pvcs as $pvc) {
            $pvcDataArr[$pvc->getNamespace().$pvc->getName()] = Yaml::dump($pvc->toArray(), 100, 2);
        }
        foreach ($storageclasses as $storageclass) {
            $storageclassDataArr[$storageclass->getNamespace().$storageclass->getName()] = Yaml::dump($storageclass->toArray(), 100, 2);
        }

        return view('config_storage', compact('namespaces', 'configmaps', 'secrets', 'pvcs', 'storageclasses',
        'configmapDataArr', 'secretDataArr', 'pvcDataArr', 'storageclassDataArr'));
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

        $namespaceDataArr = [];
        $nodeDataArr = [];
        $persistentvolumeDataArr = [];
        $clusterRoleDataArr = [];
        $clusterRoleBindingDataArr = [];
        $serviceAccountDataArr = [];
        $roleDataArr = [];
        $roleBindingDataArr = [];
        foreach ($namespaces as $namespace) {
            $namespaceDataArr[$namespace->getNamespace().$namespace->getName()] = Yaml::dump($namespace->toArray(), 100, 2);
        }
        foreach ($nodes as $node) {
            $nodeDataArr[$node->getNamespace().$node->getName()] = Yaml::dump($node->toArray(), 100, 2);
        }
        foreach ($persistentvolumes as $persistentvolume) {
            $persistentvolumeDataArr[$persistentvolume->getNamespace().$persistentvolume->getName()] = Yaml::dump($persistentvolume->toArray(), 100, 2);
        }
        foreach ($clusterRoles as $clusterRole) {
            $clusterRoleDataArr[$clusterRole->getNamespace().$clusterRole->getName()] = Yaml::dump($clusterRole->toArray(), 100, 2);
        }
        foreach ($clusterRoleBindings as $clusterRoleBinding) {
            $clusterRoleBindingDataArr[$clusterRoleBinding->getNamespace().$clusterRoleBinding->getName()] = Yaml::dump($clusterRoleBinding->toArray(), 100, 2);
        }
        foreach ($serviceAccounts as $serviceAccount) {
            $serviceAccountDataArr[$serviceAccount->getNamespace().$serviceAccount->getName()] = Yaml::dump($serviceAccount->toArray(), 100, 2);
        }
        foreach ($roles as $role) {
            $roleDataArr[$role->getNamespace().$role->getName()] = Yaml::dump($role->toArray(), 100, 2);
        }
        foreach ($roleBindings as $roleBinding) {
            $roleBindingDataArr[$roleBinding->getNamespace().$roleBinding->getName()] = Yaml::dump($roleBinding->toArray(), 100, 2);
        }




        return view('cluster', compact('namespaces', 'nodes', 'persistentvolumes', 'clusterRoles', 'clusterRoleBindings', 'events', 'serviceAccounts', 'roles', 'roleBindings', 'podCount',
        'namespaceDataArr', 'nodeDataArr', 'persistentvolumeDataArr', 'clusterRoleDataArr', 'clusterRoleBindingDataArr', 'serviceAccountDataArr', 'roleDataArr', 'roleBindingDataArr'));

    }

}
