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

    public function slug($slug)
    {
        $service = Services::where('slug', $slug)->first();

        if ($service) {
            $baseUrl = env('APP_URL');

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $service->id,
                    'title' => $service->title,
                    'slug' => $service->slug,
                    'short_desc' => $service->short_desc,
                    'content' => $service->content,
                    'image' => $service->image ? $baseUrl . '/Services/' . $service->image : null,
                    'status' => $service->status,
                ],
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'service not found.',
            ], 404);
        }
    }
}
