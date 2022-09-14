<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome2');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('test', [App\Http\Controllers\TestController::class, 'test'])->name('test');

Route::get('dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

Route::get('service', [\App\Http\Controllers\DashboardController::class, 'service'])->name('service');

Route::get('config_storage', [\App\Http\Controllers\DashboardController::class, 'config_storage'] )->name('config_storage');

Route::get('cluster', [\App\Http\Controllers\DashboardController::class, 'cluster'])->name('cluster');


Route::group(['prefix' => 'workloads'], function () {
    Route::get('deployment/{namespace}/{name}', [\App\Http\Controllers\DeploymentController::class, 'deploymentDetails'])->name('deployment-details');

    Route::get('daemonset/{namespace}/{name}', [\App\Http\Controllers\DaemonsetController::class, 'daemonsetDetails'])->name('daemonset-details');

    Route::get('pod/{namespace}/{name}', [\App\Http\Controllers\PodController::class, 'podDetails'])->name('pod-details');

    Route::get('job/{namespace}/{name}', [\App\Http\Controllers\JobController::class, 'jobDetails'])->name('job-details');

    Route::get('cronjob/{namespace}/{name}', [\App\Http\Controllers\CronJobController::class, 'cronjobDetails'])->name('cronjob-details');

    Route::get('replicaset/{namespace}/{name}', [\App\Http\Controllers\ReplicasetController::class, 'replicasetDetails'])->name('replicaset-details');

    Route::get('statefulset/{namespace}/{name}', [\App\Http\Controllers\StatefulSetController::class, 'statefulsetDetails'])->name('statefulset-details');

});

Route::group(['prefix' => 'service'], function () {
   Route::get('services/{namespace}/{name}', [\App\Http\Controllers\ServiceController::class, 'serviceDetails'])->name('service-details');

   Route::get('ingress/{namespace}/{name}', [\App\Http\Controllers\IngressController::class, 'ingressDetails'])->name('ingress-details');

   Route::get('ingressclass/{name}', [\App\Http\Controllers\IngressClassController::class, 'ingressclassDetails'])->name('ingressclass-details');
});

Route::group(['prefix' => 'config_storage'], function () {
    Route::get('configmap/{namespace}/{name}', [\App\Http\Controllers\ConfigmapController::class, 'configmapDetails'])->name('configmap-details');

    Route::get('secret/{namespace}/{name}', [\App\Http\Controllers\SecretController::class, 'secretDetails'])->name('secret-details');

    Route::get('pvc/{namespace}/{name}', [\App\Http\Controllers\PvcController::class, 'pvcDetails'])->name('pvc-details');

    Route::get('storageclass/{name}', [\App\Http\Controllers\StorageclassController::class, 'storageclassDetails'])->name('storageclass-details');

});

Route::group(['prefix' => 'cluster'], function () {
   Route::get('namespace/{name}', [\App\Http\Controllers\NamespaceController::class, 'namespaceDetails'])->name('namespace-details');

   Route::get('node/{name}', [\App\Http\Controllers\NodeController::class, 'nodeDetails'])->name('node-details');

   Route::get('persistentvolume/{name}', [\App\Http\Controllers\PersistentVolumeController::class, 'pvDetails'])->name('pv-details');

   Route::get('clusterrole/{name}', [\App\Http\Controllers\ClusterRoleController::class, 'clusterroleDetails'])->name('clusterrole-details');

   Route::get('clusterrolebinding/{name}', [\App\Http\Controllers\ClusterRoleBindingController::class, 'clusterrolebindingDetails'])->name('clusterrolebinding-details');

   Route::get('serviceaccount/{namespace}/{name}', [\App\Http\Controllers\ServiceAccountController::class, 'serviceaccountDetails'])->name('serviceaccount-details');

   Route::get('role/{namespace}/{name}', [\App\Http\Controllers\RoleController::class, 'roleDetails'])->name('role-details');

   Route::get('rolebinding/{namespace}/{name}', [\App\Http\Controllers\RoleBindingController::class, 'rolebindingDetails'])->name('rolebinding-details');

});

Route::group(['prefix' => 'create'], function () {


});

//Create Section

Route::get('create', [\App\Http\Controllers\Create\CreateController::class, 'create'])->name('create');
//Route::view('create', 'create');
Route::post('create', [\App\Http\Controllers\Create\CreateController::class, 'createResource'])->name('create-resource');



Route::post('result', [\App\Http\Controllers\Create\CreateController::class, 'result'])->name('result');
Route::view('result-page', 'result.result');


//Edit Section
//Route::post('workloads', )
