<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Requests\Post\StoreRequest;
use App\Http\Requests\Post\PutRequest;

class PostController extends Controller
{

    public function index()
    {
        return response()->json(Post::with('category')->paginate(2));
    }

   
    public function store(StoreRequest $request)
    {
        return response()->json(Post::create(request->validated()));
    }


    public function show(Post $post)
    {
        return response()->json($post);
    }

  
    
    public function update(PutRequest $request, Post $post)
    {
        $post->update($request->validate);
        return response()->json($post);
    }


    public function destroy(Post $post)
    {
        $post->delete();
        return response()->json('ok');
    }
}
