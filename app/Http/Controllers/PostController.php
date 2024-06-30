<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\PostResource;

class PostController extends Controller
{
    public function index(): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();

        // Query posts with 'likes' and 'user' relationships
        $posts = Post::with(['likes', 'user']);

        // Paginate the results
        $posts = $posts->paginate(10);

        // Transform each post using PostResource
        $posts->getCollection()->transform(function ($post) use ($user) {
            $post->liked = $user ? $post->likes->contains('user_id', $user->id) : false;
            $post->makeHidden('likes');
            $post->comments_count = $post->comments()->count();

            return new PostResource($post);
        });

        return response()->json($posts);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:255',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check if user is authenticated
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // Create a new Post instance
        $post = new Post();
        $post->title = $request->input('title');
        $post->content = $request->input('content');
        $post->user_id = $user->id;
        $post->save();

        // Load the 'user' relationship
        $post->load('user');

        // Return a JSON response with the created post and success message
        return response()->json(['post' => new PostResource($post), 'message' => 'Post created successfully'], 201);
    }

    public function show($id): JsonResponse
    {
        $post = Post::withCount(['likes', 'comments'])
            ->with('user:id,login,email,created_at,updated_at')
            ->findOrFail($id);

        $user = Auth::guard('sanctum')->user();
        $post->liked = $user ? $post->likes->contains('user_id', $user->id) : false;
        $post->makeHidden('likes');

        // Return a JSON response with the post using PostResource
        return response()->json(['post' => new PostResource($post)], 200);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $user = Auth::user();
        $post = Post::findOrFail($id);

        if ($user->id !== $post->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:255',
        ]);

        // Update the post
        $post->update($validatedData);

        // Return a JSON response with the updated post using PostResource
        return response()->json(['post' => new PostResource($post), 'message' => 'Post updated successfully'], 200);
    }

    public function destroy($id): JsonResponse
    {
        $user = Auth::user();
        $post = Post::findOrFail($id);

        if ($user->id !== $post->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Delete the post
        $post->delete();

        // Return a JSON response with a success message
        return response()->json(['message' => 'Post deleted successfully'], 200);
    }
}
