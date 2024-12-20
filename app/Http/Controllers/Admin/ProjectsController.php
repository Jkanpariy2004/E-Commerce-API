<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Projects;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProjectsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = Projects::orderBy('created_at', 'DESC')->get();

        if ($projects->isNotEmpty()) {
            $baseUrl = env('APP_URL');
            $projectsData = $projects->map(function ($project) use ($baseUrl) {
                return [
                    'id' => $project->id,
                    'title' => $project->title,
                    'slug' => $project->slug,
                    'short_desc' => $project->short_desc,
                    'content' => $project->content,
                    'construction_type' => $project->construction_type,
                    'sector' => $project->sector,
                    'location' => $project->location,
                    'image' => $project->image ? $baseUrl . '/Services/' . $project->image : null,
                    'status' => $project->status,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $projectsData,
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'project data not exist.',
            ], 404);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $project = Projects::find($id);

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
                    'image' => $project->image ? $baseUrl . '/Services/' . $project->image : null,
                    'status' => $project->status,
                ],
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Project Not Found',
            ], 404);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'slug' => 'nullable|unique:projects,slug',
            'short_desc' => 'required',
            'content' => 'required',
            'construction_type' => 'required',
            'sector' => 'required',
            'location' => 'required',
            'image' => 'required|mimes:jpg,jpeg,png|max:4096',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please Fix Validation Errors.',
                'errors' => $validator->errors(),
            ], 400);
        }

        $project = new Projects();

        $project->title = $request->title;
        $project->slug = preg_replace('/-+/', '-', preg_replace('/[^A-Za-z0-9-]+/', '-', strtolower($request->title)));
        $project->short_desc = $request->short_desc;
        $project->content = $request->content;
        $project->construction_type = $request->construction_type;
        $project->sector = $request->sector;
        $project->location = $request->location;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);

            $sanitizedName = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $originalName));

            $imageName = $sanitizedName . '-' . time() . '.' . $image->extension();
            $image->move(public_path('Projects'), $imageName);
            $project->image = $imageName;
        }
        $project->status = $request->status;

        $project->save();
        $baseUrl = env('APP_URL');

        return response()->json([
            'success' => true,
            'message' => 'Project Created Successfully.',
            'data' => [
                'id' => $project->id,
                'title' => $project->title,
                'slug' => $project->slug,
                'short_desc' => $project->short_desc,
                'content' => $project->content,
                'construction_type' => $project->construction_type,
                'sector' => $project->sector,
                'location' => $project->location,
                'image' => $project->image ? $baseUrl . '/Services/' . $project->image : null,
                'status' => $project->status,
            ],
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'slug' => 'nullable|unique:projects,slug',
            'short_desc' => 'required',
            'content' => 'required',
            'construction_type' => 'required',
            'sector' => 'required',
            'location' => 'required',
            'image' => 'nullable|mimes:jpg,jpeg,png|max:4096',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please Fix Validation Errors.',
                'errors' => $validator->errors(),
            ], 400);
        }

        $project = Projects::find($id);

        $project->title = $request->title;
        $project->slug = preg_replace('/-+/', '-', preg_replace('/[^A-Za-z0-9-]+/', '-', strtolower($request->title)));
        $project->short_desc = $request->short_desc;
        $project->content = $request->content;
        $project->construction_type = $request->construction_type;
        $project->sector = $request->sector;
        $project->location = $request->location;
        if ($request->hasFile('image')) {
            if ($project->image) {
                $oldImagePath = public_path('Projects/' . $project->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $image = $request->file('image');
            $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);

            $sanitizedName = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $originalName));

            $imageName = $sanitizedName . '-' . time() . '.' . $image->extension();
            $image->move(public_path('Projects'), $imageName);
            $project->image = $imageName;
        }
        $project->status = $request->status;

        $project->save();
        $baseUrl = env('APP_URL');

        return response()->json([
            'success' => true,
            'message' => 'Project Updated Successfully.',
            'data' => [
                'id' => $project->id,
                'title' => $project->title,
                'slug' => $project->slug,
                'short_desc' => $project->short_desc,
                'content' => $project->content,
                'construction_type' => $project->construction_type,
                'sector' => $project->sector,
                'location' => $project->location,
                'image' => $project->image ? $baseUrl . '/Services/' . $project->image : null,
                'status' => $project->status,
            ],
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $project = Projects::find($id);

        if ($project) {
            if ($project->image) {
                $oldImagePath = public_path('Projects/' . $project->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $project->delete();
            $baseUrl = env('APP_URL');

            return response()->json([
                'success' => true,
                'message' => 'Project Deleted Successfully.',
                'data' => [
                    'id' => $project->id,
                    'title' => $project->title,
                    'slug' => $project->slug,
                    'short_desc' => $project->short_desc,
                    'content' => $project->content,
                    'construction_type' => $project->construction_type,
                    'sector' => $project->sector,
                    'location' => $project->location,
                    'image' => $project->image ? $baseUrl . '/Services/' . $project->image : null,
                    'status' => $project->status,
                ],
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Project Not Found',
            ], 404);
        }
    }
}
