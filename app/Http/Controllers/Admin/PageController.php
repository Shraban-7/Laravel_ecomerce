<?php

namespace App\Http\Controllers\admin;

use App\Models\Page;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PageController extends Controller
{
    public function index(Request $request)
    {
        $query = Page::latest();

        // Apply the search filter if a keyword is provided
        if (!empty($request->get('keyword'))) {
            $query->where('name', 'like', '%' . $request->get('keyword') . '%');
        }

        $pages = $query->paginate(10);

        return view('admin.static_pages.list',[
            "pages"=>$pages
        ]);
    }

    public function create()
    {
        return view('admin.static_pages.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:pages',
            'status' => 'required|boolean',
        ]);

        $slug = Str::slug($request->name); // Create a slug from the name


        $page = new Page();

        $page->name=$request->name;
        $page->slug=$slug;
        $page->content=$request->content;
        $page->status = $request->status;

        $page->save();

        return response()->json([
            'status' => true,
            'message' => 'Page created successfully!'
        ]);

    }

    public function edit($id)
    {
        $page=Page::find($id);
        return view('admin.static_pages.edit',[
            "page"=>$page
        ]);
    }

    public function update(Request $request, $id)
    {
        $page=Page::find($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:pages,slug,' . $id, // Exclude the current page's slug
            'status' => 'required|boolean',
        ]);


        $slug = Str::slug($request->name); // Create a slug from the name
       

        $page->name=$request->name;
        $page->slug=$slug;
        $page->content=$request->content;
        $page->status = $request->status;

        $page->save();

        return response()->json([
            'status' => true,
            'message' => 'Page updated successfully!'
        ]);
    }

    public function destroy($id)
    {
        $page = Page::find($id);
        if (!$page) {
            return response()->json(['status' => false, 'message' => 'Page not found!'], 404);
        }

        $page->delete();
        return response()->json(['status' => true, 'message' => 'Page deleted successfully!']);
    }
}
