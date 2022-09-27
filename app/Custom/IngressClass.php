<?php

namespace App\Custom;

class IngressClass extends \RenokiCo\PhpK8s\Kinds\K8sResource implements \RenokiCo\PhpK8s\Contracts\InteractsWithK8sCluster
{

    protected static $kind = 'IngressClass';

    protected static $defaultVersion = 'networking.k8s.io/v1';

    protected static $namespaceable = false;

}
