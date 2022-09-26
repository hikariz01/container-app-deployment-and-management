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

    public function scaleUsingAPI($endpoint, $value) {
        return Http::withHeaders(
            ['Authorization'=>'Bearer '.env('KUBE_API_TOKEN'),
            "Content-Type" => "application/strategic-merge-patch+json"],
        )->withoutVerifying()->patch($endpoint, $value)->json();
    }

    public function edit(Request $request) {
        $cluster = $this->getCluster();

        if (Yaml::parse($request->get('value'))['kind'] !== 'ReplicaSet' && Yaml::parse($request->get('value'))['kind'] !== 'IngressClass') {
            $resource = $cluster->fromYaml($request->get('value'));
            $resource->update();
        }
        elseif (Yaml::parse($request->get('value'))['kind'] === 'ReplicaSet') {
            $data = Yaml::parse($request->get('value'));
            $namespace = $data['metadata']['namespace'];
            $name = $data['metadata']['name'];
            $resource = $this->putAPI(env('KUBE_API_SERVER').'/apis/apps/v1/namespaces/'.$namespace.'/replicasets/'.$name, $data);
//            dd($resource, $data);
//            TODO แก้ bug found unknown escape character
        }
        elseif (Yaml::parse($request->get('value'))['kind'] === 'IngressClass') {
            $data = Yaml::parse($request->get('value'));
            $name = $data['metadata']['name'];
            $resource = $this->putAPI(env('KUBE_API_SERVER').'/apis/networking.k8s.io/v1/ingressclasses/'.$name, $data);
//            dd($resource, $data);
//            TODO แก้ bug found unknown escape character
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


        $resourceTypes = ['Workloads'=>['Deployment', 'DaemonSet', 'Job', 'CronJob', 'Pod', 'ReplicaSet', 'StatefulSet'],
            'Service'=>['Service', 'Ingress', 'IngressClass'],
            'Config and Storage'=>['ConfigMap', 'Secret', 'PersistentVolumeClaim', 'StorageClass'],
            'Cluster'=>['Namespace', 'Node', 'PersistentVolume', 'ClusterRole', 'ClusterRoleBinding', 'ServiceAccount', 'Role', 'RoleBinding']
        ];


        try {
            $response = $this->deleteResource($kind, $namespace, $name);
            if ($response['status']??'-' === 'Success' || $response === true) {
                if (is_array($response)) {
                    if ($response['details']['kind'] === 'ReplicaSet') {
                        return redirect('dashboard');
                    }
                    elseif ($response['details']['kind'] === 'IngressClass') {
                        return redirect('service');
                    }
                }
                elseif (in_array($kind, $resourceTypes['Workloads'])) {
                    return redirect('dashboard');
                }
                elseif (in_array($kind, $resourceTypes['Service'])) {
                    return redirect('service');
                }
                elseif (in_array($kind, $resourceTypes['Config and Storage'])) {
                    return redirect('config_storage');
                }
                elseif (in_array($kind, $resourceTypes['Cluster'])) {
                    return redirect('cluster');
                }
            }
        } catch (KubernetesAPIException $e) {
            dd($e);
        }
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
        elseif ($kind === 'Service') {
            return $cluster->getServiceByName($name, $namespace)->delete();
        }
        elseif ($kind === 'Ingress') {
            return $cluster->getIngressByName($name, $namespace)->delete();
        }
        elseif ($kind === 'IngressClass') {
            return $this->deleteUsingAPI(env('KUBE_API_SERVER').'/apis/networking.k8s.io/v1/ingressclasses/'.$name);
        }
        elseif ($kind === 'ConfigMap') {
            return $cluster->getConfigmapByName($name, $namespace)->delete();
        }
        elseif ($kind === 'Secret') {
            return $cluster->getSecretByName($name, $namespace)->delete();
        }
        elseif ($kind === 'PersistentVolumeClaim') {
            return $cluster->getPersistentVolumeClaimByName($name, $namespace)->delete();
        }
        elseif ($kind === 'StorageClass') {
            return $cluster->getStorageClassByName($name, $namespace)->delete();
        }

        return false;
    }

    public function scale(Request $request) {
        $tmp_arr = explode(' ', $request->get('resource'));
        $kind = $tmp_arr[0];
        $namespace = $tmp_arr[1];
        $name = $tmp_arr[2];
        $value = $request->get('scaleNumber');

        $resourceTypes = ['Workloads'=>['Deployment', 'DaemonSet', 'Job', 'CronJob', 'Pod', 'ReplicaSet', 'StatefulSet'],
            'Service'=>['Service', 'Ingress', 'IngressClass'],
            'Config and Storage'=>['ConfigMap', 'Secret', 'PersistentVolumeClaim', 'StorageClass'],
            'Cluster'=>['Namespace', 'Node', 'PersistentVolume', 'ClusterRole', 'ClusterRoleBinding', 'ServiceAccount', 'Role', 'RoleBinding']
        ];


        try {
            $response = $this->scaleResource($kind, $namespace, $name, $value);
            if (is_array($response)) {
//                dd($response);
                if ($response['details']['kind'] === 'ReplicaSet') {
                    return redirect('dashboard');
                }
                elseif ($response['details']['kind'] === 'IngressClass') {
                    return redirect('service');
                }
            }
            elseif (in_array($kind, $resourceTypes['Workloads'])) {
                return redirect('dashboard');
            }
            elseif (in_array($kind, $resourceTypes['Service'])) {
                return redirect('service');
            }
            elseif (in_array($kind, $resourceTypes['Config and Storage'])) {
                return redirect('config_storage');
            }
            elseif (in_array($kind, $resourceTypes['Cluster'])) {
                return redirect('cluster');
            }

        } catch (KubernetesAPIException $e) {
            dd($e);
        }


    }

    public function scaleResource($kind, $namespace, $name, $value) {
        $cluster = $this->getCluster();
        if ($kind === 'Deployment') {
            return $cluster->getDeploymentByName($name, $namespace)->scale($value);
        }
        elseif ($kind === 'DaemonSet') {
            return $cluster->getDaemonSetByName($name, $namespace)->scale($value);
        }
        elseif ($kind === 'Job') {
            return $cluster->getJobByName($name, $namespace)->scale($value);
        }
        elseif ($kind === 'CronJob') {
            return $cluster->getCronjobByName($name, $namespace)->scale($value);
        }
        elseif ($kind === 'Pod') {
            return $cluster->getPodByName($name, $namespace)->scale($value);
        }
//        elseif ($kind === 'ReplicaSet') {
//            $data['spec'] = ['replicas'=> strval($value)];
//            return $this->scaleUsingAPI(env('KUBE_API_SERVER').'/apis/apps/v1/namespaces/'.$namespace.'/replicasets/'.$name, $data);
//        }
//        TODO fix replicaset invalidtype int32
        elseif ($kind === 'StatefulSet') {
            return $cluster->getStatefulSetByName($name, $namespace)->scale($value);
        }
    }

}
