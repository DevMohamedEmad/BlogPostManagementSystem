<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, int $postId)
    {
        $request->validate([
            'content' => 'required',
        ]);

        $post = Post::whereId($postId)->first();
        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }
        $comment = new Comment([
            'content' => $request->input('content'),
            'created_by' => Auth::id(),
            'post_id' => $post->id,
        ]);

        $post->comments()->save($comment);
        $comment->load('creator');
        return response()->json(['message' => 'Comment added successfully', 'comment' => CommentResource::make($comment)], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $commentId)
    {
        $comment = Comment::whereId($commentId)->first();

        if (!$comment) {
            return response()->json(['message' => 'Comment not found'], 404);
        }

        if ($comment->created_by !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        if ($request->has('content')) {
            $comment->content = $request->input('content');
            $comment->save();
        }
        $comment->load('creator');
        return response()->json(['message' => 'Comment updated successfully', 'comment' => CommentResource::make($comment)]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($commentId)
    {
        $comment = Comment::whereId($commentId)->first();
        if (!$comment) {
            return response()->json(['message' => 'Comment not found'], 404);
        }

        if ($comment->created_by !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully']);
    }

    public function commentsByUser($userId)
    {
        $comments = Comment::where('created_by', $userId)->with('creator')->latest()->get();

        return response()->json(['comments' => CommentResource::collection($comments)]);
    }

    public function commentsByPost($postId)
    {
        $comments = Comment::where('post_id', $postId)->with('creator')->latest()->get();

        return response()->json(['comments' => CommentResource::collection($comments)]);
    }
}
