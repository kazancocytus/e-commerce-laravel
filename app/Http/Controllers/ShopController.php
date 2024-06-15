<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductRating;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShopController extends Controller
{
    
    public function index(Request $request, $categorySlug = null, $subCategorySlug = null) 
    {

        $categoriesSelected = '';
        $subCategoriesSelected = '';
        $brandsArray = [];
            

        $categories = Category::orderBy('name', 'ASC')
                                ->with('subCategories')
                                ->get();
        

        $brands = Brand::orderBy('name', 'ASC')
                                ->where('status', 1)
                                ->get();
        
        $products = Product::where('status', 1);

        // Filters
        if (!empty($categorySlug)) {
            $category = Category::where('slug', $categorySlug)
                                    ->with('subCategories')
                                    ->first();
            if ($category) {
                $products->where('category_id', $category->id);
                $categoriesSelected = $category->id;
            }
        }
        
        if (!empty($subCategorySlug)) {
            $subCategories = SubCategory::where('slug', $subCategorySlug)
                                    ->first();
            if ($subCategories) {
                $products->where('sub_category_id', $subCategories->id);
                $subCategoriesSelected = $subCategories->id;
            }
        }

        if(!empty($request->get('brand'))) {
            $brandsArray = explode(',',$request->get('brand'));
            $products->whereIn('brands_id',$brandsArray);
        } 

        $priceMin = $request->get('price_min', 0);
        $priceMax = $request->get('price_max', 50000); 

        if ($priceMin !== '' && $priceMax !== '') {
            $products->whereBetween('price', [
                intval($priceMin),
                intval($priceMax)
            ]);
        }

        if (!empty($request->get('search'))) {
            $products = $products->where('title','like','%' . $request->get('search') . '%');
        }

        if ($request->get('sort')) {
            if ($request->get('sort') == 'latest') {
                $products->orderBy('id', 'DESC');
            } else if($request->get('sort') == 'price_asc') {
                $products->orderBy('price', 'ASC');
            } else {
                $products->orderBy('price', 'DESC');
            }
        } else {
            $products->orderBy('price', 'DESC');
        }


        $product = $products->paginate(6);
        
        $data['categories'] = $categories;
        $data['brands'] = $brands;
        $data['product'] = $product;
        $data['categoriesSelected'] = $categoriesSelected;
        $data['subCategoriesSelected'] = $subCategoriesSelected;
        $data['brandsArray'] = $brandsArray;
        $data['priceMax'] = intval($priceMax);
        $data['priceMin'] = intval($priceMin);
        $data['sort'] = $request->get('sort');


        return view('front.shop',$data);

    }

    public function product($slug) 
    {

        $product = Product::where('slug',$slug)
                        ->withCount('product_ratings')
                        ->withSum('product_ratings','rating')
                        ->with(['product_image','product_ratings'])
                        ->first();


        if ($product == null) {
            abort(404);
        }

        $relatedProduct = [];
        if ($product->related_products != '') {
            $productArray = explode(',',$product->related_products);
            $relatedProduct = Product::whereIn('id',$productArray)
                                    ->with('product_image')
                                    ->get();
        }

        $avgRating = '0.00';
        $avgRatingPercen = 0;
        if ($product->product_ratings_count > 0) {
            $avgRating = number_format(($product->product_ratings_sum_rating/$product->product_ratings_count),2);
            $avgRatingPercen = ($avgRating*100)/5;
        }

        return view('front.product',[
            'product' => $product, 
            'relatedProduct' => $relatedProduct,
            'avgRating' => $avgRating,
            'avgRatingPercen' => $avgRatingPercen
        ]);
    
    }

    public function saveRating(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|min:4',
            'email' => 'required|email',
            'review' => 'required',
            'rating' => 'required'
        ]);

        if ($validator->passes()) {

            $count = ProductRating::where('email', $request->email)
                                ->where('product_id', $request->product_id)->count();
            
            if ($count > 0) {
                session()->flash('error', 'You already rated this product');
                return response()->json([
                    'status' => true
                ]);
            }

            $productRating = new ProductRating();
            $productRating->product_id = $request->product_id;
            $productRating->username = $request->name;
            $productRating->email = $request->email;
            $productRating->comment = $request->review;
            $productRating->rating = $request->rating;
            $productRating->status = 1;
            $productRating->save();

            $message = 'Send rating for product successfully';

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

}
