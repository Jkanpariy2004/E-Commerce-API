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
        $projects = Projects::where('status', 1)->orderBy('created_at', 'DESC')->get();

        $projects = $projects->map(function ($project) {
            $project->image = env('APP_URL') . '/Projects/' . $project->image;
            return $project;
        });

        return $projects;
    }

    // show latest active services
    public function latestServices(Request $request)
    {
        $projects = Projects::where('status', 1)
            ->take($request->get('limit'))
            ->orderBy('created_at', 'DESC')
            ->get();

        $projects = $projects->map(function ($project) {
            $project->image = env('APP_URL') . '/Projects/' . $project->image;
            return $project;
        });

        return $projects;
    }

    public function slug($slug)
    {
        $project = Projects::where('slug', $slug)->first();

        if ($project) {
            $baseUrl = env('APP_URL');

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $project->id,
                    'title' => $project->title,
                    'slug' => $project->slug,
                    'short_desc' => $project->short_desc,
                    'content' => $project->content,
                    'construction_type' => $project->construction_type,
                    'sector' => $project->sector,
                    'location' => $project->location,
                    'image' => $project->image ? $baseUrl . '/Projects/' . $project->image : null,
                    'status' => $project->status,
                ],
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'project data not exist.',
            ], 404);
        }
    }
}
