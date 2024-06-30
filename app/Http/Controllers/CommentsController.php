<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentsController extends Controller
{
    public function index($postId): JsonResponse
    {
        $post = Post::findOrFail($postId);

        $comments = $post->comments()
            ->withCount('likes')
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->paginate(10); // Apply pagination before transforming

        // Transform each comment
        $comments->getCollection()->transform(function ($comment) {
            $user = Auth::guard('sanctum')->user();
            $comment->liked = $user ? $comment->likes->contains('user_id', $user->id) : false;
            return $comment->makeHidden('likes');
        });

        return response()->json(['comments' => $comments], 200);
    }

    public function store(Request $request, $postId): JsonResponse
    {
        $user = Auth::user();
        $post = Post::findOrFail($postId);

        $validatedData = $request->validate([
            'content' => 'required|string|max:255',
        ]);

        $comment = $post->comments()->create([
            'content' => $validatedData['content'],
            'user_id' => $user->id,
        ]);

        return response()->json(['message' => 'Comment added successfully', 'comment' => $comment], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $comment = Comment::findOrFail($id);
        $user = Auth::user();

        if ($comment->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validatedData = $request->validate([
            'content' => 'required|string|max:255',
        ]);

        $comment->update($validatedData);

        return response()->json(['message' => 'Comment updated successfully', 'comment' => $comment], 200);
    }

    public function destroy($id): JsonResponse
    {
        $comment = Comment::findOrFail($id);
        $user = Auth::user();

        if ($comment->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully'], 200);
    }
}
