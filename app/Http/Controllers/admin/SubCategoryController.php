<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use function PHPUnit\Framework\isEmpty;

class SubCategoryController extends Controller
{

    public function index(Request $request)
    {
        $keyword = $request->get('keyword');

        $query = SubCategory::select('sub_categories.*','categories.name as categoryName')
                            ->latest('id')
                            ->leftJoin('categories','categories.id','sub_categories.categories_id');


        if(!empty($keyword)){
            $query = $query->where('sub_categories.name', 'like', '%'. $keyword .'%');
        }

        $subCategories = $query->paginate(15);
        $data['subCategories'] = $subCategories;

        return view('admin.sub_category.list',compact('subCategories'));
    }

    public function create()
    {

        $categories = Category::orderBy('name', 'ASC')->get();
        $data['categories'] = $categories;

        return view('admin.sub_category.create',$data);

    }

    public function store(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'nullable|unique:sub_categories',
            'category' => 'required',
            'status' => 'required'
        ]);

        if ($validator->passes()){
            
            $subCategory = new SubCategory();
            $subCategory->name = $request->name;
            $subCategory->slug = $request->slug;
            $subCategory->status = $request->status;
            $subCategory->showHome = $request->showHome;
            $subCategory->categories_id = $request->category;
            $subCategory->save();

            $request->session()->flash('success', 'Sub categories created successfully');

            return response()->json([
                'status' => true,
                'message' => 'success', 'Sub categories created successfully'
            ]);

        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function edit($subCategoriesId, Request $request)
    {

        $subCategories = SubCategory::find($subCategoriesId);

        if (empty($subCategories)) {
            $request->session()->flash('error', 'Record not found');
            return redirect()->route('sub-categories.index');
        }

        $categories = Category::orderBy('name', 'ASC')->get();
        $data['categories'] = $categories;
        $data['subCategories'] = $subCategories;

        return view('admin.sub_category.edit',$data);
    }

    public function update($subCategoriesId ,Request $request)
    {

        $subCategories = SubCategory::find($subCategoriesId);

        if (empty($subCategories)) {
            return response()->json([
                'status' => false,
                'notFound' => true,
                'message' => 'error', 'Sub category not found'
            ]);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'nullable|unique:sub_categories,slug,'.$subCategories->id.',id',
            'category' => 'required',
            'status' => 'required'
        ]);

        if ($validator->passes()){
            
            $subCategories->name = $request->name;
            $subCategories->slug = $request->slug;
            $subCategories->status = $request->status;
            $subCategories->showHome = $request->showHome;
            $subCategories->categories_id = $request->category;
            $subCategories->save();

            $request->session()->flash('success', 'Sub categories updated successfully');

            return response()->json([
                'status' => true,
                'message' => 'success', 'Sub categories updated successfully'
            ]);

        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

    }

    public function destroy($subCategoriesId, Request $request)
    {  
        $subCategories = SubCategory::find($subCategoriesId);

        if (empty($subCategories)) {
            $request->session()->flash('error', 'Subcategory not found');
            return redirect()->route('sub-categories.index');
            return response()->json([
                'status' => false,
                'notFound' => true,
                'message' => 'error', 'SubCategory not found'
            ]);
        }

        $subCategories->delete();

        $request->session()->flash('success', 'SubCategory deleted successfully');

        return response()->json([
            'status' => true,
            'message' => 'success', 'SubCategory deleted successfully'
        ]);
    }
}
