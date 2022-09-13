<?php

namespace App\Http\Controllers\Create;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Create\Workload\CreatePodController;use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeploymentController;
use App\Http\Controllers\PodController;
use Illuminate\Http\Request;
use RenokiCo\PhpK8s\Exceptions\KubernetesAPIException;
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
        switch ($req->get('selectResourceType')??'') {
            case 'Deployment':
                return view('create.workloads.createDeployment', compact('namespaces', 'resourceType'));
            case 'Pod':
                return view('create.workloads.createPod', compact('namespaces', 'resourceType'));
            default:
                return view('create.workloads.createDeployment', compact('namespaces', 'resourceType'));
        }
    }

    public function result(Request $req) {
        $namespaces = $this->getNamespaces();

        switch ($req->get('resourceType')) {
            case 'Deployment':
                try {
                    $deployment = (new CreateDeploymentController())->create($req, $this->getCluster());
                    return (new DeploymentController())->deploymentDetails($deployment->getNamespace(), $deployment->getName());

                } catch (KubernetesAPIException $e) {
                    return view('result.result', compact('namespaces', 'e'));
                }
            case 'Pod':
                try {
                    $pod = (new CreatePodController())->create($req, $this->getCluster());
                    return (new PodController())->podDetails($pod->getNamespace(), $pod->getName());
                } catch (KubernetesAPIException $e) {
                    return view('result.result', compact('namespaces', 'e'));
                }
            default:
                return view('result.result', compact('namespaces'));
        }



    }
}
