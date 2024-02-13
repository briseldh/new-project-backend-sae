<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LikeController extends Controller
{
    public function getUserLikes(Request $request)
    {
        $likes = DB::select('select * from new_project_backend.post_likes where user_id = :userId', ['userId' => $request->user()->id]);
        // $likesCount = Like::where('user_id', '<=', $request->user()->id)->count();
        $allLikes = Like::all();

        return response()->json(['likes' => $likes, 'allLikes' => $allLikes], 200);
    }

    public function getAllLikes(Request $request)
    {
        $allLikes = Like::all();

        return response()->json(['allLikes' => $allLikes], 200);
    }

    public function like(Post $post)
    {
        $liker = auth()->user(); // Liker in this case is the logged in user

        foreach ($liker->likes as $likeCombination) {

            if ($likeCombination->pivot->post_id == $post->id) {

                return response()->json(['message' => 'This post is liked alredy!'], 400);
            }
        }

        $liker->likes()->attach($post->id);

        return response()->json(['message' => 'The post ' . $post->id . ' is liked by ' . $liker->name], 200);
    }

    public function dislike(Post $post)
    {
        $liker = auth()->user();

        $liker->likes()->detach($post->id);

        return response()->json(['message' => 'The post ' . $post->id . ' is disliked by ' . $liker->name], 200);
    }
}
