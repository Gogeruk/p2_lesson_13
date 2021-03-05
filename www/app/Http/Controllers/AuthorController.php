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
use Illuminate\Http\RedirectResponse;

class AuthorController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function author($author)
    {
        $user = \App\Models\User::where('name', "$author")->first();

        $posts_of = \App\Models\Post::where("user_id", "=", $user->id)->paginate(5);

        $_SESSION = ['user'         => $user->name,
                    'pagination_of' => "author/$user->name"];

        return view('pages/post-display', ['posts' => $posts_of]);
    }

    public function author_category($user_name , $category_name)
    {
        $user = \App\Models\User::where('name', "$user_name")->first();
        $category = \App\Models\Category::where('title', "$category_name")->first();
        $_SESSION = ['user'         => "$user->name",
                    'category'      => "$category->title",
                    'pagination_of' => "author/$user->name/category/$category->title"];

        $posts_of = \App\Models\Post::whereHas('user', function (\Illuminate\Database\Eloquent\Builder $query) use ($user, $category) {
            $query->where('user_id',      '=', $user->id);
            $query->where('category_id',  '=', $category->id);
        })->paginate(5);

        return view('pages/post-display', ['posts' => $posts_of]);
    }

    public function author_category_tag($user_name , $category_name , $tag_name)
    {
        $user = \App\Models\User::where('name', "$user_name")->first();
        $category = \App\Models\Category::where('title', "$category_name")->first();
        $tag = \App\Models\Tag::where('title', "$tag_name")->first();
        $_SESSION =['user'          => "$user->name",
                    'tag'           => "$tag->title",
                    'category'      => "$category->title",
                    'pagination_of' => "author/$user->name/category/$category->title/tag/$tag->title"];

        $posts_of = \App\Models\Post::whereHas('tags', function (\Illuminate\Database\Eloquent\Builder $query) use ($user, $category, $tag) {
            $query->where('user_id',      '=', $user->id);
            $query->where('category_id',  '=', $category->id);
            $query->where('tag_id',       '=', $tag->id);
        })->paginate(5);

        return view('pages/post-display', ['posts' => $posts_of]);
    }
}
