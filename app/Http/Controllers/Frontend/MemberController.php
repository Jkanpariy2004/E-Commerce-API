<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Members;

class MemberController extends Controller
{
    public function index()
    {
        $members = Members::where('status', 1)->orderBy('created_at', 'DESC')->get();

        $members = $members->map(function ($service) {
            $service->image = env('APP_URL') . '/Members/' . $service->image;
            return $service;
        });

        return $members;
    }
}
