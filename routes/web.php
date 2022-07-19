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

Route::group(['prefix' => 'api'], function () {
    Route::get('namespaces', [\App\Http\Controllers\KubeController::class, 'namespaces']);
});
