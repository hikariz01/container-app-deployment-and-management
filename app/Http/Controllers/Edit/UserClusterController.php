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

        if ($clusters === null) {
            return redirect()->route('edit-cluster');
        }

        return view('select_cluster.selectCluster', compact('namespaces', 'clusters'));
    }

    public function submitCluster(Request $request) {

        $cluster = Cluster::query()->where('id', $request->get('selectedCluster'))->firstOrFail();

        if ($request->user()->id === $cluster->user_id) {
            $request->session()->put('cluster_id', $request->get('selectedCluster'));

            $value = session('cluster_id');



            return redirect()->back()->with('success', 'Cluster [' . $cluster->name . '] selected successfully.');
        }
        else {
            return abort(403, 'You are not owner of this cluster.');
        }

    }

    public function editCluster(Request $request) {

        try {
            $namespaces = (new DashboardController())->getCluster()->getAllNamespaces();
        } catch (ModelNotFoundException $e) {
            $namespaces = ['not selected'];
        }

        $user = $request->user();

        $clusters = $user->clusters;

        return view('select_cluster.editCluster', compact('namespaces', 'clusters'));
    }

    public function submitEdit(Request $request) {
        $id = $request->get('id');
        $name = $request->get('editClusterName');
        $url = $request->get('editKubeURL');
        $token = $request->get('editKubeToken');
        $cacert = $request->get('editCacert');

        $cluster = Cluster::query()->where('id', $id)->first();

        if ($cluster->update([
            'name'=>$name,
            'url'=>$url,
            'token'=>$token,
            'cacert'=>$cacert
        ])) {
            return redirect()->back()->with('success', 'Cluster [' . $cluster->name . '] updated successfully.');
        }
        else {
            return redirect()->back()->with('error', 'There is an error.');
        }

    }

    public function addCluster(Request $request) {
        $user = $request->user();

        $cluster = new Cluster;
        $cluster->url = $request->get('kubeURL');
        $cluster->token = $request->get('kubeToken');
        $cluster->cacert = $request->get('cacert');
        $cluster->name = $request->get('clusterName');
        $cluster->user_id = $user->id;

        if ($cluster->save()) {
            return redirect()->back()->with('success', 'Cluster [' . $cluster->name . '] registered successfully.');
        }

    }

    public function deleteCluster(Request $request) {

        $cluster = Cluster::query()->where('id', $request->get('deleteValue'))->firstOrFail();

        if ($request->user()->id === $cluster->user_id) {
            if ($cluster->delete()) {
                return redirect()->back()->with('success', 'Cluster [' . $cluster->name . '] deleted successfully.');
            }
        }
        else {
            return abort(403, 'You are not owner of this cluster.');
        }
    }
}
