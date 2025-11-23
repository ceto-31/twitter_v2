<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserProfileController extends Controller
{
    public function show(User $user)
    {
        // Get tweets for this specific user, ordered by newest
        $tweets = $user->tweets()
            ->with(['user', 'likedBy'])
            ->withCount('likedBy') // count likes for each tweet
            ->latest()
            ->get();

        // Calculate total likes received across ALL their tweets
        $totalLikesReceived = $tweets->sum('liked_by_count');

        return view('users.show', compact('user', 'tweets', 'totalLikesReceived'));
    }
}