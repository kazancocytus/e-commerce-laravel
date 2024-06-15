<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PageController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->get('keyword');

        $query = Page::orderBy('created_at', 'DESC');


        if(!empty($keyword)){
            $query = $query->where('name', 'like', '%'. $keyword .'%');
        }

        $pages = $query->paginate(10);

        return view('admin.pages.list', ['pages' => $pages]);
    }

    public function create()
    {
        return view('admin.pages.create');
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:pages'
        ]);

        if ($validator->passes()) {

            $pages = new Page();
            $pages->name = $request->name;
            $pages->slug = $request->slug;
            $pages->content = $request->content;
            $pages->showHome = $request->showHome;
            $pages->save();

            $message = 'Pages created successfully';

            session()->flash('success', $message);

            return response()->json([
                'status' => true,
                'message' => $message
            ]);

        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

    } 
    
    public function edit($id)
    {
        $pages = Page::find($id);

        return view('admin.pages.edit', ['pages' => $pages]);
    }

    public function update(Request $request)
    {

        $pages = Page::find($request->id);

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:pages,slug,'.$pages->id
        ]);

        if ($validator->passes()) {

            $pages->name = $request->name;
            $pages->slug = $request->slug;
            $pages->content = $request->content;
            $pages->showHome = $request->showHome;
            $pages->save();

            $message = 'Updated pages successfully';

            session()->flash('success', $message);

            return response()->json([
                'status' => true,
                'message' => $message
            ]);

        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

    }

    public function destroy($id)
    {
        $pages = Page::find($id);

        if ($pages == null) {
            session()->flash('error', 'Page not found');
            return redirect()->route('page');
        }

        $pages->delete();

        session()->flash('success', 'Page deleted successfully');

        return response()->json([
            'status' => true
        ]);
    }
}
