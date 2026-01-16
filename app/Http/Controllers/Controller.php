<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    
    // REMOVE THIS METHOD - It's causing the Closure error
    // public function middleware($middleware, array $options = [])
    // {
    //     return app()->make('router')->middleware($middleware, $options);
    // }
}