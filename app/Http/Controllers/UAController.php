<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UAController extends Controller
{
    public function show()
    {
        return view('useragentapi');
    }
}
