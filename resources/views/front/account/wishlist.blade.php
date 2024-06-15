@extends('front.layout.app')

@section('content')
<main>
    <section class="section-5 pt-3 pb-3 mb-3 bg-white">
        <div class="container">
            <div class="light-font">
                <ol class="breadcrumb primary-color mb-0">
                    <li class="breadcrumb-item"><a class="white-text" href="#">My Account</a></li>
                    <li class="breadcrumb-item">Settings</li>
                </ol>
            </div>
        </div>
    </section>

    <section class=" section-11 ">
        <div class="container  mt-5">
            <div class="row">
                @include('front.layout.message')
                <div class="col-md-3">
                    @include('front.account.layout.sidebar')
                </div>
                <div class="col-md-9">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="h5 mb-0 pt-2 pb-2">My Orders</h2>
                        </div>
                        <div class="card-body p-4">
                            @if ($wishlist->isNotEmpty())
                                @foreach ($wishlist as $wl)
                                <div class="d-sm-flex justify-content-between mt-lg-4 mb-4 pb-3 pb-sm-2 border-bottom">
                                    <div class="d-block d-sm-flex align-items-start text-center text-sm-start">
                                        <a class="d-block flex-shrink-0 mx-auto me-sm-4" href="{{ route('front.product',$wl->product->slug) }}" style="width: 10rem;">
                                            @php
                                                $productImage = getProductImage($wl->product_id);
                                            @endphp
    
                                            @if (!empty($productImage))
                                                <img src="{{ asset('uploads/products/'.$productImage->image) }}">
                                            @else
                                                <img src="{{ asset('admin-assets/img/default-150x150.png') }}">
                                            @endif
                                        </a>


                                        <div class="pt-2">
                                            <h3 class="product-title fs-base mb-2"><a href="{{ route('front.product',$wl->product->slug) }}">{{ $wl->product->title }}</a></h3>                                        
                                            <span class="h5"><strong>${{ number_format($wl->product->price,2) }}</strong></span>
                                            <span class="h6 text-underline"><del>${{ number_format($wl->product->compare_price,2) }}</del></span>
                                        </div>
                                    </div>
                                    <div class="pt-2 ps-sm-3 mx-auto mx-sm-0 text-center">
                                        <button onclick="removeProduct('{{ $wl->product_id }}')" class="btn btn-outline-danger btn-sm" type="button"><i class="fas fa-trash-alt me-2"></i>Remove</button>
                                    </div>
                                </div>   
                                @endforeach
                                @else
                                <h3>Wishlist not found</h3>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection

@section('customJs')

<script type="text/javascript">
    function removeProduct(id) {
        if (confirm('Are you sure you want to remove this from your wishlist?')) {
            $.ajax({
                url : '{{ route("remove-wishlist") }}',
                type : 'post',
                data : {id:id},
                dataType : 'json',
                success : function(response) {
                    if (response.status == true) {
                        window.location.href="{{ route('wishlist') }}"
                    } else {
    
                    }
                }
            })
        }
    }
</script>

@endsection