<?php

namespace App\Http\Controllers\Create\Workload;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use RenokiCo\PhpK8s\Exceptions\KubernetesAPIException;
use RenokiCo\PhpK8s\K8s;
use RenokiCo\PhpK8s\KubernetesCluster;

class CreateDeploymentController extends Controller
{
    /**
     * @throws KubernetesAPIException
     */
    public function create(Request $req, KubernetesCluster $cluster)
    {
        $deploymentLabels = [];
        $selector = [];
        $podLabel = [];
        $containerLabel = [];
        $containerPort = [];
        $commands = [];
        $arguments = [];
        $env = [];
        $annotation = [];
//        $mountVolumes = [];
        $portCount = 0;


        $container = K8s::container();


        $pod = K8s::pod();


        $deployment = $cluster->deployment();


        foreach ($req->input() as $key => $value) {
            if ($key === 'deploymentLabel' && $value !== null) {
                foreach ($value as $label) {
                    $deploymentLabels[$label['key']] = $label['value'];
                }
                continue;
            }
            if ($key === 'labelSelector' && $value !== null) {
                foreach ($value as $label) {
                    $selector[$label['key']] = $label['value'];
                }
                continue;
            }
            if ($key === 'podLabel' && $value !== null) {
                foreach ($value as $label) {
                    $podLabel[$label['key']] = $label['value'];
                }
                continue;
            }
            if ($key === 'containerLabel' && $value !== null) {
                foreach ($value as $label) {
                    $containerLabel[$label['key']] = $label['value'];
                }
                continue;
            }
            if ($key === 'containerPort' && $value !== null) {
                foreach ($value as $label) {
                    if (isset($label['name']))
                        $containerPort[$portCount]['name'] = $label['name'];
                    if (isset($label['containerPort']))
                        $containerPort[$portCount]['containerPort'] = (int) $label['containerPort'];
                    if (isset($label['protocol']))
                        $containerPort[$portCount]['protocol'] = $label['protocol'];
                    $portCount++;
                }
                continue;
            }
//            if (str_contains($key, 'containerPortHostPort') && $value != null) {
//                $containerPort[preg_replace('/[^\d.]+/', '', $key) - 1]['hostPort'] = $value;
//                continue;
//            }
//            if (str_contains($key, 'containerPortHostPort') && $value != null) {
//                $containerPort[preg_replace('/[^\d.]+/', '', $key) - 1]['hostPort'] = $value;
//                continue;
//            }
            if ($key === 'cpuRequest' && $value !== null) {
                $container->minCpu($value);
                continue;
            }
            if ($key === 'memRequest' && $value !== null) {
                $container->minMemory($value, 'Mi');
                continue;
            }
            if ($key === 'runCommand' && $value !== null) {
                foreach (explode(',', $value) as $cmd) {
                    $commands[] = $cmd;
                }
                $container->setCommand($commands);
                continue;
            }
            if ($key === 'runCommandArgument' && $value !== null) {
                foreach (explode(',', $value) as $agrs) {
                    $arguments[] = $agrs;
                }
                $container->setArgs($arguments);
                continue;
            }
            if ($key === 'envVariable') {
                foreach ($value as $label) {
                    if ($label['key'] !== null)
                        $env[$label['key']] = $label['value'];
                }
                continue;
            }
//            if (str_contains($key, 'mountName') && $value != null) {
//                $mountVolumes[preg_replace('/[^\d.]+/', '', $key) - 1]['name'] = $value;
//                continue;
//            }
//            if (str_contains($key, 'mountPath') && $value != null) {
//                $mountVolumes[preg_replace('/[^\d.]+/', '', $key) - 1]['mountPath'] = $value;
//                continue;
//            }
//            if (str_contains($key, 'mountReadOnly') && $value != null) {
//                $mountVolumes[preg_replace('/[^\d.]+/', '', $key) - 1]['mountReadOnly'] = $value;
//                continue;
//            }
//            if (str_contains($key, 'mountSubPath') && $value != null) {
//                $mountVolumes[preg_replace('/[^\d.]+/', '', $key) - 1]['mountSubPath'] = $value;
//                continue;
//            }

//            POD
            if ($key === 'podName' && $value !== null) {
                $pod->setName($value);
                continue;
            }

//            DEPLOYMENT
            if ($key === 'name' && $value !== null) {
                $deployment->setName($value);
                continue;
            }

            if ($key === 'namespace' && $value !== null) {
                $deployment->setNamespace($value);
                continue;
            }

            if ($key === 'annotation' && $value !== null) {
                $annotation['description'] = $value;
                continue;
            }
            if ($key === 'replicas' && $value !== null) {
                $deployment->setReplicas($value);
                continue;
            }

        }

        if ($req->get('containerName') != null) {
            $container->setName($req->get('containerName'));
        }
        if ($req->get('containerImage') != null) {
            $container->setImage($req->get('containerImage'), $req->get('containerImageVersion') === null ? 'latest' : $req->get('containerImageVersion'));
        }
        $container->setLabels($containerLabel);

        if (count($containerPort) != 0)
            $container->setPorts($containerPort);

        if (count($env) != 0)
            $container->setEnv($env);

//        POD
        if (count($podLabel) != 0)
            $pod->setLabels($podLabel);

//        if (count($mountVolumes) != 0) {
//            foreach ($mountVolumes as $mountVolume) {
//                $volume = K8s::volume();
//                if (isset($mountVolume['name']))
//                    $volume->emptyDirectory($mountVolume['name']);
//                if (isset($mountVolume['mountPath']))
//                    $volume->mountTo($mountVolume['mountPath']);
//                if (isset($mountVolume['mountReadOnly']))
//                    $volume->setAttribute('mountReadOnly', (bool) $mountVolume['mountReadOnly']);
//                if (isset($mountVolume['subPath']))
//                    $volume->setAttribute('subPath', $mountVolume['subPath']);
//                $container->addMountedVolumes([$volume]);
//                $pod->addVolumes([$volume]);
//            }
//        }


        $pod->setContainers([$container]);

//        DEPLOYMENT
        if (count($deploymentLabels) != 0) {
            $deployment->setLabels($deploymentLabels);
        }
        if (count($annotation) != 0) {
            $deployment->setAnnotations($annotation);
        }
        $deployment->setSelectors(['matchLabels'=>$selector]);
        $deployment->setTemplate($pod);

        return $deployment->create();

    }
}
