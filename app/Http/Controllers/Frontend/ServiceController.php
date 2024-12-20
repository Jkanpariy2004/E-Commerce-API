<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Services;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    // show active services
    public function index()
    {
        $services = Services::where('status', 1)->orderBy('created_at', 'DESC')->get();

        $services = $services->map(function ($service) {
            $service->image = env('APP_URL') . '/Services/' . $service->image;
            return $service;
        });

        return $services;
    }

    // show latest active services
    public function latestServices(Request $request)
    {
        $services = Services::where('status', 1)
            ->take($request->get('limit'))
            ->orderBy('created_at', 'DESC')
            ->get();

        $services = $services->map(function ($service) {
            $service->image = env('APP_URL') . '/Services/' . $service->image;
            return $service;
        });

        return $services;
    }

}
