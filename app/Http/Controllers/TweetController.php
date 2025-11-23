<?php

namespace App\Http\Controllers;

use App\Models\Tweet;
use App\Http\Requests\StoreTweetRequest; // Import our validator
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TweetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get tweets with the user info and the count of likes
        // 'latest()' is a shortcut for orderBy('created_at', 'desc')
        $tweets = Tweet::with(['user', 'likedBy'])
            ->withCount('likedBy') // Adds a 'liked_by_count' attribute
            ->latest()
            ->get();

        return view('tweets.index', compact('tweets'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTweetRequest $request)
    {
        // Validation is handled automatically by StoreTweetRequest
        
        // Create the tweet for the currently authenticated user
        $request->user()->tweets()->create($request->validated());

        return redirect()->route('tweets.index')->with('success', 'Tweet posted!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tweet $tweet)
    {
        // Security: Ensure the user owns this tweet
        if ($tweet->user_id !== Auth::id()) {
            abort(403);
        }

        return view('tweets.edit', compact('tweet'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreTweetRequest $request, Tweet $tweet)
    {
        // Security: Ensure the user owns this tweet
        if ($tweet->user_id !== Auth::id()) {
            abort(403);
        }

        $tweet->update($request->validated());

        return redirect()->route('tweets.index')->with('success', 'Tweet updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tweet $tweet)
    {
        // Security: Ensure the user owns this tweet
        if ($tweet->user_id !== Auth::id()) {
            abort(403);
        }

        $tweet->delete();

        return redirect()->route('tweets.index')->with('success', 'Tweet deleted.');
    }
}