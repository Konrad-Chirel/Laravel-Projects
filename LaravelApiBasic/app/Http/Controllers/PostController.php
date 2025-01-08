<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends ApiController
{
    public function index(Request $request)
    {
        $posts = Post::get();
        return $this->sucessResponse($posts);
    }

    public function store(StorePostRequest $request)
    {
        $post = Post::create($request->validated());
        
        return $this->sucessResponse($post, 'Post created successfully', 201);
    }

    public function show(Post $post)
    {
        if (!$post) {
            return $this->errorResponse('Post not found', 404);
        }       
        return $this->sucessResponse($post);
    }

    public function update(UpdatePostRequest $request, Post $post)
    {
        // Récupérer les données validées
        $validatedData = $request->validated();
        
        // Mettre à jour le post
        $post->update($validatedData);
        
        return $this->sucessResponse($post, 'Post updated successfully', 200);
    }

    public function destroy(Post $post)
    {
        $post->delete();
        
        return $this->sucessResponse($post, 'Post deleted successfully', 200);
    }
}
