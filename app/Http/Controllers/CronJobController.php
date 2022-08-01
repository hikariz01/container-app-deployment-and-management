<?php

namespace App\Http\Controllers;

class CronJobController extends DashboardController
{
    public function cronjobDetails($namespace, $name)
    {
        $cluster = $this->getCluster();

        $namespaces = $cluster->getAllNamespaces();

        $cronjob = $cluster->getCronjobByName($name, $namespace);

        $age = '1days';

        $activeJobs = $cronjob->getStatus('active');

        $activeJobArr = [];

        if (!is_null($activeJobs)) {
            foreach ($activeJobs as $activeJob) {
                $activeJobArr[] = $cluster->getJobByName($activeJob['name'], $activeJob['namespace']);
            }
        }

        $allJobs = $cluster->getAllJobs($namespace);

        $inactiveJobs = [];

        foreach ($allJobs as $allJob) {
            if ($allJob->getMetadata()['ownerReferences'][0]['uid']??null === $cronjob->getResourceUid()) {
                if ($allJob->getStatus('conditions')[0]['type']??null === 'Complete' && $allJob->getStatus('conditions')[0]['status']??null === "True") {
                    $inactiveJobs[] =$allJob;
                }
            }
        }


        $events = $cronjob->getEvents();

        return view('workloads.cronjob', compact('namespaces', 'cronjob', 'age', 'activeJobArr', 'inactiveJobs', 'events'));
    }
}
