@extends('admin.layout.app')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">					
					<div class="container-fluid my-2">
						<div class="row mb-2">
							<div class="col-sm-6">
								<h1>Shipping Management</h1>
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
                                                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                                                    @endforeach
                                                    <option value="rest_of_world">Rest of World</option>
                                                @endif
                                            </select>
                                            <p></p>
										</div>
									</div>	
                                    <div class="col-md-4">
                                        <label for="amount">Amount</label>
                                        <input type="text" name="amount" id="amount" class="form-control" placeholder="Amount">
                                        <p></p>
                                    </div>							
								</div>
							</div>							
						</div>
						<div class="pb-5">
							<button type="submit" class="btn btn-primary">Create</button>
						</div>
                        </form>
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <table class="table table-striped">
                                            <tr>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Amount</th>
                                                <th>Action</th>
                                            </tr>
                                            @if ($shippingCharge->isNotEmpty())
                                                @foreach ($shippingCharge as $shipping)
                                                    <tr>
                                                        <td>{{ $shipping->id }}</td>
                                                        <td>{{ $shipping->country_id == 'rest_of_world' ? 'Rest of World' : $shipping->name}}</td>
                                                        <td>${{ $shipping->amount }}</td>
                                                        <td>
                                                            <a href="{{ route('shipping.edit',$shipping->id) }}" class="btn btn-primary">Edit</a>
                                                            <a href="javascript:void(0)" onclick="deleteShipping('{{ $shipping->id }}');" class="btn btn-danger">Delete</a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
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
            url: '{{ route("shipping.store") }}',
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

    function deleteShipping(id) 
    {

    var url = '{{ route("shipping.delete", ":id") }}';
    var newUrl = url.replace(':id', id);

    if (confirm("Are you sure you want to delete this shipping?")) {
        $.ajax({
            url: newUrl,
            type: 'DELETE',
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.status) {
                    window.location.href = "{{ route('shipping.create') }}";
                } else {
                    alert('Failed to delete shipping.');
                }
            },
            error: function(xhr, status, error) {
                alert('An error occurred: ' + error);
            }
        });
    }
    }


</script>


@endsection