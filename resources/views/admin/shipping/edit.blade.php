@extends('admin.layout.app')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">					
					<div class="container-fluid my-2">
						<div class="row mb-2">
							<div class="col-sm-6">
								<h1>Shipping Management</h1>
							</div>
                            <div class="col-sm-6 text-right">
								<a href="{{ route('shipping.create') }}" class="btn btn-primary">Back</a>
							</div>
						</div>
					</div>
					<!-- /.container-fluid -->
				</section>
				<!-- Main content -->
				<section class="content">
					<!-- Default box -->
					<div class="container-fluid">
                        @include('admin.message')
                        <form action="" method="post" id="shippingForm" name="shippingForm">
                        @csrf
						<div class="card">
							<div class="card-body">								
								<div class="row">
									<div class="col-md-4">
										<div class="mb-3">
											<label for="name">Name</label>
                                            <select name="country" id="country" class="form-control">
                                                <option value="">Select Country</option>
                                                @if ($countries->isNotEmpty())
                                                    @foreach ($countries as $country)
                                                        <option {{ $shippingCharge->country_id == $country->id ? 'selected' : '' }} value="{{ $country->id }}">{{ $country->name }}</option>
                                                    @endforeach
                                                    <option {{ $shippingCharge->country_id == 'rest_of_world' ? 'selected' : '' }} value="rest_of_world">Rest of World</option>
                                                @endif
                                            </select>
                                            <p></p>
										</div>
									</div>	
                                    <div class="col-md-4">
                                        <label for="amount">Amount</label>
                                        <input type="text" name="amount" id="amount" class="form-control" placeholder="Amount" value="{{ $shippingCharge->amount }}">
                                        <p></p>
                                    </div>							
								</div>
							</div>							
						</div>
						<div class="pb-5">
							<button type="submit" class="btn btn-primary">Update</button>
						</div>
                        </form>
					</div>
					<!-- /.card -->
				</section>

@endsection

@section('customJs')
<script>

    $("#shippingForm").submit(function(event){
        event.preventDefault();
        var element = $(this);
        $("button[type=submit]").prop('disabled',true);

        $.ajax({
            url: '{{ route("shipping.update", $shippingCharge->id) }}',
            type: 'POST',
            data: element.serializeArray(),
            dataType: 'json',
            success: function(response){
                $("button[type=submit]").prop('disabled',false);
                if (response.status == true){

                window.location.href = "{{ route('shipping.create') }}"

                $('#country').removeClass('is-invalid')
                    .siblings('p')
                    .removeClass('invalid-feedback').html("");

                $('#amount').removeClass('is-invalid')
                    .siblings('p')
                    .removeClass('invalid-feedback').html("");

                } else {
                    window.location.href = " {{ route('shipping.edit', $shippingCharge->id) }} "

                    var errors = response.errors;


                    if (errors.country) {
                        $('#country').addClass('is-invalid')
                            .siblings('p')
                            .addClass('invalid-feedback')
                            .html(errors.country[0]);
                    } else {
                        $('#country').removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback')
                            .html("");
                    }

                    if (errors.amount) {
                        $('#amount').addClass('is-invalid')
                            .siblings('p')
                            .addClass('invalid-feedback')
                            .html(errors.amount[0]);
                    } else {
                        $('#amount').removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback')
                            .html("");
                    }
                }
            },
            error: function(jqXHR, exception){
                console.log("Something went wrong");
            }
        });
    });


</script>


@endsection