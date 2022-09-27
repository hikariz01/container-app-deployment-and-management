<?php

namespace App\Custom;

use RenokiCo\PhpK8s\Contracts\Scalable;
use RenokiCo\PhpK8s\Traits\Resource\CanScale;

class ReplicaSet extends \RenokiCo\PhpK8s\Kinds\K8sResource implements \RenokiCo\PhpK8s\Contracts\InteractsWithK8sCluster, Scalable
{
    protected static $kind = 'ReplicaSet';

    protected static $defaultVersion = 'apps/v1';

    protected static $namespaceable = true;

    use CanScale;

}
