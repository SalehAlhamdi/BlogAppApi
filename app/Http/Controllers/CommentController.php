<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    //get all comments of post
    public function index($id){
        $post=Post::find($id);

        //check if post exists
        if (!$post){
            return response([
                'message'=>'Post not found'
            ],403);
        }

        return response([
            'comments'=>$post->comments()->with('user:id,name')->get()
        ],200);
    }

    //create comment
    public function store(Request $request,$id){
        $post=Post::find($id);

        //check if post exists
        if (!$post){
            return response([
                'message'=>'Post not found'
            ],403);
        }

        //validate fields
        $attrs=$request->validate([
            'comment'=>'required|string'
        ]);

        //create Comment
        Comment::create([
           'comment'=>$attrs['comment'],
           'post_id'=>$id,
           'user_id'=>auth()->user()->id
        ]);

        return response([
            'message'=>'Comment Created'
        ],200);
    }

    //update Comment
    public function update(Request $request,$id){
        $comment=Comment::find($id);


        //check if Comment exists
        if (!$comment){
            return response([
                'message'=>'Comment not found'
            ],403);
        }

        //check user if it has permission
        if ($comment->user_id != auth()->user()->id){
            return response([
                'message'=>'permission denied.'
            ],403);
        }

        //validate fields
        $attrs=$request->validate([
           'comment'=>'required|string'
        ]);

        //update comment
        $comment->update([
           'comment'=>$attrs['comment']
        ]);


        return response([
            'message'=>'Comment updated'
        ],200);
    }

    public function destroy($id){
        $comment=Comment::find($id);

        //check if Comment exists
        if (!$comment){
            return response([
                'message'=>'Comment not found'
            ],403);
        }

        //check user if it has permission
        if ($comment->user_id != auth()->user()->id){
            return response([
                'message'=>'permission denied'
            ]);
        }

        $comment->delete();

        return response([
            'message'=>'Comment deleted'
        ],200);
    }
}
