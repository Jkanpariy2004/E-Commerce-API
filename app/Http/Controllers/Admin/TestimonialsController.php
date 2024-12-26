<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonials;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TestimonialsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $testimonial = Testimonials::orderBy('created_at', 'DESC')->get();

        if ($testimonial->isNotEmpty()) {
            $baseUrl = env('APP_URL');
            $testimonialData = $testimonial->map(function ($testimonials) use ($baseUrl) {
                return [
                    'id' => $testimonials->id,
                    'testimonial' => $testimonials->testimonial,
                    'citation' => $testimonials->citation,
                    'designations'=>$testimonials->designations,
                    'image' => $testimonials->image ? $baseUrl . '/Testimonials/' . $testimonials->image : null,
                    'status' => $testimonials->status,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $testimonialData,
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'testimonial data not exist.',
            ], 404);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $testimonial = Testimonials::find($id);

        if ($testimonial) {
            $baseUrl = env('APP_URL');

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $testimonial->id,
                    'testimonial' => $testimonial->testimonial,
                    'citation' => $testimonial->citation,
                    'designations'=>$testimonial->designations,
                    'image' => $testimonial->image ? $baseUrl . '/Testimonials/' . $testimonial->image : null,
                    'status' => $testimonial->status,
                ],
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'testimonial not found.',
            ], 404);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'testimonial' => 'required',
            'citation' => 'required',
            'designations'=>'required',
            'image' => 'required|mimes:jpg,jpeg,png|max:4096',
            'status' => 'required'
        ]);

        if ($validator->fails()) {
            return response([
                'success' => false,
                'message' => 'Please Fix Validation error.',
                'error' => $validator->errors(),
            ], 400);
        }

        $testimonial = new Testimonials();

        $testimonial->testimonial = $request->testimonial;
        $testimonial->citation = $request->citation;
        $testimonial->designations = $request->designations;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);

            $sanitizedName = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $originalName));

            $imageName = $sanitizedName . '-' . time() . '.' . $image->extension();
            $image->move(public_path('Testimonials'), $imageName);
            $testimonial->image = $imageName;
        }

        $testimonial->status = $request->status;

        $testimonial->save();
        $baseUrl = env('APP_URL');

        return response()->json([
            'success' => true,
            'message' => 'Testimonials Created Successfully.',
            'data' => [
                'id' => $testimonial->id,
                'testimonial' => $testimonial->testimonial,
                'citation' => $testimonial->citation,
                'designations'=>$testimonial->designations,
                'image' => $testimonial->image ? $baseUrl . '/Testimonials/' . $testimonial->image : null,
                'status' => $testimonial->status,
            ],
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'testimonial' => 'required',
            'citation' => 'required',
            'designations'=>'required',
            'image' => 'nullable|mimes:jpg,jpeg,png|max:4096',
            'status' => 'required'
        ]);

        if ($validator->fails()) {
            return response([
                'success' => false,
                'message' => 'Please Fix Validation error.',
                'error' => $validator->errors(),
            ], 400);
        }

        $testimonial = Testimonials::find($id);

        if ($testimonial) {
            $testimonial->testimonial = $request->testimonial;
            $testimonial->citation = $request->citation;
            $testimonial->designations = $request->designations;

            if ($request->hasFile('image')) {
                if ($testimonial->image) {
                    $oldImagePath = public_path('Testimonials/' . $testimonial->image);
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                $image = $request->file('image');
                $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);

                $sanitizedName = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $originalName));

                $imageName = $sanitizedName . '-' . time() . '.' . $image->extension();
                $image->move(public_path('Testimonials'), $imageName);
                $testimonial->image = $imageName;
            }

            $testimonial->status = $request->status;

            $testimonial->save();
            $baseUrl = env('APP_URL');

            return response()->json([
                'success' => true,
                'message' => 'Testimonials Updated Successfully.',
                'data' => [
                    'id' => $testimonial->id,
                    'testimonial' => $testimonial->testimonial,
                    'citation' => $testimonial->citation,
                    'designations'=>$testimonial->designations,
                    'image' => $testimonial->image ? $baseUrl . '/Testimonials/' . $testimonial->image : null,
                    'status' => $testimonial->status,
                ],
            ], 200);
        } else {
            return response([
                'success' => false,
                'message' => 'Testimonial Not Found'
            ], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $testimonial = Testimonials::find($id);

        if ($testimonial) {
            if ($testimonial->image) {
                $oldImagePath = public_path('Testimonials/' . $testimonial->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $testimonial->delete();

            return response([
                'success' => true,
                'message' => 'Testimonial Deleted Successfully.',
                'data' => $testimonial
            ], 200);
        } else {
            return response([
                'success' => true,
                'message' => 'Testimonial Not Found.',
            ], 404);
        }
    }
}
