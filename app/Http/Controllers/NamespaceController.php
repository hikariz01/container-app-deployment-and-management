<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NamespaceController extends Controller
{
    public function selectedNamespace(Request $request) {

        if($request->has('namespace'))
            $ns = $request->input('namespace');
        else
            $ns = 'default';

        // fetch your namespace

    }
}
