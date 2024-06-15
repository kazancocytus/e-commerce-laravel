@extends('front.layout.app')

@section('content')
<main>
    <section class="section-5 pt-3 pb-3 mb-3 bg-white">
        <div class="container">
            <div class="light-font">
                <ol class="breadcrumb primary-color mb-0">
                    <li class="breadcrumb-item"><a class="white-text" href="{{ route('front.home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a class="white-text" href="{{ route('front.shop') }}">Shop</a></li>
                    <li class="breadcrumb-item">{{ $product->title }}</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="section-7 pt-3 mb-3">
        <div class="container">
            @include('front.layout.message')
            <div class="row ">
                <div class="col-md-5">
                    <div id="product-carousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner bg-light">
                            @if ($product->product_image)
                                @foreach ($product->product_image as $key => $productImage)
                                <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                                    <img class="w-100 h-100" src="{{ asset('uploads/products/'.$productImage->image) }}" alt="Image">
                                </div>
                                @endforeach
                            @endif
                        </div>
                        <a class="carousel-control-prev" href="#product-carousel" data-bs-slide="prev">
                            <i class="fa fa-2x fa-angle-left text-dark"></i>
                        </a>
                        <a class="carousel-control-next" href="#product-carousel" data-bs-slide="next">
                            <i class="fa fa-2x fa-angle-right text-dark"></i>
                        </a>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="bg-light right">
                        <h1>{{ $product->title }}</h1>
                        <div class="d-flex mb-3">
                            <!-- <div class="text-primary mr-2">
                                <small class="fas fa-star"></small>
                                <small class="fas fa-star"></small>
                                <small class="fas fa-star"></small>
                                <small class="fas fa-star-half-alt"></small>
                                <small class="far fa-star"></small>
                            </div> -->
                            <div class="star-rating product mt-2" title="">
                                <div class="back-stars">
                                    <i class="fa fa-star" aria-hidden="true"></i>
                                    <i class="fa fa-star" aria-hidden="true"></i>
                                    <i class="fa fa-star" aria-hidden="true"></i>
                                    <i class="fa fa-star" aria-hidden="true"></i>
                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                
                                        <div class="front-stars" style="width: {{ $avgRatingPercen }}%">
                                            <i class="fa fa-star" aria-hidden="true"></i>
                                            <i class="fa fa-star" aria-hidden="true"></i>
                                            <i class="fa fa-star" aria-hidden="true"></i>
                                            <i class="fa fa-star" aria-hidden="true"></i>
                                            <i class="fa fa-star" aria-hidden="true"></i>
                                        </div>
                                </div>
                            </div>  
                            <small class="pt-1">({{ $product->product_ratings_count > 1 ? $product->product_ratings_count.' Reviews' : $product->product_ratings_count. ' Review' }} )</small>
                        </div>
                        @if ($product->compare_price)
                        <h2 class="price text-secondary"><del>${{ $product->compare_price }}</del></h2>
                        @endif
                        <h2 class="price ">${{ $product->price }}</h2>

                        {!! $product->short_description !!}
                         @if ($product->track_qty == 'Yes')
                            @if ($product->qty > 0)
                                <a href="javascript:void(0)" onclick="addToCart('{{ $product->id }}')" class="btn btn-dark">Add To Cart
                                    <i class="fa fa-shopping-cart"></i>
                                </a>                            
                            @else
                                <a href="javascript:void(0)" class="btn btn-dark">
                                    <i></i> Out Of Stock
                                </a>        
                            @endif
                        @else
                            <a href="javascript:void(0)" onclick="addToCart('{{ $product->id }}')" class="btn btn-dark">Add To Cart
                                <i class="fa fa-shopping-cart"></i>
                            </a>     
                        @endif
                    </div>
                </div>

                <div class="col-md-12 mt-5">
                    <div class="bg-light">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="description-tab" data-bs-toggle="tab" data-bs-target="#description" type="button" role="tab" aria-controls="description" aria-selected="true">Description</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="shipping-tab" data-bs-toggle="tab" data-bs-target="#shipping" type="button" role="tab" aria-controls="shipping" aria-selected="false">Shipping & Returns</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab" aria-controls="reviews" aria-selected="false">Reviews</button>
                            </li>
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="description" role="tabpanel" aria-labelledby="description-tab">
                                {!! $product->description !!}
                            </div>
                            <div class="tab-pane fade" id="shipping" role="tabpanel" aria-labelledby="shipping-tab">
                            {!! $product->shipping_returns !!}
                            </div>
                            <div class="tab-pane fade" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">
                            <div class="col-md-8">
                                <form action="" id="ratingForm" name="ratingForm">
                                    <input type="hidden" name="product_id" id="product_id" value="{{ $product->id }}">
                                <div class="row">
                                    <h3 class="h4 pb-3">Write a Review</h3>
                                    <div class="form-group col-md-6 mb-3">
                                        <label for="name">Name</label>
                                        <input type="text" class="form-control" name="name" id="name" placeholder="Name">
                                        <p></p>
                                    </div>
                                    <div class="form-group col-md-6 mb-3">
                                        <label for="email">Email</label>
                                        <input type="text" class="form-control" name="email" id="email" placeholder="Email">
                                        <p></p>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="rating">Rating</label>
                                        <br>
                                        <div class="rating" style="width: 10rem">
                                            <input id="rating-5" type="radio" name="rating" value="5"/><label for="rating-5"><i class="fas fa-3x fa-star"></i></label>
                                            <input id="rating-4" type="radio" name="rating" value="4"  /><label for="rating-4"><i class="fas fa-3x fa-star"></i></label>
                                            <input id="rating-3" type="radio" name="rating" value="3"/><label for="rating-3"><i class="fas fa-3x fa-star"></i></label>
                                            <input id="rating-2" type="radio" name="rating" value="2"/><label for="rating-2"><i class="fas fa-3x fa-star"></i></label>
                                            <input id="rating-1" type="radio" name="rating" value="1"/><label for="rating-1"><i class="fas fa-3x fa-star"></i></label>
                                        </div>
                                        <p class="product-rating-errors"></p>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="">How was your overall experience?</label>
                                        <textarea name="review"  id="review" class="form-control" cols="30" rows="10" placeholder="How was your overall experience?"></textarea>
                                        <p></p>
                                    </div>
                                    <div>
                                        <button type="submit" class="btn btn-dark">Submit</button>
                                    </div>
                                    
                                </div>
                                </form>
                            </div>
                            <div class="col-md-12 mt-5">
                                <div class="overall-rating mb-3">
                                    <div class="d-flex">
                                        <h1 class="h3 pe-3">{{ $avgRating }}</h1>
                                        <div class="star-rating mt-2" title="">
                                            <div class="back-stars">
                                                <i class="fa fa-star" aria-hidden="true"></i>
                                                <i class="fa fa-star" aria-hidden="true"></i>
                                                <i class="fa fa-star" aria-hidden="true"></i>
                                                <i class="fa fa-star" aria-hidden="true"></i>
                                                <i class="fa fa-star" aria-hidden="true"></i>
                                                
                                                <div class="front-stars" style="width: {{ $avgRatingPercen }}%">
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                </div>
                                            </div>
                                        </div>  
                                        <div class="pt-2 ps-2">({{ $product->product_ratings_count > 1 ? $product->product_ratings_count.' Reviews' : $product->product_ratings_count. ' Review' }} )</div>
                                    </div>
                                    
                                </div>
                                @if ($product->product_ratings->isNotEmpty())
                                    @foreach ($product->product_ratings as $rating)
                                    @php
                                        $ratingPercen = ($rating->rating*100)/5
                                    @endphp
                                    <div class="rating-group mb-4">
                                       <span> <strong>{{ $rating->username }} </strong></span>
                                        <div class="star-rating mt-2" title="">
                                            <div class="back-stars">
                                                <i class="fa fa-star" aria-hidden="true"></i>
                                                <i class="fa fa-star" aria-hidden="true"></i>
                                                <i class="fa fa-star" aria-hidden="true"></i>
                                                <i class="fa fa-star" aria-hidden="true"></i>
                                                <i class="fa fa-star" aria-hidden="true"></i>
                                                
                                                <div class="front-stars" style="width: {{ $ratingPercen }}%">
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                </div>
                                            </div>
                                        </div>   
                                        <div class="my-3">
                                            <p>{{ $rating->comment }}</p>
                                        </div>
                                    </div>
                                    @endforeach
                                    @else
                                    <h4>Ratings not yet</h4>
                                @endif
                            </div>
                        </div>
                            </div>
                        </div>
                    </div>
                </div> 
            </div>           
        </div>
    </section>

    @if (!empty($relatedProduct))
    <section class="pt-5 section-8">
        <div class="container">
            <div class="section-title">
                <h2>Related Products</h2>
            </div> 
            <div class="col-md-12">
                <div id="related-products" class="carousel">
                        @foreach ($relatedProduct as $product)
                        @php
                            $productImage = $product->product_image->first();
                        @endphp
                        <div class="card product-card">
                            <div class="product-image position-relative">
                                <a href="{{ route('front.product',$product->slug) }}" class="product-img">
                                    @if (!empty($productImage))
                                    <img class="card-img-top" src="{{ asset('uploads/products/'.$productImage->image) }}" alt="">
                                    @else
                                    <img class="card-img-top" src="{{ asset('admin-assets/img/default-150x150.png') }}" alt="">
                                    @endif
                                </a>
                                <a class="whishlist" onclick="addToWishlist('{{ $product->id }}')" href="javascript:void(0);"><i class="far fa-heart"></i></a>                           
    
                                <div class="product-action">
                                    @if ($product->track_qty == 'Yes')
                                        @if ($product->qty > 0)
                                        <a href="javascript:void(0)" onclick="addToCart('{{ $product->id }}')" class="btn btn-dark">Add To Cart
                                            <i class="fa fa-shopping-cart"></i>
                                        </a>                            
                                        @else
                                        <a href="javascript:void(0)" class="btn btn-dark">
                                            <i></i> Out Of Stock
                                        </a>        
                                        @endif
                                    @else
                                    <a href="javascript:void(0)" onclick="addToCart('{{ $product->id }}')" class="btn btn-dark">Add To Cart
                                        <i class="fa fa-shopping-cart"></i>
                                    </a>     
                                    @endif
                                </div>
                            </div>                        
                            <div class="card-body text-center mt-3">
                                <a class="h6 link" href="{{ route('front.product',$product->slug) }}">{{ $product->title }}</a>
                                <div class="price mt-2">
                                    @if ($product->compare_price > 0)
                                    <span class="h5"><strong>${{ $product->price }}</strong></span>
                                    @endif
                                    <span class="h6 text-underline"><del>${{ $product->compare_price }}</del></span>
                                </div>
                            </div>                        
                        </div> 
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </section>
</main>

@endsection


@section('customJs')

<script type="text/javascript">

    $("#ratingForm").submit(function(event) {
        event.preventDefault()

        $.ajax({
            url : '{{ route("save-rating",$product->id) }}',
            type : 'post',
            data : $(this).serializeArray(),
            dataType : 'json',
            success : function(response) {

                if (response.status == false) {
                
                    var errors = response.errors

                    if (errors.name) {
                        $("#name").addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors.name)
                    } else {
                        $("#name").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('')
                    }

                    if (errors.email) {
                        $("#email").addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors.email)
                    } else {
                        $("#email").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('')
                    }

                    if (errors.review) {
                        $("#review").addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors.review)
                    } else {
                        $("#review").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('')
                    }

                    if (errors.rating) {
                        $(".product-rating-errors").addClass('text-danger').html(errors.rating)
                    } else {
                        $(".product-rating-errors").removeClass('text-danger').html('')
                    }
                } else {
                    window.location.reload()
                }

            }
        })

    })

</script>

@endsection