<?php

namespace App\Http\Controllers\Edit;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use RenokiCo\PhpK8s\Exceptions\KubernetesAPIException;
use Symfony\Component\Yaml\Yaml;

class EditController extends DashboardController
{

    public function putAPI($endpoint, $data) {
        return Http::withHeaders(
            ['Authorization'=>'Bearer '.env('KUBE_API_TOKEN'),
                'Content-Type'=>'application/yaml'],
        )->withoutVerifying()->put($endpoint, $data)->json();
    }

    public function deleteUsingAPI($endpoint) {
        return Http::withHeaders(
            ['Authorization'=>'Bearer '.env('KUBE_API_TOKEN')],
        )->withoutVerifying()->delete($endpoint)->json();
    }


    public function edit(Request $request) {
        $cluster = $this->getCluster();

        if (Yaml::parse($request->get('value'))['kind'] !== 'ReplicaSet') {
            $resource = $cluster->fromYaml($request->get('value'));
            $resource->update();
        }
        else {
            $data = Yaml::parse($request->get('value'));
            $namespace = Yaml::parse($request->get('value'))['metadata']['namespace'];
            $name = Yaml::parse($request->get('value'))['metadata']['name'];
            $resource = $this->putAPI(env('KUBE_API_SERVER').'/apis/apps/v1/namespaces/'.$namespace.'/replicasets/'.$name, $data);
        }


        //TODO make replicaset works (cURL)

        $resourceTypes = ['Workloads'=>['Deployment', 'DaemonSet', 'Job', 'CronJob', 'Pod', 'ReplicaSet', 'StatefulSet'],
            'Service'=>['Service', 'Ingress', 'IngressClass'],
            'Config and Storage'=>['ConfigMap', 'Secret', 'PersistentVolumeClaim', 'StorageClass'],
            'Cluster'=>['Namespace', 'PersistentVolume', 'ClusterRole', 'ClusterRoleBinding', 'ServiceAccount', 'Role', 'RoleBinding']
        ];

        if (is_array($resource)) {
            if ($resource['kind'] === 'ReplicaSet') {
                return redirect('dashboard');
            }
            elseif ($resource['kind'] === 'IngressClass') {
                return redirect('service');
            }
            return redirect('dashboard');
        }
        elseif (in_array($resource->getKind(), $resourceTypes['Workloads'])) {
            return redirect('dashboard');
        }
        elseif (in_array($resource->getKind(), $resourceTypes['Service'])) {
            return redirect('service');
        }
        elseif (in_array($resource->getKind(), $resourceTypes['Config and Storage'])) {
            return redirect('config_storage');
        }
        elseif (in_array($resource->getKind(), $resourceTypes['Cluster'])) {
            return redirect('cluster');
        }

    }

    public function delete(Request $request) {
        $tmp_arr = explode(' ', $request->get('resource'));
        $kind = $tmp_arr[0];
        $namespace = $tmp_arr[1];
        $name = $tmp_arr[2];
        try {
            $response = $this->deleteResource($kind, $namespace, $name);
        } catch (KubernetesAPIException $e) {

        }
        dd($request);
    }

    /**
     * @throws KubernetesAPIException
     */
    public function deleteResource($kind, $namespace, $name) {
        $cluster = $this->getCluster();
        if ($kind === 'Deployment') {
            return $cluster->getDeploymentByName($name, $namespace)->delete();
        }
        elseif ($kind === 'DaemonSet') {
            return $cluster->getDaemonSetByName($name, $namespace)->delete();
        }
        elseif ($kind === 'Job') {
            return $cluster->getJobByName($name, $namespace)->delete();
        }
        elseif ($kind === 'CronJob') {
            return $cluster->getCronjobByName($name, $namespace)->delete();
        }
        elseif ($kind === 'Pod') {
            return $cluster->getPodByName($name, $namespace)->delete();
        }
        elseif ($kind === 'ReplicaSet') {
            return $this->deleteUsingAPI(env('KUBE_API_SERVER').'/apis/apps/v1/namespaces/'.$namespace.'/replicasets/'.$name);
        }
        elseif ($kind === 'StatefulSet') {
            return $cluster->getStatefulSetByName($name, $namespace)->delete();
        }

    }

}
