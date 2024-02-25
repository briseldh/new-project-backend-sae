<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\FileUpload;
use App\Models\Like;
use App\Models\Post;
use App\Models\ProfilePicUpload;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{

    public function insert(Request $request)
    {
        try {
            $policyResp = Gate::inspect('insert', Post::class);

            if ($policyResp->allowed()) {

                $rules = [
                    // 'user_id' => 'required|numeric', //Needed only when using Postman
                    'title' => 'required|min:5|max:50',
                    'text' => 'min:10|max:2000',
                    'avatar' => 'required|mimes:jpeg,pdf|max:2048'
                ];

                $validator = Validator::make($request->all(), $rules);

                if ($validator->fails()) {
                    return response()->json(['message' => $validator->errors()], 400);
                }

                $post = new Post();
                $post->user_id = $request->user()->id; // For Postman-> $request->input('user_id');
                $post->title = $request->input('title');
                $post->text = $request->input('text');

                $post->save();


                //===================UPLOAD=======================================================//
                $file = $request->file('avatar');
                // return response()->json(['data' => $file], 200);

                if ($request->hasFile('avatar')) {

                    $fileName = date('Y-m-d') . '_' . time() . $file->getClientOriginalName();
                    $path = 'storage/' . $fileName;
                    $onlyName = explode('.', $file->getClientOriginalName());

                    $post_images = new FileUpload();

                    $post_images->post_id = $post->id;
                    $post_images->path = $path;
                    $post_images->alt_text = $onlyName[0];
                    $post_images->uploaded_at = date('Y-m-d H:i:s');
                    $post_images->user_id = $request->user()->id;

                    $post_images->save();

                    // Storage::putFileAs('public', $file, $fileName);
                    $request->file('avatar')->storeAs('public', $fileName);


                    return response()->json(['message' => $policyResp->message(), 'postData' => $post], 201);
                }

                //=======My Method=======//
                // if ($request->hasFile('avatar')) {

                //     $fileName = date('Y-m-d') . '_' . time() . $file->getClientOriginalName();
                //     $path = 'public/' . $fileName;
                //     $onlyName = explode('.', $file->getClientOriginalName());

                //     $post_images = new FileUpload();

                //     $post_images->post_id = $post->id;
                //     $post_images->path = $path;
                //     $post_images->alt_text = $onlyName[0];
                //     $post_images->uploaded_at = date('Y-m-d H:i:s');

                //     $post_images->save();

                //     Storage::putFileAs('public', $file, $fileName);


                //     return response()->json(['message' => $policyResp->message(), 'postData' => $post], 201);
                // }

                //========Mathias Method============//
                // if ($request->hasFile('avatar')) {
                //     $extension = '.' . $request->file('avatar')->extension();
                //     $path = $request->file('avatar')->storeAs('images', 'emer-kot2' . $extension, 'public');

                //     return response()->json(['message' => "Inserted Successfuly", 'postData' => $post], 201);
                // }

                //===================UPLOAD=======================================================//

                return response()->json(['message' => $policyResp->message(), 'postData' => $post], 201);
            }

            return response()->json(['message' => $policyResp->message()], 403);
        } catch (Exception $e) {
            return response()->json(['message' => '===FATAL=== ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $post = Post::find($id);

            $policyResp = Gate::inspect('update', $post);

            if ($policyResp->allowed()) {
                $rules = [
                    'title' => 'min:5|max:50',
                    'text' => 'min:10|max:2000',
                ];

                $validator = Validator::make($request->all(), $rules);

                if ($validator->fails()) {
                    return response()->json(['message' => $validator->errors()], 400);
                }

                $post->title = $request->input('title');
                $post->text = $request->input('text');

                $post->save();

                return response()->json(['message' => $policyResp->message()], 200);
            }

            return response()->json(['message' => $policyResp->message()], 403);
        } catch (Exception $e) {
            return response()->json(['message' => '===FATAL=== ' . $e->getMessage()], 500);
        }
    }

    public function delete(string $id)
    {
        try {
            $post = Post::find($id);

            $postImage = FileUpload::where('post_id', $post->id)->get();


            $policyResp = Gate::inspect('delete', $post);

            if ($policyResp->allowed()) {

                $path = explode('/', $postImage[0]->path);

                $fileName = $path[1];

                Storage::delete('public/' . $fileName);

                $post->delete();

                return response()->json(['message' => $policyResp->message()], 200);
            }

            return response()->json(['message' => $policyResp->message()], 403);
        } catch (Exception $e) {
            return response()->json(['message' => '===FATAL=== ' . $e->getMessage()], 500);
        }
    }

    public function getById(string $id)
    {
        try {
            $post = Post::find($id);

            $policyResp = Gate::inspect('getById', $post);

            if ($policyResp->allowed()) {

                return response()->json(['message' => $policyResp->message(), 'post' => $post], 200);
            }

            return response()->json(['message' => $policyResp->message()], 403);
        } catch (Exception $e) {
            return response()->json(['message' => '===FATAL=== ' . $e->getMessage()], 500);
        }
    }

    public function getAll(Request $request)
    {

        $posts = Post::with('file', 'comments', 'postLikes')->get();
        $comments = Comment::with('user')->get();
        $allLikes = Like::all();
        $allProfilePics = ProfilePicUpload::all();

        return response()->json(['posts' => $posts, 'comments' => $comments, 'allLikes' => $allLikes, 'allProfilePics' => $allProfilePics], 200);
    }
}
