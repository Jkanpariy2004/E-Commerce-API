<?php

namespace App\Http\Controllers\Admin;

use App\Models\Members;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class MembersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $members = Members::orderBy('created_at', 'DESC')->get();

        if ($members->isNotEmpty()) {
            $baseUrl = env('APP_URL');
            $membersData = $members->map(function ($members) use ($baseUrl) {
                return [
                    'id' => $members->id,
                    'name' => $members->name,
                    'image' => $members->image ? $baseUrl . '/Members/' . $members->image : null,
                    'job_title' => $members->job_title,
                    'linkedin_url' => $members->linkedin_url,
                    'status' => $members->status,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $membersData,
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Members data not exist.',
            ], 404);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $members = Members::find($id);

        if ($members) {
            $baseUrl = env('APP_URL');

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $members->id,
                    'name' => $members->name,
                    'image' => $members->image ? $baseUrl . '/Members/' . $members->image : null,
                    'job_title' => $members->job_title,
                    'linkedin_url' => $members->linkedin_url,
                    'status' => $members->status,
                ]
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Members data not exist.',
            ], 404);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'image' => 'required|mimes:jpg,jpeg,png|max:4096',
            'job_title' => 'required',
            'linkedin_url' => 'required',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please Fix Validation Erorr',
                'errors' => $validator->errors(),
            ], 400);
        }

        $members = new Members();
        $members->name = $request->name;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);

            $sanitizedName = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $originalName));

            $imageName = $sanitizedName . '-' . time() . '.' . $image->extension();
            $image->move(public_path('Members'), $imageName);
            $members->image = $imageName;
        }
        $members->job_title = $request->job_title;
        $members->linkedin_url = $request->linkedin_url;
        $members->status = $request->status;

        $members->save();

        $baseUrl = env('APP_URL');

        return response()->json([
            'success' => true,
            'message' => 'Members created successfully',
            'data' => [
                'id' => $members->id,
                'name' => $members->name,
                'image' => $members->image ? $baseUrl . '/Members/' . $members->image : null,
                'job_title' => $members->job_title,
                'linkedin_url' => $members->linkedin_url,
                'status' => $members->status,
            ]
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'image' => 'nullable|mimes:jpg,jpeg,png|max:4096',
            'job_title' => 'required',
            'linkedin_url' => 'required',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please Fix Validation Erorr',
                'errors' => $validator->errors(),
            ], 400);
        }

        $members = Members::find($id);

        if ($members) {
            $members->name = $request->name;
            if ($request->hasFile('image')) {
                if ($members->image) {
                    $oldImagePath = public_path('Members/' . $members->image);
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                $image = $request->file('image');
                $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);

                $sanitizedName = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $originalName));

                $imageName = $sanitizedName . '-' . time() . '.' . $image->extension();
                $image->move(public_path('Members'), $imageName);
                $members->image = $imageName;
            }
            $members->job_title = $request->job_title;
            $members->linkedin_url = $request->linkedin_url;
            $members->status = $request->status;

            $members->save();

            $baseUrl = env('APP_URL');

            return response()->json([
                'success' => true,
                'message' => 'Members Updated successfully',
                'data' => [
                    'id' => $members->id,
                    'name' => $members->name,
                    'image' => $members->image ? $baseUrl . '/Members/' . $members->image : null,
                    'job_title' => $members->job_title,
                    'linkedin_url' => $members->linkedin_url,
                    'status' => $members->status,
                ]
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Members not found',
            ], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $members = Members::find($id);

        if ($members) {
            if ($members->image) {
                $oldImagePath = public_path('Members/' . $members->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $members->delete();

            return response()->json([
                'success' => true,
                'message' => 'Members Deleted successfully',
                'data' => $members
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Members not found',
            ], 404);
        }
    }
}
