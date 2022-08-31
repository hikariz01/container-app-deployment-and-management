<?php

namespace App\Http\Controllers\Create;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DashboardController;

class CreateController extends DashboardController
{
    public function create()
    {
        $cluster = $this->getCluster();

        $namespaces = $cluster->getAllNamespaces();

        return view('create', compact('namespaces'));
    }
}
