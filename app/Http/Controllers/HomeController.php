<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
//     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
//        $namespaces = (new DashboardController())->getCluster()->getAllNamespaces();

        return redirect()->route('dashboard')->with('success', 'Logged in successfully.');
    }
}

