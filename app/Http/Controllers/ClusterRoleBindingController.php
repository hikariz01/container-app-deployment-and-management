<?php

namespace App\Http\Controllers;

class ClusterRoleBindingController extends DashboardController
{
    public function clusterrolebindingDetails($name)
    {

        $cluster = $this->getCluster();

        $namespaces = $cluster->getAllNamespaces();

        $crb = $cluster->getClusterRoleBindingByName($name, '');

        $age = '1days';

        $cr = $cluster->getClusterRoleByName($crb->getRole()['name'], '');

        $subjectRef = [];

        foreach ($crb->getSubjects(false) as $subject) {
            if ($subject['kind'] === 'ServiceAccount') {
                $subjectRef[$subject['name']] = $cluster->getServiceAccountByName($subject['name'], $subject['namespace']);
            }
        }

        return view('cluster.clusterrolebinding', compact('namespaces', 'crb', 'age', 'cr', 'subjectRef'));
    }
}
