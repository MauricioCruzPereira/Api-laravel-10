<?php

use App\Http\Controllers\HelloWorldController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

route::controller(HelloWorldController::class)->group(function(){
    route::get('/','get');
});
