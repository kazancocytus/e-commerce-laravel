@extends('front.layout.app');

@section('content')

<main>
    <section class="section-5 pt-3 pb-3 mb-3 bg-white">
        <div class="container">
            <div class="light-font">
                <ol class="breadcrumb primary-color mb-0">
                    <li class="breadcrumb-item"><a class="white-text" href="{{ route('front.home') }}">Home</a></li>
                    <li class="breadcrumb-item">Login</li>
                </ol>
                @if (Session::has('success'))
                    <div class="alert alert-success">
                        {{ Session::get('success') }}
                    </div>
                @endif
                @if (Session::has('error'))
                    <div class="alert alert-danger">
                        {{ Session::get('error') }}
                    </div>
                @endif
            </div>
        </div>
    </section>

    <section class=" section-10">
        <div class="container">
            <div class="login-form">    
                <form action="{{ route('proccess-forgot-password') }}" method="post" name="forgotPasswordForm" id="forgotPasswordForm">
                    @csrf
                    <h4 class="modal-title">Forgot Password</h4>
                    <div class="form-group">
                        <input type="text" class="form-control @error('email') is-invalid @enderror" name="email" id="email" placeholder="Email" value="{{ old('email') }}">
                        @error('email')
                            <p class="invalid-feedback">{{ $message }}</p>
                        @enderror
                    </div>
                    <input type="submit" class="btn btn-dark btn-block btn-lg" value="Send">              
                </form>			
                <div class="text-center small">Have you remembered your account? <a href="{{ route('login') }}">Login</a></div>
            </div>
        </div>
    </section>
</main>

@endsection


@section('customJs')


@endsection