<?php

namespace App\Http\Controllers\Create;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DashboardController;
use Illuminate\Http\Request;
use RenokiCo\PhpK8s\K8s;

class CreateController extends DashboardController
{
    public function getNamespaces() {
        $cluster = $this->getCluster();

        return $cluster->getAllNamespaces();
    }

    public function create()
    {

        $namespaces = $this->getNamespaces();

        $resourceTypes = ['Workloads'=>['Deployment', 'Daemon set', 'Job', 'Cron Job', 'Pod', 'Replica Set', 'Stateful Set'],
            'Service'=>['Service', 'Ingress', 'Ingress Class'],
            'Config and Storage'=>['Config Map', 'Secret', 'Persistent Volume Claim', 'Storage Class'],
            'Cluster'=>['Namespace', 'Persistent Volume', 'Cluster Role', 'Cluster Role Binding', 'Service Account', 'Role', 'Role Binding']
        ];


        return view('create', compact('namespaces', 'resourceTypes'));
    }

    public function createResource(Request $req) {

        $namespaces = $this->getNamespaces();

        $resourceType = '';

        if ($req->has('selectResourceType')) {
            $resourceType = $req->get('selectResourceType');
        }

        switch ($data['selectResourceType']??'') {
            case 'Deployment':
                return view('create.workloads.createDeployment', compact('namespaces', 'resourceType'));
            default:
                return view('create.workloads.createDeployment', compact('namespaces', 'resourceType'));
        }

    }

    public function result(Request $req) {
        $namespaces = $this->getNamespaces();

        $deploymentLabels = [];
        $selector = [];
        $podLabel = [];
        $containerLabel = [];
        $containerPort = [];
        $portCount = 0;

        foreach ($req->input() as $key => $value) {
            if (str_contains($key, 'deploymentLabelKey')) {
                $deploymentLabels[$value] = null;
                continue;
            }
            if (str_contains($key, 'deploymentLabelValue')) {
                $deploymentLabels[array_key_last($deploymentLabels)] = $value;
                continue;
            }
            if (str_contains($key, 'labelSelectorKey')) {
                $selector[$value] = null;
                continue;
            }
            if (str_contains($key, 'labelSelectorValue')) {
                $selector[array_key_last($selector)] = $value;
                continue;
            }
            if (str_contains($key, 'podLabelKey')) {
                $podLabel[$value] = null;
                continue;
            }
            if (str_contains($key, 'podLabelValue')) {
                $podLabel[array_key_last($podLabel)] = $value;
                continue;
            }
            if (str_contains($key, 'containerLabelKey')) {
                $containerLabel[$value] = null;
                continue;
            }
            if (str_contains($key, 'containerLabelValue')) {
                $containerLabel[array_key_last($containerLabel)] = $value;
                continue;
            }
            if (str_contains($key, 'containerPortName') && $value != null) {
                $containerPort[preg_replace('/[^\d.]+/', '', $key) - 1]['name'] = $value;
                continue;
            }
            if (str_contains($key, 'containerPortPort') && $value != null) {
                $containerPort[preg_replace('/[^\d.]+/', '', $key) - 1]['containerPort'] = $value;
                continue;
            }
            if (str_contains($key, 'containerPortProtocol') && $value != null) {
                $containerPort[preg_replace('/[^\d.]+/', '', $key) - 1]['protocol'] = $value;
                continue;
            }
            if (str_contains($key, 'containerPortHostIP') && $value != null) {
                $containerPort[preg_replace('/[^\d.]+/', '', $key) - 1]['hostIP'] = $value;
                continue;
            }
            if (str_contains($key, 'containerPortHostPort') && $value != null) {
                $containerPort[preg_replace('/[^\d.]+/', '', $key) - 1]['hostPort'] = $value;
                continue;
            }
        }

        $container = K8s::container();
        if ($req->get('containerName') != null) {
            $container->setName($req->get('containerName'));
        }
        if ($req->get('containerImage') != null) {
            $container->setImage($req->get('containerImage'), $req->get('containerImageVersion') === null ? 'latest' : $req->get('containerImageVersion'));
        }
        $container->setLabels($containerLabel);


        dd($deploymentLabels, $selector, $podLabel, $containerLabel, $container, $containerPort);


        return view('result.result', compact('namespaces'));

    }
}
