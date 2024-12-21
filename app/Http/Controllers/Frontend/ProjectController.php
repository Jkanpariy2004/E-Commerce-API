<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Projects;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    // show active services
    public function index()
    {
        $services = Projects::where('status', 1)->orderBy('created_at', 'DESC')->get();

        $services = $services->map(function ($service) {
            $service->image = env('APP_URL') . '/Projects/' . $service->image;
            return $service;
        });

        return $services;
    }

    // show latest active services
    public function latestServices(Request $request)
    {
        $services = Projects::where('status', 1)
            ->take($request->get('limit'))
            ->orderBy('created_at', 'DESC')
            ->get();

        $services = $services->map(function ($service) {
            $service->image = env('APP_URL') . '/Projects/' . $service->image;
            return $service;
        });

        return $services;
    }
}
