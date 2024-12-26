<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Articles;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    // show active blogs
    public function index()
    {
        $services = Articles::where('status', 1)->orderBy('created_at', 'DESC')->get();

        $services = $services->map(function ($service) {
            $service->image = env('APP_URL') . '/Articles/' . $service->image;
            return $service;
        });

        return $services;
    }

    // show latest active blogs
    public function latestArticles(Request $request)
    {
        $services = Articles::where('status', 1)
            ->take($request->get('limit'))
            ->orderBy('created_at', 'DESC')
            ->get();

        $services = $services->map(function ($service) {
            $service->image = env('APP_URL') . '/Articles/' . $service->image;
            return $service;
        });

        return $services;
    }
}
