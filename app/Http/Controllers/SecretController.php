<?php

namespace App\Http\Controllers;

class SecretController extends DashboardController
{
    public function secretDetails($namespace, $name)
    {
        $cluster = $this->getCluster();

        $namespaces = $cluster->getAllNamespaces();

        $secret = $cluster->getSecretByName($name, $namespace);

        $age = $this->getAge($secret);

        return view('config_storage.secret', compact('namespaces','secret', 'age'));
    }
}
