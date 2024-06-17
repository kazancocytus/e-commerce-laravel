@extends('front.layout.app')

@section('content')

    <section class="container">
        <div class="col-md-12 text-center py-5">
            @if (Session::has('success'))
                <div class="alert alert-success">
                    {{ Session::get('success') }}
                </div>
            @endif
            <h1>Thank You</h1>
            <p>Your order is: {{ $id }}</p>
            <p>Check your email to see your shopping details !</p>
            <a href="{{ route('front.home') }}" class="btn btn-dark">Back to Home</a>
        </div>
    </section>

@endsection