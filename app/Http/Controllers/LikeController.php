<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function update(Request $request, $likeableType, $id): JsonResponse
    {
        $likeable = match($likeableType) {
            'posts' => Post::findOrFail($id),
            'comments' => Comment::findOrFail($id),
            default => null,
        };

        if (!$likeable) {
            return response()->json(['error' => 'Invalid likeable type'], 400);
        }

        $user = Auth::user();
        $existingLike = $likeable->likes()->where('user_id', $user->id)->first();

        if ($existingLike) {
            $existingLike->delete();
            return response()->json(['message' => 'Like removed successfully'], 200);
        }

        $like = $likeable->likes()->create(['user_id' => $user->id]);
        return response()->json(['message' => 'Like added successfully', 'like' => $like], 201);
    }
}
