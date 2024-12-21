<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Articles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ArticlesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $blogs = Articles::orderBy('created_at', 'DESC')->get();

        if ($blogs->isNotEmpty()) {
            $baseurl = env('APP_URL');

            $data = $blogs->map(function ($blog) use ($baseurl) {
                return [
                    'id' => $blog->id,
                    'title' => $blog->title,
                    'slug' => $blog->slug,
                    'author' => $blog->author,
                    'content' => $blog->content,
                    'image' => $baseurl . '/Articales/' . $blog->image,
                    'status' => $blog->status,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data,
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No articles exist.',
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $blog = Articles::find($id);

        if ($blog) {
            $baseurl = env('APP_URL');

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $blog->id,
                    'title' => $blog->title,
                    'slug' => $blog->slug,
                    'auther' => $blog->auther,
                    'content' => $blog->content,
                    'image' => $baseurl . '/Articales/' . $blog->image,
                    'status' => $blog->status,
                ],
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Article Not Found',
            ], 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'slug' => 'nullable',
            'auther' => 'required',
            'content' => 'required',
            'image' => 'required|mimes:jpg,jpeg,png',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please Fix Validation Errors.',
                'error' => $validator->errors(),
            ], 400);
        }

        $blog = new Articles();
        $blog->title = $request->title;
        $blog->slug = $request->slug ? $request->slug : Str::slug($request->title);
        $blog->auther = $request->auther;
        $blog->content = $request->content;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);

            $sanitizedName = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $originalName));

            $imageName = $sanitizedName . '-' . time() . '.' . $image->extension();
            $image->move(public_path('Articles'), $imageName);
            $blog->image = $imageName;
        }
        $blog->status = $request->status;

        $blog->save();

        $baseurl = env('APP_URL');

        return response()->json([
            'success' => true,
            'message' => 'Blog Created Successfully.',
            'data' => [
                'id' => $blog->id,
                'title' => $blog->title,
                'slug' => $blog->slug,
                'auther' => $blog->auther,
                'content' => $blog->content,
                'image' => $baseurl . '/Articales/' . $blog->image,
                'status' => $blog->status,
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
            'slug' => 'nullable',
            'auther' => 'required',
            'content' => 'required',
            'image' => 'required|mimes:jpg,jpeg,png',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please fix validation errors.',
                'error' => $validator->errors(),
            ], 400);
        }

        $blog = Articles::find($id);

        if (!$blog) {
            return response()->json([
                'success' => false,
                'message' => 'Article not found.',
            ], 404);
        }

        $blog->title = $request->title;
        $blog->slug = $request->slug ? $request->slug : Str::slug($request->title);
        $blog->auther = $request->auther;
        $blog->content = $request->content;

        if ($request->hasFile('image')) {
            if ($blog->image) {
                $oldImagePath = public_path('Articles/' . $blog->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $image = $request->file('image');
            $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);

            $sanitizedName = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $originalName));

            $imageName = $sanitizedName . '-' . time() . '.' . $image->extension();
            $image->move(public_path('Articles'), $imageName);
            $blog->image = $imageName;
        }

        $blog->status = $request->status;
        $blog->save();

        $baseurl = env('APP_URL');

        return response()->json([
            'success' => true,
            'message' => 'Blog updated successfully.',
            'data' => [
                'id' => $blog->id,
                'title' => $blog->title,
                'slug' => $blog->slug,
                'auther' => $blog->auther,
                'content' => $blog->content,
                'image' => $baseurl . '/Articales/' . $blog->image,
                'status' => $blog->status,
            ],
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $blog = Articles::find($id);

        if ($blog) {
            if ($blog->image) {
                $oldImagePath = public_path('Articles/' . $blog->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $blog->delete();
            $baseurl = env('APP_URL');

            return response()->json([
                'success' => true,
                'message' => 'Blog Deleted Successfully.',
                'data' => [
                    'id' => $blog->id,
                    'title' => $blog->title,
                    'slug' => $blog->slug,
                    'auther' => $blog->auther,
                    'content' => $blog->content,
                    'image' => $baseurl . '/Articales/' . $blog->image,
                    'status' => $blog->status,
                ],
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Blog Not Found.',
            ], 404);
        }
    }
}
