<?php

namespace App\Http\Controllers;

class RoleBindingController extends DashboardController
{
    public function rolebindingDetails($namespace, $name)
    {
        $cluster = $this->getCluster();

        $namespaces = $cluster->getAllNamespaces();

        $rolebinding = $cluster->getRoleBindingByName($name, $namespace);

        $age = '1days';

        $role = $cluster->getRoleByName($rolebinding->getRole()['name'], $namespace);

        $subjectRef = [];

        foreach ($rolebinding->getSubjects(false) as $subject) {
            if ($subject['kind'] === 'ServiceAccount') {
                $subjectRef[$subject['name']] = $cluster->getServiceAccountByName($subject['name'], $subject['namespace']??'-');
            }
        }

        return view('cluster.rolebinding', compact('namespaces', 'rolebinding', 'age', 'subjectRef', 'role'));
    }
}
