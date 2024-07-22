<?php

use App\Http\Controllers\admin\AdminLoginController;
use App\Http\Controllers\admin\BrandController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\DashboardController;
use App\Http\Controllers\admin\DiscountController;
use App\Http\Controllers\admin\OrderController;
use App\Http\Controllers\admin\PageController;
use App\Http\Controllers\admin\ProductController;
use App\Http\Controllers\admin\ProductImageController;
use App\Http\Controllers\admin\ProductSubCategoryController;
use App\Http\Controllers\admin\SettingController;
use App\Http\Controllers\admin\ShippingController;
use App\Http\Controllers\admin\SubCategoryController;
use App\Http\Controllers\admin\TempImagesController;
use App\Http\Controllers\admin\UsersController;
use App\Http\Controllers\CartController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\AuthController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/mail',function(){
//     orderEmail(19);
// });

Route::get('/', [FrontController::class, 'index'])->name('front.home');
Route::post('add-wishlist', [FrontController::class, 'addWishlist'])->name('add-wishlist');
Route::get('/page/{slug}', [FrontController::class, 'showPage'])->name('show-pages');
Route::post('/send-contact', [FrontController::class, 'sendContact'])->name('send-contact');

Route::get('/shop/{categorySlug?}/{subCategorySlug?}', [ShopController::class, 'index'])->name('front.shop');
Route::get('/product/{slug}', [ShopController::class, 'product'])->name('front.product');
Route::post('/save-ratings', [ShopController::class, 'saveRating'])->name('save-rating');

Route::get('/cart', [CartController::class, 'cart'])->name('front.cart');
Route::post('/add-to-cart', [CartController::class, 'addToCart'])->name('front.addToCart');
Route::post('/update-cart', [CartController::class, 'updateCart'])->name('front.updateCart');
Route::delete('/delete-cart', [CartController::class, 'deleteCart'])->name('front.deleteCart');

Route::get('/checkout', [CartController::class, 'checkout'])->name('front.checkout');
Route::post('/process-checkout', [CartController::class , 'processCheckout'])->name('front.process-checkout');
Route::get('/thanks/{id}', [CartController::class, 'thankyou'])->name('front.thanks');
Route::post('/order-summery', [CartController::class, 'getOrderSummery'])->name('front.get-order-summery');
Route::post('/apply-discount', [CartController::class, 'applyDiscount'])->name('front.apply-discount');
Route::post('/remove-discount', [CartController::class, 'removeDiscount'])->name('front.remove-discount');

// Route account user
Route::group(['prefix' => 'account'],function() {
    Route::group(['middleware' => 'guest'],function() {
        Route::get('/register', [AuthController::class, 'register'])->name('register');
        Route::get('/otp-page', [AuthController::class, 'otpPage'])->name('otp-page');
        Route::post('/send-otp', [AuthController::class, 'validateOtp'])->name('send-otp');
        Route::post('/resend-otp', [AuthController::class, 'resendOtp'])->name('resend-otp');
        Route::post('/process-register', [AuthController::class, 'processRegister'])->name('processRegister');

        Route::get('/login', [AuthController::class, 'login'])->name('login');
        Route::post('/authenticate', [AuthController::class, 'authenticate'])->name('authenticate');

        Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('forgot-password');
        Route::post('/forgot-password', [AuthController::class, 'proccessForgotPassword'])->name('proccess-forgot-password');
        Route::get('/reset-password/{token}', [AuthController::class, 'resetPassword'])->name('reset-password');
        Route::post('/proccess-reset-password', [AuthController::class, 'proccessResetPassword'])->name('proccess-reset-password');

    });

    Route::group(['middleware' => 'auth'], function() {
        Route::get('/profile-account', [AuthController::class, 'profilAccount'])->name('profil.account');
        Route::post('/update-profile', [AuthController::class, 'updateProfileAccount'])->name('update-profile');
        Route::post('/update-address', [AuthController::class, 'updateAddress'])->name('update-address');
        Route::get('/my-orders', [AuthController::class, 'orders'])->name('orders.account');
        Route::get('/my-orders/detail/{id}', [AuthController::class, 'ordersDetail'])->name('orders-detail.account');
        Route::get('/wishlist', [AuthController::class, 'wishlist'])->name('wishlist');
        Route::post('/remove-wishlist', [AuthController::class, 'removeWishlist'])->name('remove-wishlist');
        Route::get('/change-password', [AuthController::class, 'userChangePassword'])->name('user-change-password');
        Route::post('/proccess-change', [AuthController::class, 'proccessChangePassword'])->name('proccess-change-password');
        Route::get('/logout', [AuthController::class, 'logout'])->name('logout');


    });
});

Route::group(['prefix' => 'admin'], function(){

    Route::group(['middleware' => 'admin.guest'], function(){
        
        Route::get('/login', [AdminLoginController::class, 'index'])->name('admin.login');
        Route::get('/forgot-password-page', [AdminLoginController::class, 'showForgotPasswordPage'])->name('admin.forgot-password');
        Route::post('/proccess-forgot-password', [AdminLoginController::class, 'proccessForgotPassword'])->name('admin.proccess-forgot-password');
        Route::post('/authenticate', [AdminLoginController::class, 'authenticate'])->name('admin.authenticate');

    });

    Route::group(['middleware' => 'admin.auth'], function(){
        
        Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/change-password', [SettingController::class, 'showChangePassword'])->name('show-page-change-password');
        Route::post('/change-password', [SettingController::class, 'proccessChangePassword'])->name('proccess-change-password-admin');
        Route::get('/logout', [DashboardController::class, 'logout'])->name('admin.logout');

        // Category
        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('/categories/store', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{category}/update', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}/delete', [CategoryController::class, 'destroy'])->name('categories.delete');

        // Sub Category
        Route::get('/sub-categories', [SubCategoryController::class, 'index'])->name('sub-categories.index');
        Route::get('/sub-categories/create', [SubCategoryController::class, 'create'])->name('sub-categories.create');
        Route::post('/sub-categories/store', [SubCategoryController::class, 'store'])->name('sub-categories.store');
        Route::get('/sub-categories/{subCategories}/edit', [SubCategoryController::class, 'edit'])->name('sub-categories.edit');
        Route::put('/sub-categories/{subCategories}/update', [SubCategoryController::class, 'update'])->name('sub-categories.update');
        Route::delete('/sub-categories/{subCategories}/delete', [SubCategoryController::class, 'destroy'])->name('sub-categories.delete');

        // Brands
        Route::get('/brands', [BrandController::class, 'index'])->name('brands.index');
        Route::get('/brands/create', [BrandController::class, 'create'])->name('brands.create');
        Route::post('/brands/store', [BrandController::class, 'store'])->name('brands.store');
        Route::get('/brands/{brands}/edit', [BrandController::class, 'edit'])->name('brands.edit');
        Route::put('/brands/{brands}/update', [BrandController::class, 'update'])->name('brands.update');
        Route::delete('/brands/{brands}/delete', [BrandController::class, 'destroy'])->name('brands.delete');

        // Product
        Route::get('/product', [ProductController::class, 'index'])->name('product.index');
        Route::get('/product/create', [ProductController::class, 'create'])->name('product.create');
        Route::post('/product/store', [ProductController::class, 'store'])->name('product.store');
        Route::get('/product/{products}/edit', [ProductController::class, 'edit'])->name('product.edit');
        Route::put('/product/{products}/update', [ProductController::class, 'update'])->name('product.update');
        Route::delete('/product/{product}/delete', [ProductController::class, 'destroy'])->name('product.delete');
        Route::get('/get-product', [ProductController::class, 'getProducts'])->name('product.get-product');
        Route::get('/ratings-product', [ProductController::class, 'productRatings'])->name('admin.product-ratings');
        Route::get('/change-status', [ProductController::class, 'changeStatus'])->name('admin.change-status');

        Route::get('/product-subcategories', [ProductSubCategoryController::class, 'index'])->name('product-subcategories.index');

        Route::post('/products-image/update', [ProductImageController::class, 'update'])->name('product-images.update');
        Route::delete('/products-image/delete', [ProductImageController::class, 'destroy'])->name('product-images.delete');

        // Route shipping
        Route::get('/shipping/create', [ShippingController::class, 'create'])->name('shipping.create');
        Route::post('/shipping/store', [ShippingController::class, 'store'])->name('shipping.store');
        Route::get('/shipping/edit/{id}', [ShippingController::class, 'edit'])->name('shipping.edit');
        Route::post('/shipping/update/{id}', [ShippingController::class, 'update'])->name('shipping.update');
        Route::delete('/shipping/delete/{id}', [ShippingController::class, 'destroy'])->name('shipping.delete');

        // Route CRUD discount coupon
        Route::get('/discount', [DiscountController::class, 'index'])->name('discount');
        Route::get('/discount/create', [DiscountController::class, 'create'])->name('discount.create');
        Route::post('/dicsount/store', [DiscountController::class, 'store'])->name('discount.store');
        Route::get('/dicsount/edit/{id}', [DiscountController::class, 'edit'])->name('discount.edit');
        Route::post('/dicsount/update', [DiscountController::class, 'update'])->name('discount.update');
        Route::delete('/dicsount/delete/{id}', [DiscountController::class, 'delete'])->name('discount.delete');

        // Route Order admin
        Route::get('/orders', [OrderController::class, 'index'])->name('orders');
        Route::get('/detail/{id}', [OrderController::class, 'detail'])->name('detail-orders');
        Route::post('/update-orders/{id}', [OrderController::class, 'updateStatusOrder'])->name('update-orders');
        Route::post('/send-invoice/{id}', [OrderController::class, 'sendInvoice'])->name('send-invoice');

        // Route CRUD Users
        Route::get('/users', [UsersController::class, 'index'])->name('admin.users');
        Route::get('/create-users', [UsersController::class, 'create'])->name('users-create');
        Route::post('/create-users', [UsersController::class, 'store'])->name('users-store');
        Route::get('/edit-users/{id}', [UsersController::class, 'edit'])->name('users-edit');
        Route::post('/update-users', [UsersController::class, 'update'])->name('users-update');
        Route::delete('/delete-users/{id}', [UsersController::class, 'destroy'])->name('delete-users');

        // Route CRUD Page Static
        Route::get('/pages', [PageController::class, 'index'])->name('page');
        Route::get('/pages-create', [PageController::class, 'create'])->name('create-page');
        Route::post('/pages-store', [PageController::class, 'store'])->name('store.page');
        Route::get('/pages-edit/{id}', [PageController::class, 'edit'])->name('edit-page');
        Route::post('/pages-update', [PageController::class, 'update'])->name('update-page');
        Route::delete('/pages-delete/{id}', [PageController::class, 'destroy'])->name('delete-page');

        Route::post('/upload-temp-image', [TempImagesController::class, 'create'])->name('temp-images.create');

        Route::get('/getSlug', function(Request $request){

            $slug = '';
            if (!empty($request->title)) {
                $slug = Str::slug($request->title);
            }

            return response()->json([
                'status' => true,
                'slug' => $slug
            ]);

        })->name('getSlug');

    });

});