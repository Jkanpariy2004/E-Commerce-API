<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Services;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ServicesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $services = Services::orderBy('created_at', 'DESC')->get();

        if ($services->isNotEmpty()) {
            $baseUrl = env('APP_URL');
            $servicesData = $services->map(function ($service) use ($baseUrl) {
                return [
                    'id' => $service->id,
                    'title' => $service->title,
                    'slug' => $service->slug,
                    'short_desc' => $service->short_desc,
                    'content' => $service->content,
                    'image' => $service->image ? $baseUrl . '/Services/' . $service->image : null,
                    'status' => $service->status,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $servicesData,
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'services data not exist.',
            ], 404);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $service = Services::find($id);

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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'slug' => 'nullable|unique:services,slug',
            'short_desc' => 'required',
            'content' => 'required',
            'image' => 'required|mimes:jpg,jpeg,png|max:4096',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please Fix Validation error.',
                'error' => $validator->errors(),
            ], 400);
        }

        $service = new Services();
        $service->title = $request->title;
        $service->slug = preg_replace('/-+/', '-', preg_replace('/[^A-Za-z0-9-]+/', '-', strtolower($request->title)));
        $service->short_desc = $request->short_desc;
        $service->content = $request->content;

        // If User Upload Big Size Image To reduce in laravel to use "intervantion image library"
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);

            $sanitizedName = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $originalName));

            $imageName = $sanitizedName . '-' . time() . '.' . $image->extension();
            $image->move(public_path('Services'), $imageName);
            $service->image = $imageName;
        }
        $service->status = $request->status;

        $service->save();
        $baseUrl = env('APP_URL');

        return response()->json([
            'success' => true,
            'message' => 'Services Created Successfully.',
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
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'slug' => 'nullable|unique:services,slug',
            'short_desc' => 'required',
            'content' => 'required',
            'image' => 'nullable|mimes:jpg,jpeg,png|max:4096',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please Fix Validation error.',
                'error' => $validator->errors(),
            ], 400);
        }

        $service = Services::find($id);
        $service->title = $request->title;
        $service->slug = preg_replace('/-+/', '-', preg_replace('/[^A-Za-z0-9-]+/', '-', strtolower($request->title)));
        $service->short_desc = $request->short_desc;
        $service->content = $request->content;

        if ($request->hasFile('image')) {
            if ($service->image) {
                $oldImagePath = public_path('Services/' . $service->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $image = $request->file('image');
            $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);

            $sanitizedName = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $originalName));

            $imageName = $sanitizedName . '-' . time() . '.' . $image->extension();
            $image->move(public_path('Services'), $imageName);
            $service->image = $imageName;
        }
        $service->status = $request->status;

        $service->save();
        $baseUrl = env('APP_URL');

        return response()->json([
            'success' => true,
            'message' => 'Services Updated Successfully.',
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
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $service = Services::find($id);

        if ($service) {
            if ($service->image) {
                $oldImagePath = public_path('Services/' . $service->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $service->delete();

            return response()->json([
                'success' => true,
                'message' => 'Service deleted Successfully.',
                'data' => $service,
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Service Not Found.',
            ], 200);
        }
    }
}
