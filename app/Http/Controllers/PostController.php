<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use App\Services\PostFormatterService;
use Illuminate\Http\Request;

class PostController extends Controller
{
    protected PostFormatterService $postFormatter;

    public function __construct(PostFormatterService $postFormatter)
    {
        $this->postFormatter = $postFormatter;
    }

    public function postsByUser($userId)
    {
        $user = User::findOrFail($userId);
        if(!$user){
            return response()->json(['message' => 'User not found'], 404);
        }

        $posts = $user->posts()->with('creator')->latest()->get();

        return response()->json(['posts' => PostResource::collection($posts)]);
    }

    public function postsByCategory($categoryId)
    {
        $category = Category::findOrFail($categoryId);

        if(!$category){
            return response()->json(['message' => 'Category not found'], 404);
        }

        $posts = $category->posts()->with('creator')->latest()->get();

        return response()->json(['posts' => PostResource::collection($posts)]);
    }
    
    public function index()
    {
        //
    }

    public function store(StorePostRequest $request)
    {

        $post = Post::create([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'category_id' => $request->input('category_id'),
            'created_by' => auth()->id()
        ]);
        $post->content = $this->postFormatter->format($post->content);
        return response()->json(['message' => 'Post created successfully', 'post' => PostResource::make($post)], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($postId)
    {
        $post = Post::whereId($postId)->first();

        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        $post = $post->load('creator');
        $post->content = $this->postFormatter->format($post->content);
        return response()->json(['post' => PostResource::make($post)]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $postId)
    {

        $post = Post::whereId($postId)->first();

        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        $post->update([
            'title' => $request->input('title') ?? $post->title,
            'content' => $request->input('content') ?? $post->content,
            'category_id' => $request->input('category_id') ?? $post->category_id
        ]);

        $post->content = $this->postFormatter->format($post->content);

        return response()->json(['message' => 'Post updated successfully', 'post' => PostResource::make($post)]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($postId)
    {
        $post = Post::whereId($postId)->first();
        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }
        $post->delete();

        return response()->json(['message' => 'Post deleted successfully']);
    }
}
