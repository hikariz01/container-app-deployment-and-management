<?php

namespace App\Http\Controllers\Edit;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DashboardController;
use App\Models\Cluster;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class UserClusterController extends Controller
{
    public function selectCluster(Request $request)
    {
        try {
            $namespaces = (new DashboardController())->getCluster()->getAllNamespaces();
        }
        catch (ModelNotFoundException $e) {
            $namespaces = ['not selected'];
        }

        $user = $request->user();

        $clusters = $user->clusters;


        return view('select_cluster.selectCluster', compact('namespaces', 'clusters'));
    }

    public function submitCluster(Request $request) {

        $cluster = Cluster::query()->where('id', $request->get('selectedCluster'))->firstOrFail();

        if ($request->user()->id === $cluster->user_id) {
            $request->session()->put('cluster_id', $request->get('selectedCluster'));

            $value = session('cluster_id');



            return redirect('select-cluster');
        }
        else {
            return abort(403, 'You are not owner of this cluster.');
        }

    }
}
