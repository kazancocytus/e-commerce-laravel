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
                <form action="" method="post" name="otpForm" id="otpForm">
                    @csrf
                    <h4 class="modal-title">Verfiy your otp code</h4>
                    <div class="form-group">
                        <input type="text" class="form-control" name="otp" id="otp" placeholder="otp" value="{{ old('otp') }}">
                        <p></p>
                    </div>
                    <input type="submit" class="btn btn-dark btn-block btn-lg" value="Send">              
                </form>			
                <div class="text-center small">
                    Your OTP expired? 
                    <a href="javascript:void(0);" id="resendOtpBtn">Resend</a>
                </div>
            </div>
        </div>
    </section>
</main>

@endsection


@section('customJs')

<script type="text/javascript">

    $("#otpForm").submit(function(){
        event.preventDefault();

        $("#button[type='submit']").prop('disabled',true)

        $.ajax({
        url : '{{ route("send-otp") }}',
        type : 'post',
        data : $(this).serializeArray(),
        dataType : 'json',
        success : function(response){
            $("#button[type='submit']").prop('disabled',false)



            if (response.status == false) {
                if (response.error instanceof Object) {
                    $.each(response.error, function(key, value) {
                        if (key == 'otp') {
                            $("#otp").siblings("p").addClass('invalid-feedback').html(value);
                            $("#otp").addClass('is-invalid')
                        }
                    });
                } else {
                    $("#otp").siblings("p").addClass('invalid-feedback').html(response.error);
                    $("#otp").addClass('is-invalid')
                }
                } else {
                $("#otp").siblings("p").removeClass('invalid-feedback').html('');
                $("#otp").removeClass('is-invalid')
                window.location.href = "{{ route('login') }}"
                }
                   
        },
        error : function(jQXHR, exception) {
            console.log("something went wrong");
        }
        });
    });


    $("#resendOtpBtn").click(function() {
        $.ajax({
            url: '{{ route("resend-otp") }}',
            type: 'post',
            data: { email: '{{ old('email', session('otp_email')) }}' },
            dataType: 'json',
            success: function(response) {
                if (response.status === true) {
                    alert(response.message)
                } else {
                    alert(response.error)
                }
            },
            error: function() {
                console.log("Something went wrong");
            }
        });
    });



</script>

@endsection