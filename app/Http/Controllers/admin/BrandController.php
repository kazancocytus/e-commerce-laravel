<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BrandController extends Controller
{
    public function index(Request $request)
    {

        $keyword = $request->get('keyword');

        $query = Brand::orderBy('created_at', 'DESC');


        if(!empty($keyword)){
            $query = $query->where('name', 'like', '%'. $keyword .'%');
        }

        $brands = $query->paginate(15);
        $data['brands'] = $brands;

        return view('admin.brands.list', compact('brands'));

    }

    public function create()
    {
        return view('admin.brands.create');
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'nullable|unique:brands',
            'status' => 'required'
        ]);

        if ($validator->passes()) {

            $brands = new Brand();
            $brands->name = $request->name;
            $brands->slug = $request->slug;
            $brands->status = $request->status;
            $brands->save();

            session()->flash('success', 'Brands created successfully');

            return response()->json([
                'status' => true,
                'message' => 'success', 'Brands created successfully'
            ]);
 
        } else {

            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ]);

        }

    }

    public function edit($brandId, Request $request)
    {

        $brands = Brand::find($brandId);
        if (empty($brands)) {
            session()->flash('error', 'Brands not found');
            return redirect()->route('brands.index');
        }

        return view('admin.brands.edit', compact('brands'));

    }

    public function update($brandId, Request $request)
    {
        
        $brands = Brand::find($brandId);

        if (empty($brands)) {
            return response()->json([
                'status' => false,
                'notFound' => true,
                'message' => 'error', 'Brands not found'
            ]);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'nullable|unique:brands,slug,'.$brands->id.',id',
            'status' => 'required'
        ]);

        if ($validator->passes()) {

            $brands->name = $request->name;
            $brands->slug = $request->slug;
            $brands->status = $request->status;
            $brands->save();

            session()->flash('success', 'Brands updated successfully');

            return response()->json([
                'status' => true,
                'message' => 'success', 'Brands updated successfully'
            ]);
            

        } else {

            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ]);

        }
 
    }

    public function destroy($brandId, Request $request)
    {

        $brands = Brand::find($brandId);
        if (empty($brands)) {
            session()->flash('error', 'Brands not found');
            return response()->json([
                'status' => false,
                'message' => 'error', 'Brands not found'
            ]);
        }

        $brands->delete();

        session()->flash('success', 'Brands delete successfuly');

        return response()->json([
            'status' => true,
            'message' => 'success', 'Brands delete successfully'
        ]);

    }
}
