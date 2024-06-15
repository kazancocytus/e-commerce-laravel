@extends('front.layout.app');

@section('content')

<main>
    <section class="section-5 pt-3 pb-3 mb-3 bg-white">
        <div class="container">
            <div class="light-font">
                <ol class="breadcrumb primary-color mb-0">
                    <li class="breadcrumb-item"><a class="white-text" href="{{ route('profil.account') }}">My Account</a></li>
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
                            <h2 class="h5 mb-0 pt-2 pb-2">Personal Information</h2>
                        </div>
                        <form action="" method="post" name="profileForm" id="profileForm">
                        <div class="card-body p-4">
                            <div class="row">
                                <div class="mb-3">               
                                    <label for="name">Name</label>
                                    <input type="text" value="{{ $user->name }}" name="name" id="name" placeholder="Enter Your Name" class="form-control">
                                    <p></p>
                                </div>
                                <div class="mb-3">            
                                    <label for="email">Email</label>
                                    <input type="text" value="{{ $user->email }}" name="email"  id="email" placeholder="Enter Your Email" class="form-control">
                                    <p></p>
                                </div>
                                <div class="mb-3">                                    
                                    <label for="phone">Phone</label>
                                    <input type="text" value="{{ $user->phone }}" name="phone" id="phone" placeholder="Enter Your Phone" class="form-control">
                                    <p></p>
                                </div>

                                <div class="d-flex">
                                    <button type="submit" class="btn btn-dark">Update</button>
                                </div>
                            </div>
                        </div>
                        </form>
                    </div>

                    <div class="card mt-5">
                        <div class="card-header">
                            <h2 class="h5 mb-0 pt-2 pb-2">Address</h2>
                        </div>
                    <form action="" name="addressForm" id="addressForm">
                        <input type="hidden" name="user_id" id="user_id" value="{{ !empty($customerAddress->user_id) ? $customerAddress->user_id : '' }}">
                        <div class="card-body pt-4">
                            <div class="row">
                                <div class="mb-3">
                                    <label for="first_name">First Name</label>
                                    <input type="text" value="{{ !empty($customerAddress->first_name)  ? $customerAddress->first_name : ''}}" name="first_name" id="first_name" placeholder="Enter your first name" class="form-control">
                                    <p></p>
                                </div>
                                <div class="mb-3">
                                    <label for="last_name">Last Name</label>
                                    <input type="text" value="{{ !empty($customerAddress->last_name) ? $customerAddress->last_name : '' }}" name="last_name" id="last_name" placeholder="Enter yout last name" class="form-control">
                                    <p></p>
                                </div>
                                <div class="mb-3">
                                    <label for="email">Email</label>
                                    <input type="text" value="{{ !empty($customerAddress->email) ? $customerAddress->email : '' }}" name="email" id="email_address" placeholder="Enter your email" class="form-control">
                                    <p></p>
                                </div>
                                <div class="mb-3">
                                    <label for="mobile">Mobile</label>
                                    <input type="text" value="{{ !empty($customerAddress->mobile) ? $customerAddress->mobile : '' }}" name="mobile" id="mobile" placeholder="Enter your mobile phone" class="form-control">
                                    <p></p>
                                </div>
                                <div class="mb-3">
                                    <select name="country" id="country" class="form-control">
                                        <option value="">Select a Country</option>
                                        @if ($countries->isNotEmpty())
                                            @foreach ($countries as $country)
                                                <option value="{{ $country->id }}" {{ (!empty($customerAddress) && $customerAddress->country_id == $country->id) ? 'selected' : '' }}>{{ $country->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <p></p>
                                </div> 
                                <div class="mb-3">
                                    <label for="address">Address</label>
                                    <textarea name="address" id="address" class="form-control" cols="30" rows="5">{{ !empty($customerAddress->address) ? $customerAddress->address : '' }}</textarea>
                                    <p></p>
                                </div>
                                <div class="mb-3">
                                    <label for="apartement">Apartement</label>
                                    <input type="text" value="{{ !empty($customerAddress->apartement) ? $customerAddress->apartement : '' }}" name="apartement" id="apartement" placeholder="Enter your apartement" class="form-control">
                                    <p></p>
                                </div>
                                <div class="mb-3">
                                    <label for="city">City</label>
                                    <input type="text" value="{{ !empty($customerAddress->city) ? $customerAddress->city : '' }}" name="city" id="city" placeholder="Enter your city " class="form-control">
                                    <p></p>
                                </div>
                                <div class="mb-3">
                                    <label for="state">State</label>
                                    <input type="text" value="{{ !empty($customerAddress->state) ? $customerAddress->state : '' }}" name="state" id="state" placeholder="Enter your state" class="form-control">
                                    <p></p>
                                </div>
                                <div class="mb-3">
                                    <label for="zip">Zip</label>
                                    <input type="text" value="{{ !empty($customerAddress->zip) ? $customerAddress->zip : '' }}" name="zip" id="zip" placeholder="Enter your zip" class="form-control">
                                    <p></p>
                                </div>
                                <div class="d-flex">
                                    <button type="submit" class="btn btn-dark">Update</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    </div>

                </div>
            </div>
        </div>
    </section>
</main>

@endsection


@section('customJs')

<script type="text/javascript">
    $("#profileForm").submit(function(event) {
        event.preventDefault()

        $.ajax({
            url : '{{ route("update-profile") }}',
            type : 'post',
            data : $(this).serializeArray(),
            dataType : 'json',
            success : function(response) {
                var errors = response.errors
                if (response.status == true) {

                    window.location.href = "{{ route('profil.account') }}"

                } else {
                    if (errors.name) {
                        $("#name").addClass('is-invalid').siblings('p').html(errors.name).addClass('invalid-feedback')
                    } else {
                        $("#name").removeClass('is-invalid').siblings('p').html('').removeClass('invalid-feedback')
                    }

                    if (errors.email) {
                        $("#email").addClass('is-invalid').siblings('p').html(errors.email).addClass('invalid-feedback')
                    } else {
                        $("#email").removeClass('is-invalid').siblings('p').html('').removeClass('invalid-feedback')
                    }

                    if (errors.phone) {
                        $("#phone").addClass('is-invalid').siblings('p').html(errors.phone).addClass('invalid-feedback')
                    } else {
                        $("#phone").removeClass('is-invalid').siblings('p').html('').removeClass('invalid-feedback')
                    }
                }
            }
        })
    })

    $("#addressForm").submit(function(event) {
        event.preventDefault()

        $.ajax({
            url : '{{ route("update-address") }}',
            type : 'post',
            data : $(this).serializeArray(),
            dataType : 'json',
            success : function(response) {
                var errors = response.errors
                if (response.status == true) {

                    window.location.href = "{{ route('profil.account') }}"

                } else {
                    if (errors.first_name) {
                        $("#first_name").addClass('is-invalid').siblings('p').html(errors.name).addClass('invalid-feedback')
                    } else {
                        $("#first_name").removeClass('is-invalid').siblings('p').html('').removeClass('invalid-feedback')
                    }

                    if (errors.last_name) {
                        $("#last_name").addClass('is-invalid').siblings('p').html(errors.name).addClass('invalid-feedback')
                    } else {
                        $("#last_name").removeClass('is-invalid').siblings('p').html('').removeClass('invalid-feedback')
                    }

                    if (errors.email) {
                        $("#email_address").addClass('is-invalid').siblings('p').html(errors.email).addClass('invalid-feedback')
                    } else {
                        $("#email_address").removeClass('is-invalid').siblings('p').html('').removeClass('invalid-feedback')
                    }

                    if (errors.mobile) {
                        $("#mobile").addClass('is-invalid').siblings('p').html(errors.mobile).addClass('invalid-feedback')
                    } else {
                        $("#mobile").removeClass('is-invalid').siblings('p').html('').removeClass('invalid-feedback')
                    }

                    if (errors.country) {
                        $("#country").addClass('is-invalid').siblings('p').html(errors.country).addClass('invalid-feedback')
                    } else {
                        $("#country").removeClass('is-invalid').siblings('p').html('').removeClass('invalid-feedback')
                    }

                    if (errors.city) {
                        $("#city").addClass('is-invalid').siblings('p').html(errors.city).addClass('invalid-feedback')
                    } else {
                        $("#city").removeClass('is-invalid').siblings('p').html('').removeClass('invalid-feedback')
                    }
                    
                    if (errors.state) {
                        $("#state").addClass('is-invalid').siblings('p').html(errors.state).addClass('invalid-feedback')
                    } else {
                        $("#state").removeClass('is-invalid').siblings('p').html('').removeClass('invalid-feedback')
                    }

                    if (errors.zip) {
                        $("#zip").addClass('is-invalid').siblings('p').html(errors.zip).addClass('invalid-feedback')
                    } else {
                        $("#zip").removeClass('is-invalid').siblings('p').html('').removeClass('invalid-feedback')
                    }

                    if (errors.address) {
                        $("#address").addClass('is-invalid').siblings('p').html(errors.address).addClass('invalid-feedback')
                    } else {
                        $("#address").removeClass('is-invalid').siblings('p').html('').removeClass('invalid-feedback')
                    }
                }
            }
        })
    })
</script>

@endsection