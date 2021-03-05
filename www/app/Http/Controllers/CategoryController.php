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

class CategoryController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    public function categoryCheck($title)
    {
        $category = \App\Models\Category::where('title', "$title")->first();

        $posts_of = \App\Models\Post::where("category_id", "=", $category->id)->paginate(5);

        $_SESSION = ['category'     => $category->title,
                    'pagination_of' => "category/$category->title"];

        return view('pages/post-display', ['posts' => $posts_of]);
    }
    
    public function categoryAll()
    {
        $categories = \App\Models\Category::paginate(5);

        $_SESSION['pagination_of'] = 'categories';

        return view('pages/categories-display', ['categories' => $categories]);
    }

    public function create()
    {;
        return view('category/form_category');
    }

    public function createSave(Request $request)
    {
        $request->validate([
            'title'    => ['required', 'unique:posts,title', 'min:5', 'max:255', 'string'],
            'slug'     => ['required', 'min:5', 'max:255', 'string'],
        ]);
        
        $new_cat = new \App\Models\Category();
        $new_cat ->title       = $request['title'];
        $new_cat ->slug        = $request['slug'];
        $new_cat ->save();
                
        return redirect()->route('list-all-categories');  
    }

    public function update(\App\Models\Category $category)
    {
        return view('category/form_category', ['post' => $category]);
    }

    public function updateSave(Request $request, \App\Models\Category $category)
    {
        $request->validate([
            'title'    => ['required', 'unique:posts,title', 'min:5', 'max:255', 'string'],
            'slug'     => ['required', 'min   :5', 'max:255', 'string'],
        ]);

        $up_cat = \App\Models\Category::find($category['id']);
        $up_cat ->title       = $request['title'];
        $up_cat ->slug        = $request['slug'];
        $up_cat ->save();

        return redirect()->route('list-all-categories');  
    }

    public function delete(\App\Models\Category $category)
    {
        $category->delete();
        
        return redirect()->route('list-all-categories');  
    }
}
