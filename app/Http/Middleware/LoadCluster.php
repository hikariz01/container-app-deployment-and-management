<?php

namespace App\Http\Middleware;

use App\Http\Controllers\DashboardController;
use App\Models\Cluster;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class loadCluster
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        $cluster = Cluster::query()->where('id', session('cluster_id', '1'))->first();

        if ($cluster === null) {
            return redirect(route('select-cluster'));
        }

        View::share('selected_cluster_name', $cluster->name??'not selected');

        DashboardController::$api_url = $cluster->url;

        return $next($request);
    }
}
