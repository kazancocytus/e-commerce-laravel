@extends('front.layout.app')

@section('content')

<main>
    <section class="section-5 pt-3 pb-3 mb-3 bg-white">
        <div class="container">
            <div class="light-font">
                <ol class="breadcrumb primary-color mb-0">
                    <li class="breadcrumb-item"><a class="white-text" href="{{ route('front.home') }}">Home</a></li>
                    <li class="breadcrumb-item active">Shop</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="section-6 pt-5">
        <div class="container">
            <div class="row">            
                <div class="col-md-3 sidebar">
                    <div class="sub-title">
                        <h2>Categories</h3>
                    </div>
                    
                    <div class="card">
                        <div class="card-body">
                            <div class="accordion accordion-flush" id="accordionExample">
                                @if ($categories->isNotEmpty())
                                    @foreach ($categories as $key => $category)
                                    <div class="accordion-item">
                                        @if ($category->subCategories->isNotEmpty())
                                        <h2 class="accordion-header" id="headingOne">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne-{{ $key }}" aria-expanded="false" aria-controls="collapseOne">
                                                {{ $category->name }}
                                            </button>
                                        </h2>
                                        @else
                                        <a href="{{ route('front.shop',$category->slug) }}" class="nav-item-link"{{ $category->name }}></a>
                                        @endif

                                        @if ($category->subCategories->isNotEmpty())
                                        <div id="collapseOne-{{ $key }}" class="accordion-collapse collapse {{ $categoriesSelected == $category->id ? 'show' : '' }}" aria-labelledby="headingOne" data-bs-parent="#accordionExample" >
                                            <div class="accordion-body">
                                                    @foreach ($category->subCategories as $subCategory)
                                                    <div class="navbar-nav">
                                                        <a href="{{ route('front.shop',[$category->slug,$subCategory->slug]) }}" class="nav-item nav-link  {{ $subCategoriesSelected == $subCategory->id ? 'text-primary' : '' }}">{{ $subCategory->name }}</a>                                            
                                                    </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>  
                                        @endif
                                    @endforeach
                                @endif               
                                                    
                            </div>
                        </div>
                    </div>

                    <div class="sub-title mt-5">
                        <h2>Brand</h3>
                    </div>
                    
                    <div class="card">
                        <div class="card-body">
                            @if ($brands->isNotEmpty())
                                @foreach ($brands as $key => $brand)
                                <div class="form-check mb-2">
                                    <input {{ in_array($brand->id, $brandsArray) ? 'checked' : '' }} class="form-check-input brand-label" type="checkbox" name="brand[]" value="{{ $brand->id }}" id="brand-{{ $brand->id }}">
                                    <label class="form-check-label" for="brand">
                                        {{ $brand->name }}
                                    </label>
                                </div>
                                @endforeach
                            @endif              
                        </div>
                    </div>

                    <div class="sub-title mt-5">
                        <h2>Price</h3>
                    </div>
                    
                    <div class="card">
                        <div class="card-body">
                            <input type="text" class="js-range-slider" name="my_range" value="">                
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="row pb-3">
                        <form action="">
                        <div class="col-12 pb-1">
                            <div class="d-flex align-items-center justify-content-end mb-4">
                                <div class="ml-2">
                                    <select name="pagination" id="pagination" class="form-control" onchange="this.form.submit()">
                                        <option value="6" {{ request('pagination') == 6 ? 'selected' : '' }}>6</option>
                                        <option value="8" {{ request('pagination') == 8 ? 'selected' : '' }}>8</option>
                                        <option value="10" {{ request('pagination') == 10 ? 'selected' : '' }}>10</option>
                                        <option value="12" {{ request('pagination') == 12 ? 'selected' : '' }}>12</option>
                                        <option value="14" {{ request('pagination') == 14 ? 'selected' : '' }}>14</option>
                                    </select>
                                    <select name="sort" id="sort" class="form-control" onchange="this.form.submit()">
                                        <option {{ $sort == 'latest' ? 'selected' : '' }} value="latest">Latest</option>
                                        <option {{ $sort == 'price_desc' ? 'selected' : '' }} value="price_desc">Price High</option>
                                        <option {{ $sort == 'price_asc' ? 'selected' : '' }} value="price_asc">Price Low</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        </form>
                                
                        
                        @if ($product->isNotEmpty())
                            @foreach ($product as $products)
                            @php
                                $productImage = $products->product_image()->first();
                            @endphp
                            <div class="col-md-4">
                                <div class="card product-card">
                                    <div class="product-image position-relative">
                                        @if (!empty($productImage->image))
                                        <a href="" class="product-img"><img class="card-img-top" src="{{ asset('uploads/products/'.$productImage->image) }}" alt=""></a>
                                        @else 
                                        <a href="" class="product-img"><img class="card-img-top" src="{{ asset('admin-assets/img/default-150x150.png') }}" alt=""></a>
                                        @endif
                                        <a class="whishlist" onclick="addToWishlist('{{ $products->id }}')" href="javascript:void(0);"><i class="far fa-heart"></i></a>                              
    
                                        <div class="product-action">
                                            @if ($products->track_qty == 'Yes')
                                                @if ($products->qty > 0)
                                                <a href="javascript:void(0)" onclick="addToCart('{{ $products->id }}')" class="btn btn-dark">Add To Cart
                                                    <i class="fa fa-shopping-cart"></i>
                                                </a>                            
                                                @else
                                                <a href="javascript:void(0)" class="btn btn-dark">
                                                    <i></i> Out Of Stock
                                                </a>        
                                                @endif
                                            @else
                                            <a href="javascript:void(0)" onclick="addToCart('{{ $products->id }}')" class="btn btn-dark">Add To Cart
                                                <i class="fa fa-shopping-cart"></i>
                                            </a>     
                                            @endif
                                        </div>
                                    </div>                        
                                    <div class="card-body text-center mt-3">
                                        <a class="h6 link" href="{{ route('front.product',$products->slug) }}">{{ $products->title }}</a>
                                        <div class="price mt-2">
                                            <span class="h5"><strong>${{ $products->price }}</strong></span>
                                            @if ($products->compare_price > 0)
                                            <span class="h6 text-underline"><del>${{ $products->compare_price }}</del></span>
                                            @endif
                                        </div>
                                    </div>                        
                                </div>                                               
                            </div>  
                            @endforeach
                        @endif

                        <div class="col-md-12 pt-5">
                            {{ $product->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>    
</main>

@endsection

@section('customJs')

<script>
    $(".brand-label").change(function(){
        apply_filters();
    });

    $("#sort").change(function(){
        apply_filters();
    });

    function apply_filters() {
        var brands = [];

        $(".brand-label").each(function() {
            if ($(this).is(":checked") == true) {
                brands.push($(this).val());
            };
        });

        var url = '{{ url()->current() }}?';

        // Price range filter
        url += '&price_min='+slider.result.from+'&price_max='+slider.result.to;

        // Sorting filter
        var keyword = $("#search").val()

        if (keyword.length > 0) {
            url += '&search='+keyword
        }

        // Brand filter
        if (brands.length > 0) {
            url +='&brand='+brands.toString();
        }

        // Sorting fiter
        var sortValue = $("#sort").val();
        if (sortValue) {
            url += '&sort=' + sortValue;
        }

        window.location.href = url+'&brand='+brands.toString();
    }



    rangeSlider = $(".js-range-slider").ionRangeSlider({
        type : "double",
        min : 0,
        max : 50000,
        from : '{{ $priceMin }}',
        step : 10,
        to : '{{ $priceMax }}',
        skin : 'round',
        max_postfix : "+",
        prefix : "$",
        onFinish : function() {
            apply_filters()
        }
    });

    var slider = $(".js-range-slider").data("ionRangeSlider");


</script>

@endsection