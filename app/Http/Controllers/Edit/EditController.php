<?php

namespace App\Http\Controllers\Edit;

use App\Custom\IngressClass;
use App\Custom\ReplicaSet;
use App\Http\Controllers\Controller;
use App\Http\Controllers\DashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use RenokiCo\PhpK8s\Exceptions\KubernetesAPIException;
use Symfony\Component\Yaml\Yaml;

class EditController extends DashboardController
{

    public function deleteUsingAPI($endpoint) {
        return Http::withHeaders(
            ['Authorization'=>'Bearer '.env('KUBE_API_TOKEN')],
        )->withoutVerifying()->delete($endpoint)->json();
    }


    public function edit(Request $request) {
        $cluster = $this->getCluster();


        if (Yaml::parse($request->get('value'))['kind'] !== 'ReplicaSet' && Yaml::parse($request->get('value'))['kind'] !== 'IngressClass') {
            $resource = $cluster->fromYaml($request->get('value'));
            $resource->update();
        }
        elseif (Yaml::parse($request->get('value'))['kind'] === 'ReplicaSet') {
            $yaml = $request->get('value');
            $data = Yaml::parse($yaml);
            $resource = new ReplicaSet($this->getCluster(), $data);
            $resource->update();
        }
        elseif (Yaml::parse($request->get('value'))['kind'] === 'IngressClass') {
            $data = Yaml::parse($request->get('value'));
            $resource = new IngressClass($this->getCluster(), $data);
            $resource->update();
        }


        //TODO make replicaset works (cURL)

        $resourceTypes = ['Workloads'=>['Deployment', 'DaemonSet', 'Job', 'CronJob', 'Pod', 'ReplicaSet', 'StatefulSet'],
            'Service'=>['Service', 'Ingress', 'IngressClass'],
            'Config and Storage'=>['ConfigMap', 'Secret', 'PersistentVolumeClaim', 'StorageClass'],
            'Cluster'=>['Namespace', 'PersistentVolume', 'ClusterRole', 'ClusterRoleBinding', 'ServiceAccount', 'Role', 'RoleBinding']
        ];

        if (in_array($resource->getKind(), $resourceTypes['Workloads'])) {
            return redirect()->back()->with('success', $resource->getKind().'['. $resource->getName() .'] updated successfully.');
        }
        elseif (in_array($resource->getKind(), $resourceTypes['Service'])) {
            return redirect()->back()->with('success', $resource->getKind().'['. $resource->getName() .'] updated successfully.');
        }
        elseif (in_array($resource->getKind(), $resourceTypes['Config and Storage'])) {
            return redirect()->back()->with('success', $resource->getKind().'['. $resource->getName() .'] updated successfully.');
        }
        elseif (in_array($resource->getKind(), $resourceTypes['Cluster'])) {
            return redirect()->back()->with('success', $resource->getKind().'['. $resource->getName() .'] updated successfully.');
        }
        else {
            return redirect()->back()->with('error', 'There is an error.');
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
                    if ($response['details']['kind'] === 'replicasets') {
                        return redirect('dashboard')->with('success', 'ReplicaSet['. $response['details']['name'] .'] deleted successfully');
                    }
                    elseif ($response['details']['kind'] === 'ingressclasses') {
                        return redirect('service')->with('success', 'IngressClass['. $response['details']['name'] .'] deleted successfully');
                    }
                }
                elseif (in_array($kind, $resourceTypes['Workloads'])) {
                    return redirect('dashboard')->with('success', $kind.'['. $name .'] deleted successfully.');
                }
                elseif (in_array($kind, $resourceTypes['Service'])) {
                    return redirect('service')->with('success', $kind.'['. $name .'] deleted successfully.');
                }
                elseif (in_array($kind, $resourceTypes['Config and Storage'])) {
                    return redirect('config_storage')->with('success', $kind.'['. $name .'] deleted successfully.');
                }
                elseif (in_array($kind, $resourceTypes['Cluster'])) {
                    return redirect('cluster')->with('success', $kind.'['. $name .'] deleted successfully.');
                }
            }
            else {
                if (in_array($kind, $resourceTypes['Workloads'])) {
                    return redirect('dashboard')->with('error', 'There is an error.');
                }
                elseif (in_array($kind, $resourceTypes['Service'])) {
                    return redirect('service')->with('error', 'There is an error.');
                }
                elseif (in_array($kind, $resourceTypes['Config and Storage'])) {
                    return redirect('config_storage')->with('error', 'There is an error.');
                }
                elseif (in_array($kind, $resourceTypes['Cluster'])) {
                    return redirect('cluster')->with('error', 'There is an error.');
                }
            }
        } catch (KubernetesAPIException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
        return redirect()->back()->with('error', 'There is an error.');
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
            return $this->deleteUsingAPI(DashboardController::$api_url.'/apis/apps/v1/namespaces/'.$namespace.'/replicasets/'.$name);
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
            return $this->deleteUsingAPI(DashboardController::$api_url.'/apis/networking.k8s.io/v1/ingressclasses/'.$name);
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
        $data = $request->get('resource');


        try {
            $response = $this->scaleResource($kind, $namespace, $name, $value, $data);
            $dataArr = Yaml::parse($data);
            $dataKind = $dataArr['kind']??'-';
            $dataName = $dataArr['metadata']['name']??'-';

            if (is_object($response)) {
                if ($dataKind === 'ReplicaSet') {
                    return redirect()->back()->with('success', $dataKind . '[' . $dataName .'] scaled successfully.');
                }
                else {
                    return redirect()->back()->with('success', $kind. '[' . $name .'] scaled successfully.');
                }
            }
            else {
                return redirect()->back()->with('error', 'There is an error');
            }


        } catch (KubernetesAPIException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }


    }

    public function scaleResource($kind, $namespace, $name, $value, $data='') {
        $cluster = $this->getCluster();
        $attr = Yaml::parse($data);
        if ($kind === 'Deployment') {
            return $cluster->getDeploymentByName($name, $namespace)->scale($value);
        }
        elseif ($kind === 'StatefulSet') {
            return $cluster->getStatefulSetByName($name, $namespace)->scale($value);
        }
        elseif ($attr['kind'] === 'ReplicaSet') {
            $replicaset = new ReplicaSet($this->getCluster(), $attr);
            return $replicaset->scale($value);
        }
    }

}
