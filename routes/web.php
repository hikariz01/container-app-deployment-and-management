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

Route::get('create', [App\Http\Controllers\HomeController::class, 'create'])->name('create');

Route::get('test', [App\Http\Controllers\TestController::class, 'test'])->name('test');

Route::get('dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

Route::get('service', [\App\Http\Controllers\DashboardController::class, 'service'])->name('service');

Route::get('config_storage', [\App\Http\Controllers\DashboardController::class, 'config_storage'] )->name('config_storage');

Route::get('cluster', [\App\Http\Controllers\DashboardController::class, 'cluster'])->name('cluster');


Route::group(['prefix' => 'workloads'], function () {
    Route::get('deployment/{namespace}/{name}', [\App\Http\Controllers\DeploymentController::class, 'deploymentDetails'])->name('deployment-details');

    Route::get('daemonset/{namespace}/{name}', [\App\Http\Controllers\DaemonsetController::class, 'daemonsetDetails'])->name('daemonset-details');

});
