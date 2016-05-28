<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{

    public function swagger()
    {
        return \Swagger\scan(base_path('app'));
    }
}