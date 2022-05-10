<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    //get all posts
    public function index()
    {
        return response([
            //get all posts and join user table using relations and show if current user liked post or not
            'posts'=>Post::orderBy('created_at','desc')->with('user:id,name')->withCount('comments','likes')
                ->with('likes',function ($like){
                return $like->where('user_id',auth()->user()->id)->select('id','user_id','post_id')->get();
            })->get()
        ],200);
    }

    //get single post
    public function show($id)
    {
        return response([
           'post'=>Post::where('id',$id)->withCount('comments','likes')->get()
        ],200);
    }

    //create a post
    public function store(Request $request)
    {
        //validate fields
        $attrs=$request->validate([
           'body'=>'required|string',
        ]);


        $post=Post::create([
           'body'=>$attrs['body'],
            'user_id'=>auth()->user()->id,
        ]);


        return response([
            'message'=>'Post Created',
            'post'=>$post
        ],200);
    }

    //update a post
    public function update(Request $request,$id)
    {
        $post=Post::find($id);

        //check if post exists
        if (!$post)
        {
            return response([
               'message'=>'Post not found'
            ],403);
        }

        //check user if it has permission
        if ($post->user_id != auth()->user()->id)
        {
            return response([
                'message'=>'permission denied.'
            ]);
        }

        //validate fields
        $attrs=$request->validate([
            'body'=>'required|string'
        ]);

        //update post
        $post->update([
            'body'=>$attrs['body']
        ]);

        return response([
            'message'=>'Post updated',
            'post'=>$post,
        ],200);
    }
    public function destroy($id){
        $post=Post::find($id);

        //check if post exists
        if (!$post){
            return response([
                'message'=>'Post not found'
            ],403);
        }

        //check user if it has permission
        if ($post->user_id != auth()->user()->id){
            return response([
                'message'=>'permission denied.'
            ],403);
        }

        //delete comments/likes and post
        $post->comments()->delete();
        $post->likes()->delete();
        $post->delete();

        return response([
            'message'=>'Post deleted'
        ],200);
    }
}
