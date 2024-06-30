<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;
class FavoritesController extends Controller
{
    /**
     * Toggle a post as favorite for the authenticated user.
     *
     * @param int $postId
     * @return JsonResponse
     */
    public function update($postId): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        $post = Post::findOrFail($postId);

        // Check if the post is already favorited by the user
        if ($user->favorites()->where('post_id', $post->id)->exists()) {
            // Post is already favorited, so remove it
            $user->favorites()->detach($post->id);
            $message = 'Post removed from favorites';
        } else {
            // Post is not favorited, so add it
            $user->favorites()->attach($post->id);
            $message = 'Post added to favorites';
        }


        return response()->json(['message' => $message], 200);
    }

    /**
     * Remove a post from favorites for the authenticated user.
     *
     * @param int $postId
     * @return JsonResponse
     */


    /**
     * Get all favorite posts of the authenticated user.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();

        // Load favorite posts with their details
        $favorites = $user->favorites()->with('user')->get();

        // Transform each favorite post
        $favorites->transform(function ($post) use ($user) {
            // Check if the user has liked the post
            $post->liked = $user ? $post->likes->contains('user_id', $user->id) : false;

            // Get the count of likes instead of detailed like objects
            $post->likes_count = $post->likes->count();

            // Remove the 'likes' relationship data completely
            unset($post->likes);

            // Remove the 'pivot' information
            $post->setHidden(['pivot']);

            return $post;
        });

        return response()->json(['favorites' => $favorites], 200);
    }
}

