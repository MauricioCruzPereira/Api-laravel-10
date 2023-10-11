<?php

namespace App\Http\Controllers;

use App\Services\HelloWorldService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HelloWorldController extends Controller
{
    public function get() : JsonResponse{
        $helloWorld = new HelloWorldService();
        return response()->json($helloWorld->get());
    }
}
