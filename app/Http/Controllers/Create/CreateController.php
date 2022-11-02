<?php

namespace App\Http\Controllers\Create;

use App\Custom\IngressClass;
use App\Custom\ReplicaSet;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Create\Workload\CreateDeploymentController;
use App\Http\Controllers\Create\Workload\CreatePodController;use App\Http\Controllers\DashboardController;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Request;
use RenokiCo\PhpK8s\Exceptions\KubernetesAPIException;
use RenokiCo\PhpK8s\K8s;
use Symfony\Component\Yaml\Yaml;

class CreateController extends DashboardController
{
    public function getNamespaces() {
        $cluster = $this->getCluster();

        return $cluster->getAllNamespaces();
    }

    public function create()
    {

        $namespaces = $this->getNamespaces();

//        $resourceTypes = ['Workloads'=>['Deployment', 'Daemon set', 'Job', 'Cron Job', 'Pod', 'Replica Set', 'Stateful Set'],
//            'Service'=>['Service', 'Ingress', 'Ingress Class'],
//            'Config and Storage'=>['Config Map', 'Secret', 'Persistent Volume Claim', 'Storage Class'],
//            'Cluster'=>['Namespace', 'Persistent Volume', 'Cluster Role', 'Cluster Role Binding', 'Service Account', 'Role', 'Role Binding']
//        ];
        $resourceTypes = ['Workloads'=>['Deployment', 'Pod']];


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
                    return redirect()->route('deployment-details', ['namespace'=>$deployment->getNamespace(), 'name'=>$deployment->getName()]);

                } catch (KubernetesAPIException $e) {
                    return redirect()->back()->with('error', $e->getMessage());
                }
            case 'Pod':
                try {
                    $pod = (new CreatePodController())->create($req, $this->getCluster());
                    return redirect()->route('pod-details', ['namespace'=>$pod->getNamespace(), 'name'=>$pod->getName()]);
                } catch (KubernetesAPIException $e) {
                    return redirect()->back()->with('error', $e->getMessage());
                }
            default:
                return view('result.result', compact('namespaces'));
        }



    }

    public function createFromYaml(Request $request) {
        $yaml = $request->get('value');
        $dataArr = yaml_parse($yaml, -1);
        $cluster = $this->getCluster();

        try {
            foreach ($dataArr as $data) {
                if (isset($data['kind'])) {
                    if ($data['kind'] === 'ReplicaSet') {
                        $replicaset = new ReplicaSet($cluster, $data);
                        $response[$data['kind'].'-'.$data['metadata']['name']] = $replicaset->createOrUpdate();
                    }
                    elseif ($data['kind'] === 'IngressClass') {
                        $ingressclass = new IngressClass($cluster, $data);
                        $response[$data['kind'].'-'.$data['metadata']['name']] = $ingressclass->createOrUpdate();
                    }
                    else {
                        $resource = $cluster->fromYaml(yaml_emit($data));
                        $response[$data['kind'].'-'.$data['metadata']['name']??'-'] = $resource->createOrUpdate();
                    }
                }
            }
            return redirect('dashboard')->with('success', 'Resources created successfully.');
        }
        catch (KubernetesAPIException $e) {
            return redirect('dashboard')->with('error', 'There is an error! Please review your yaml again.');
        }
    }

    public function createFromYamlFile(Request $request) {
//        $request->validate([
//           'file' => 'required|mimes:yml,yaml'
//        ]);
        $cluster = $this->getCluster();

        $result = '';

        if ($request->hasFile('file')) {
            $files = $request->file('file');
            foreach ($files as $yaml) {
                try {
                    $data = $yaml->get();
                } catch (FileNotFoundException $e) {
                    return redirect('dashboard')->with('error', $e->getMessage());
                }
                $yaml_data = file_get_contents($yaml);
                $resources = yaml_parse($yaml_data, -1);
                foreach ($resources as $resource) {
                    try {
                        if ($resource['kind'] === 'ReplicaSet') {
                            $replicaset = new ReplicaSet($cluster, $resource);
                            $response[] = $replicaset->createOrUpdate();
                        }
                        elseif ($resource['kind'] === 'IngressClass') {
                            $ingressclass = new IngressClass($cluster, $resource);
                            $response[] = $ingressclass->createOrUpdate();
                        }
                        else {
                            $resourceToCreate = $cluster->fromYaml(yaml_emit($resource));
                            $response[] = $resourceToCreate->createOrUpdate();
                        }
                    }
                    catch (KubernetesAPIException $e) {
                        return redirect('dashboard')->with('error', $e->getMessage());
                    }
                    foreach ($response as $res) {
                        $result .= $res->getKind().'[' . $res->getName() . '] ';
                    }
                }
            }

            return redirect('dashboard')->with('success', $result .'created successfully.');
        }
        else {
            return redirect('dashboard')->with('error', 'There is an error! Please review your yaml files again.');
        }
    }
}
