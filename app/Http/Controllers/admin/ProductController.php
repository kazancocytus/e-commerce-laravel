<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductRating;
use App\Models\SubCategory;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    
    public function index(Request $request)
    {

        $keyword = $request->get('keyword');

        $query = Product::latest('id')
                        ->with('product_image');


        if(!empty($keyword)){
            $query = $query->where('title', 'like', '%'. $keyword .'%');
        }

        $product = $query->paginate(15);
        $data['product']= $product;

        return view('admin.products.list', $data);
    }

    public function create()
    {

        $data = [];
        $categories = Category::orderBy('name', 'ASC')->get();
        $brands = Brand::orderBy('name', 'ASC')->get();
        $data['categories'] = $categories;
        $data['brands'] = $brands;

        return view('admin.products.create',$data);

    }

    public function store(Request $request) 
    {

        dd($request->all());

        $rules = [
            'title' => 'required',
            'slug' => 'required|unique:products',
            'price' => 'required|numeric',
            'sku' => 'required',
            'track_qty' => 'required|in:Yes,No',
            'category_id' => 'required|numeric',
            'is_featured' => 'required|in:Yes,No'
        ];

        if (!empty($request->track_qty) && $request->track_qty == 'Yes') {
            $rules['qty'] = 'required|numeric';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes()) {

            $product = new Product();
            $product->title = $request->title;
            $product->slug = $request->slug;
            $product->description = $request->description;
            $product->short_description = $request->short_description;
            $product->shipping_returns = $request->shipping_returns;
            $product->price = $request->price;
            $product->compare_price = $request->compare_price;
            $product->sku = $request->sku;
            $product->barcode = $request->barcode;
            $product->track_qty = $request->track_qty;
            $product->qty = $request->qty;
            $product->category_id = $request->category_id;
            $product->sub_category_id = $request->sub_category_id;
            $product->brands_id = $request->brands_id;
            $product->is_featured = $request->is_featured;
            $product->related_products = (!empty($request->related_products)) ? implode(',',$request->related_products) : '';
            $product->save();

            // Save image pict
            if ($request->has('image_array') && is_array($request->image_array)) {
                foreach ($request->image_array as $temp_image_id) {
                    $tempImageInfo = TempImage::find($temp_image_id);
                    
                    if ($tempImageInfo) {
                        $extArray = explode('.', $tempImageInfo->name);
                        $ext = last($extArray);
        
                        $productImage = new ProductImage();
                        $productImage->product_id = $product->id;
                        $productImage->image = 'NULL';
                        $productImage->save();
        
                        $imageName = $product->id . '-' . $productImage->id . '-' . time() . '.' . $ext;
                        $productImage->image = $imageName;
                        $productImage->save();
        
                        $sourcePath = public_path('/temp/'.$tempImageInfo->name);
                        $productImagePath = public_path('/uploads/products/'.$imageName);
        
                        if (file_exists($sourcePath)) {
                            copy($sourcePath, $productImagePath);
                        } else {
                            Log::error("File not found: " . $sourcePath);
                        }
                    } else {
                        Log::error("Temp image not found for ID: " . $temp_image_id);
                    }
                }
            } else {
                Log::error("No files found in the request or the files array is not valid");
            }
        

            session()->flash('success', 'Product added successfully');

            return response()->json([
                'status' => true,
                'message' => 'success', 'Product created successfully'
            ]);
 
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

    }

    public function edit($productId, Request $request) 
    {
        

        $product = Product::find($productId);

        if (empty($product)) {
            session()->flash('error', 'Product not found');
            return redirect()->route('product.index')->with('error', 'Product not found');
        }

        $productImage = ProductImage::where('product_id',$product->id)->get();

        $subCategory = SubCategory::where('categories_id',$product->category_id)->get();

        $relatedProduct = [];

        if (!empty($product->related_products)) {
            $productArray = explode(',', $product->related_products);
            $relatedProduct = Product::whereIn('id', $productArray)->get();
        }

        // dd($relatedProduct);

        $data = [];
        $categories = Category::orderBy('name', 'ASC')->get();
        $brands = Brand::orderBy('name', 'ASC')->get();
        $data['categories'] = $categories;
        $data['brands'] = $brands;
        $data['product'] = $product;
        $data['subCategory'] = $subCategory;
        $data['productImage'] = $productImage;
        $data['relatedProduct'] = $relatedProduct;

        return view('admin.products.edit',$data);

    }

    public function update($productId, Request $request)
    {

        $productIdFromRequest = $request->input('id');
        $product = Product::find($productIdFromRequest);
        
        // dd('Product ID:', $productIdFromRequest, 'Request Data:', $request->all());


        $rules = [
            'title' => 'required',
            'slug' => 'required',
            'price' => 'required|numeric',
            'sku' => 'required',
            'track_qty' => 'required|in:Yes,No',
            'category_id' => 'required|numeric',
            'is_featured' => 'required|in:Yes,No'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes()) {

            // $product->id = $request->id;
            $product->title = $request->title;
            $product->slug = $request->slug;
            $product->description = $request->description;
            $product->short_description = $request->short_description;
            $product->shipping_returns = $request->shipping_returns;
            $product->price = $request->price;
            $product->compare_price = $request->compare_price;
            $product->sku = $request->sku;
            $product->barcode = $request->barcode;
            $product->track_qty = $request->track_qty;
            $product->qty = $request->qty;
            $product->category_id = $request->category_id;
            $product->sub_category_id = $request->sub_category_id;
            $product->brands_id = $request->brands_id;
            $product->is_featured = $request->is_featured;
            $product->related_products = (!empty($request->related_products)) ? implode(',',$request->related_products) : '';


            $product->save();



            if ($request->hasFile('image_array') && is_array($request->file('image_array'))) {
                foreach ($request->file('image_array') as $uploadedImage) {
                    if ($uploadedImage && $uploadedImage->isValid()) {
                        $ext = $uploadedImage->getClientOriginalExtension();
            
                        $productImage = new ProductImage();
                        $productImage->product_id = $product->id;
                        $productImage->image = 'NULL';
                        $productImage->save();
            
                        $imageName = $product->id . '-' . $productImage->id . '-' . time() . '.' . $ext;
                        $productImage->image = $imageName;
                        $productImage->save();
            
                        $productImagePath = public_path().'/uploads/products/'.$imageName;
            
                        $uploadedImage->move(public_path('/uploads/products'), $imageName);
                    } else {
                        Log::error("Invalid file upload");
                    }
                }
            } else {
                Log::error("No files found in image_array or image_array is not an array");
            }
            
            

            session()->flash('success', 'Product updated successfully');

            return response()->json([
                'status' => true,
                'message' => 'success', 'Product updated successfully'
            ]);
 
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

    }

    public function destroy($productId, Request $request)
    {
        $product = Product::find($productId);

        if(empty($product)) {
            session()->flash('error', 'Product not found');
            return redirect()->route('product.index');
        }

        $productImage = ProductImage::where('product_id',$productId)->get();

        if(!empty($productImage)) {
            foreach ($productImage as $productImages) {
                File::delete(public_path('uploads/products/'.$productImages->image));
            }

            ProductImage::where('product_id',$productId)->delete();
        }

        $product->delete();

        session()->flash('success', 'Product deleted successfully');

        return response()->json([
            'status' => true,
            'message' => 'success', 'Product deleted successfully'
        ]);


    }

    public function getProducts(Request $request) 
    {

        $termProduct = [];
        if ($request->term != "") {
            $product = Product::where('title','like','%'. $request->term .'%')->get();
            
            if ($product != null) {
                foreach ($product as $products) {
                    $termProduct[] = array('id' => $products->id, 'text' => $products->title);
                }
            }
        }

        return response()->json([
            'tags' => $termProduct,
            'status' => true
        ]);

    }

    public function productRatings(Request $request)
    {
        $ratings = ProductRating::leftJoin('products','products.id','=','product_ratings.product_id');
        $ratings = $ratings->select('product_ratings.*','products.title as productTitle');
        $ratings = $ratings->orderBy('created_at','DESC');

        $keyword = $request->get('keyword');

        if (!empty($keyword)) {
            $ratings = $ratings->where('products.title','like','%' . $keyword . '%');
            $ratings = $ratings->where('product_ratings.username','like','%' . $keyword . '%');
        }

        $ratings = $ratings->paginate(10);

        return view('admin.products.ratings', ['ratings' => $ratings]);
    }

    public function changeStatus(Request $request)
    {

        $productRating = ProductRating::find($request->id);
        if ($productRating == null) {
            session()->flash('error', 'Rating not found');
            return response()->json([
                'status' => false
            ]);
        }
        $productRating->status = $request->status;
        $productRating->save();

        session()->flash('success', 'Status changed successfully');

        return response()->json([
            'status' => true
        ]);

    }

}
