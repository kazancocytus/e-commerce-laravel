<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class ProductImageController extends Controller
{
    
    public function update(Request $request)
    {

        $image = $request->image;
        $ext = $image->getClientOriginalExtension();
        $sourcePath = $image->getPathName();
        

        $productImage = new ProductImage();
        $productImage->product_id = $request->product_id;
        $productImage->image = 'NULL';
        $productImage->save();
        
        $imageName =  $request->product_id . '-' . $productImage->id . '-' . time() . '.' . $ext;
        $productImage->image = $imageName;
        $productImage->save();

        $productImagePath = public_path().'/uploads/products/'.$imageName;
        if (file_exists($sourcePath)) {
            copy($sourcePath, $productImagePath);
        } else {
            Log::error("file not found" . $sourcePath);
        }

        return response()->json([
            'status' => true,
            'image_id' => $productImage->id,
            'imagePath' => asset('uploads/products/'.$productImage->image),
            'message' => 'success', 'Image saved successfully'
        ]);

        
    }
    
    public function destroy(Request $request)
    {

        $productImage = ProductImage::find($request->id);

        if (empty($productImage)) {
            $request->session()->flash('error', 'Image not found');
            return response()->json([
                'status' => false,
                'message' => 'error', 'Image not found'
            ]);
        }

        File::delete(public_path('uploads/products/'.$productImage->image));

        $productImage->delete();

        return response()->json([
            'status' => true,
            'message' => 'success', 'Image deleted successfully'
        ]);

    }
}
