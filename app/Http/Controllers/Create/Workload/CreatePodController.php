<?php

namespace App\Http\Controllers\Create\Workload;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DashboardController;
use Illuminate\Http\Request;
use RenokiCo\PhpK8s\Exceptions\KubernetesAPIException;
use RenokiCo\PhpK8s\K8s;
use RenokiCo\PhpK8s\KubernetesCluster;

class CreatePodController extends DashboardController
{
    public function selectedProbe(Request $req, $probe, $probeType) {
        if ($req->get($probeType.'Handler') === 'http') {
            if ($req->get($probeType)['http']['path'] !== null && $req->get($probeType)['http']['port'] !== null) {
                $headers = [];
                foreach ($req->get($probeType)['http']['header'] as $header) {
                    if ($header['name'] !== null && $header['value'] !== null)
                        $headers[$header['name']] = $header['value'];
                }
                $probe->http($req->get($probeType)['http']['path'],(int) $req->get($probeType)['http']['port'], $headers, $req->get($probeType)['http']['scheme']);
            }
        }
        elseif ($req->get($probeType.'Handler') === 'command') {
            if ($req->get($probeType)['command'] != null) {
                $command = explode(',', $req->get($probeType)['command']);
                $probe->command($command);
            }
        }
        elseif ($req->get($probeType.'Handler') === 'tcp') {
            if ($req->get($probeType)['tcp']['host'] !== null) {
                $probe->tcp((int) $req->get($probeType)['tcp']['port'], $req->get($probeType)['tcp']['host']);
            }
            else if ($req->get($probeType)['tcp']['port'] !== null){
                $probe->tcp((int) $req->get($probeType)['tcp']['port']);
            }
        }
    }

    /**
     * @throws KubernetesAPIException
     */
    public function create(Request $req, KubernetesCluster $cluster)
    {
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


        $pod = $cluster->pod();

        foreach ($req->input() as $key => $value) {
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

//            POD
            if ($key === 'podName' && $value !== null) {
                $pod->setName($value);
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

        if (($req->get('startupProbe')['initialDelaySeconds'] !== null
                && $req->get('startupProbe')['periodSeconds'] !== null
                && $req->get('startupProbe')['timeoutSeconds'] !== null
                && $req->get('startupProbe')['failureThreshold'] !== null
                && $req->get('startupProbe')['successThreshold'] !== null)
            && (($req->get('startupProbe')['http']['path'] !== null
                    && $req->get('startupProbe')['http']['port'] !== null)
                || $req->get('startupProbe')['command'] !== null
                || $req->get('startupProbe')['tcp']['port'] !== null)) {
            $probe = K8s::probe()
                ->setInitialDelaySeconds((int) $req->get('startupProbe')['initialDelaySeconds'])
                ->setPeriodSeconds((int) $req->get('startupProbe')['periodSeconds'])
                ->setTimeoutSeconds((int) $req->get('startupProbe')['timeoutSeconds'])
                ->setFailureThreshold((int) $req->get('startupProbe')['failureThreshold'])
                ->setSuccessThreshold((int) $req->get('startupProbe')['successThreshold']);

            $this->selectedProbe($req, $probe, 'startupProbe');

            $container->setStartupProbe($probe);
        }


        if (($req->get('livenessProbe')['initialDelaySeconds'] !== null
                && $req->get('livenessProbe')['periodSeconds'] !== null
                && $req->get('livenessProbe')['timeoutSeconds'] !== null
                && $req->get('livenessProbe')['failureThreshold'] !== null
                && $req->get('livenessProbe')['successThreshold'] !== null)
            && (($req->get('livenessProbe')['http']['path'] !== null
                    && $req->get('livenessProbe')['http']['port'] !== null)
                || $req->get('livenessProbe')['command'] !== null
                || $req->get('livenessProbe')['tcp']['port'] !== null)) {
            $probe = K8s::probe()
                ->setInitialDelaySeconds((int) $req->get('livenessProbe')['initialDelaySeconds'])
                ->setPeriodSeconds((int) $req->get('livenessProbe')['periodSeconds'])
                ->setTimeoutSeconds((int) $req->get('livenessProbe')['timeoutSeconds'])
                ->setFailureThreshold((int) $req->get('livenessProbe')['failureThreshold'])
                ->setSuccessThreshold((int) $req->get('livenessProbe')['successThreshold']);

            $this->selectedProbe($req, $probe, 'livenessProbe');

            $container->setLivenessProbe($probe);
        }


        if (($req->get('readinessProbe')['initialDelaySeconds'] !== null
                && $req->get('readinessProbe')['periodSeconds'] !== null
                && $req->get('readinessProbe')['timeoutSeconds'] !== null
                && $req->get('readinessProbe')['failureThreshold'] !== null
                && $req->get('readinessProbe')['successThreshold'] !== null)
            && (($req->get('readinessProbe')['http']['path'] !== null
                    && $req->get('readinessProbe')['http']['port'] !== null)
                || $req->get('readinessProbe')['command'] !== null
                || $req->get('readinessProbe')['tcp']['port'] !== null)) {
            $probe = K8s::probe()
                ->setInitialDelaySeconds((int) $req->get('readinessProbe')['initialDelaySeconds'])
                ->setPeriodSeconds((int) $req->get('readinessProbe')['periodSeconds'])
                ->setTimeoutSeconds((int) $req->get('readinessProbe')['timeoutSeconds'])
                ->setFailureThreshold((int) $req->get('readinessProbe')['failureThreshold'])
                ->setSuccessThreshold((int) $req->get('readinessProbe')['successThreshold']);

            $this->selectedProbe($req, $probe, 'readinessProbe');

            $container->setReadinessProbe($probe);
        }

        if (count($annotation) !== 0) {
            $pod->setAnnotations($annotation);
        }


        $pod->setContainers([$container]);


//        DEPLOYMENT
        return $pod->create();

    }
}
