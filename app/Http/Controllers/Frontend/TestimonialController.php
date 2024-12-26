<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Testimonials;

class TestimonialController extends Controller
{
    // show active testimonials
    public function index()
    {
        $testimonials = Testimonials::where('status', 1)->orderBy('created_at', 'DESC')->get();

        $testimonials = $testimonials->map(function ($service) {
            $service->image = env('APP_URL') . '/Testimonials/' . $service->image;
            return $service;
        });

        return $testimonials;
    }
}
