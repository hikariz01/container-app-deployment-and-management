<?php

namespace App\Http\Controllers;

class JobController extends DashboardController
{
    public function jobDetails($namespace, $name)
    {
        $cluster = $this->getCluster();

        $namespaces = $cluster->getAllNamespaces();

        foreach ($cluster->getAllJobs('') as $dep) {
            if (!strcmp($dep->getName(), $name)) {
                $namespace = $dep->getNamespace();
            }
        }

        $job = $cluster->getJobByName($name, $namespace);

        $age = '1days';

        $pods = $job->getPods();

        $events = $job->getEvents();

        return view('workloads.job', compact('namespaces', 'job', 'age', 'pods', 'events'));
    }
}
