<?php

namespace App\Http\Controllers;

use App\Models\Tweet;
use Illuminate\Http\Request;

class TweetLikeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Tweet $tweet, Request $request)
    {
        $user = $request->user();

        if ($tweet->likedBy()->where('user_id', $user->id)->exists()) {
            // If already liked, unlike it
            $tweet->likedBy()->detach($user->id);
            $liked = false;
        } else {
            // If not liked, like it
            $tweet->likedBy()->attach($user->id);
            $liked = true;
        }

        // Return the new state + count so the frontend can update immediately
        return response()->json([
            'liked' => $liked,
            'count' => $tweet->likedBy()->count(),
        ]);
    }
}