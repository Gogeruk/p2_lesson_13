<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class PostController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function create()
    {
        $users      = \App\Models\User::all();
        $categories = \App\Models\Category::all();
        $tags       = \App\Models\Tag::all();

        return view('post/form_post',  ['users'      => $users,
                                        'categories' => $categories,
                                        'tags'       => $tags ]);
    }

    public function createSave(Request $request)
    {
        $request->validate([
            'user'     => ['required', 'exists:users,id'],
            'category' => ['exists:categories,id', 'required'],
            'title'    => ['required', 'unique:posts,title', 'min:5', 'max:255', 'string'],
            'slug'     => ['required', 'min:5', 'max:255', 'string'],
            'body'     => ['required', 'min:5', 'max:3000', 'string'],
            'tags'     => ['exists:tags,id', 'required',],
        ]);
        
        $cat = \App\Models\Category::find($request['category']);
        $new_post = new \App\Models\Post();
        $new_post ->title       = $request['title'];
        $new_post ->slug        = $request['slug'];
        $new_post ->body        = $request['body'];
        $new_post ->category_id = $request['category'];
        $new_post ->user_id     = $request['user'];
        $new_post ->save();
        $new_post->tags()->attach($request['tags']);
                
        return redirect()->route('home');  
    }
    
    public function update(\App\Models\Post $post)
    {
        $users        = \App\Models\User::all();
        $categories   = \App\Models\Category::all();
        $tags         = \App\Models\Tag::all();
        $fuck_you_php = $post->tags->pluck('id')->toArray();

        return view('post/form_post', ['users'         => $users,
                                        'post'         => $post,
                                        'categories'   => $categories,
                                        'fuck_you_php' => $fuck_you_php,
                                        'tags'         => $tags ]);
    }

    public function updateSave(Request $request, \App\Models\Post $post)
    {
        $request->validate([
            'user'     => ['required', 'exists:users,id'],
            'category' => ['exists            :categories,id', 'required'],
            'title'    => ['required', 'unique:posts,title', 'min:5', 'max:255', 'string'],
            'slug'     => ['required', 'min   :5', 'max:255', 'string'],
            'body'     => ['required', 'min   :5', 'max:3000', 'string'],
            'tags'     => ['exists            :tags,id', 'required',],
        ]);
    
        //dd($post);
        $up_post = \App\Models\Post::find($post['id']);
        $up_post ->title       = $request['title'];
        $up_post ->slug        = $request['slug'];
        $up_post ->body        = $request['body'];
        $up_post ->category_id = $request['category'];
        $up_post ->user_id     = $request['user'];
        $up_post ->save();
        $up_post->tags()->detach();
        $up_post->tags()->attach($request['tags']);

        return redirect()->route('home');  
    }
    
    public function delete(\App\Models\Post $post)
    {
        $post->tags()->detach();
        $post->delete();
        
        return redirect()->route('home');  
    }
}
