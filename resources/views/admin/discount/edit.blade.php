@extends('admin.layout.app')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">					
					<div class="container-fluid my-2">
						<div class="row mb-2">
							<div class="col-sm-6">
								<h1>Create Discount</h1>
							</div>
							<div class="col-sm-6 text-right">
								<a href="{{ route('discount') }}" class="btn btn-primary">Back</a>
							</div>
						</div>
					</div>
					<!-- /.container-fluid -->
				</section>
				<!-- Main content -->
				<section class="content">
					<!-- Default box -->
					<div class="container-fluid">
                        <form action="" method="post" id="discountForm" name="discountForm">
                        @csrf
                        <input type="hidden" name="id" id="id" value="{{ $discount->id }}">
						<div class="card">
							<div class="card-body">								
								<div class="row">
									<div class="col-md-6">
										<div class="mb-3">
											<label for="code">Code</label>
											<input type="text" name="code" id="code" class="form-control" placeholder="Coupen code" value="{{ $discount->code }}">	
                                            <p></p>
										</div>
									</div>
									<div class="col-md-6">
										<div class="mb-3">
											<label for="name">Name</label>
											<input type="text" name="name" id="name" class="form-control" placeholder="Name coupon" value="{{ $discount->name }}">	
                                            <p></p>
										</div>
									</div>		
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="description">Description</label>
                                            <textarea name="description" id="description" cols="50" rows="5">{{ $discount->description }}</textarea>
                                            <p></p>
                                        </div>
                                    </div>							
									<div class="col-md-6">
										<div class="mb-3">
											<label for="max_uses">Max Uses</label>
											<input type="text" name="max_uses" id="max_uses" class="form-control" placeholder="Max Uses" value="{{ $discount->max_uses }}">	
											<p></p>
										</div>
									</div>									
									<div class="col-md-6">
										<div class="mb-3">
											<label for="max_uses_user">Max Uses User</label>
											<input type="text" name="max_uses_user" id="max_uses_user" class="form-control" placeholder="Max Uses User" value="{{ $discount->max_uses_user }}">	
											<p></p>
										</div>
									</div>																	
									<div class="col-md-6">
										<div class="mb-3">
											<label for="type">Type</label>
											<select name="type" id="type" class="form-control">
                                                <option {{ $discount->type == 'fixed' ? 'selected' : '' }} value="fixed">Fixed</option>
                                                <option {{ $discount->type == 'percent' ? 'selected' : '' }} value="percent">Percent</option>
                                            </select>
                                            <p></p>
										</div>
									</div>
                                    <div class="col-md-6">
										<div class="mb-3">
											<label for="discount_amount">Discount Amount</label>
											<input type="text" name="discount_amount" id="discount_amount" class="form-control" placeholder="Discount Amount" value="{{ $discount->discount_amount }}">	
											<p></p>
										</div>
									</div>	
                                    <div class="col-md-6">
										<div class="mb-3">
											<label for="min_amount">Min Amount</label>
											<input type="text" name="min_amount" id="min_amount" class="form-control" placeholder="Min Amount" value="{{ $discount->min_amount }}">	
											<p></p>
										</div>
									</div>	
                                    <div class="col-md-6">
										<div class="mb-3">
											<label for="status">Status</label>
											<select name="status" id="status" class="form-control">
                                                <option {{ $discount->status == 1 ? 'selected' : '' }} value="1">Active</option>
                                                <option {{ $discount->status == 0 ? 'selected' : '' }} value="0">Block</option>
                                            </select>
                                            <p></p>
										</div>
									</div>
                                    <div class="col-md-6">
										<div class="mb-3">
											<label for="starts_at">Start At</label>
											<input type="text" name="starts_at" id="starts_at" class="form-control" placeholder="Start at" value="{{ $discount->starts_at }}">	
											<p></p>
										</div>
									</div>
                                    <div class="col-md-6">
										<div class="mb-3">
											<label for="expired_at">Expired At</label>
											<input type="text" name="expired_at" id="expired_at" class="form-control" placeholder="Expired at" value="{{ $discount->expired_at }}">	
											<p></p>
										</div>
									</div>
								</div>
							</div>							
						</div>
						<div class="pb-5 pt-3">
							<button type="submit" class="btn btn-primary">Update</button>
						</div>
                        </form>
					</div>
					<!-- /.card -->
				</section>

@endsection

@section('customJs')
<script>

    $("#discountForm").submit(function(event){
        event.preventDefault();
        var element = $(this);
        $("button[type=submit]").prop('disabled',true);

        $.ajax({
            url: '{{ route("discount.update") }}',
            type: 'POST',
            data: element.serializeArray(),
            dataType: 'json',
            success: function(response){
                $("button[type=submit]").prop('disabled',false);
                if (response.status == true){

                window.location.href = "{{ route('discount') }}"

                $('#code').removeClass('is-invalid')
                    .siblings('p')
                    .removeClass('invalid-feedback').html("");

                $('#type').removeClass('is-invalid')
                    .siblings('p')
                    .removeClass('invalid-feedback').html("");
                
                $('#discount_amount').removeClass('is-invalid')
                    .siblings('p')
                    .removeClass('invalid-feedback').html("");
                
                $('#status').removeClass('is-invalid')
                    .siblings('p')
                    .removeClass('invalid-feedback').html("");
                
                $('#starts_at').removeClass('is-invalid')
                    .siblings('p')
                    .removeClass('invalid-feedback').html("");

                $('#expired_at').removeClass('is-invalid')
                    .siblings('p')
                    .removeClass('invalid-feedback').html("");

                } else {
                    var errors = response.errors;

                    if (errors.code) {
                        $('#code').addClass('is-invalid')
                            .siblings('p')
                            .addClass('invalid-feedback')
                            .html(errors.code[0]);
                    } else {
                        $('#code').removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback')
                            .html("");
                    }

                    if (errors.max_uses) {
                        $('#max_uses').addClass('is-invalid')
                            .siblings('p')
                            .addClass('invalid-feedback')
                            .html(errors.max_uses[0]);
                    } else {
                        $('#max_uses').removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback')
                            .html("");
                    }

                    if (errors.max_uses_user) {
                        $('#max_uses_user').addClass('is-invalid')
                            .siblings('p')
                            .addClass('invalid-feedback')
                            .html(errors.max_uses_user[0]);
                    } else {
                        $('#max_uses_user').removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback')
                            .html("");
                    }

                    if (errors.discount_amount) {
                        $('#discount_amount').addClass('is-invalid')
                            .siblings('p')
                            .addClass('invalid-feedback')
                            .html(errors.discount_amount[0]);
                    } else {
                        $('#discount_amount').removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback')
                            .html("");
                    }

                    if (errors.min_amount) {
                        $('#min_amount').addClass('is-invalid')
                            .siblings('p')
                            .addClass('invalid-feedback')
                            .html(errors.min_amount[0]);
                    } else {
                        $('#min_amount').removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback')
                            .html("");
                    }

                    if (errors.starts_at) {
                        $('#starts_at').addClass('is-invalid')
                            .siblings('p')
                            .addClass('invalid-feedback')
                            .html(errors["starts_at"]);
                    } else {
                        $('#starts_at').removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback')
                            .html("");
                    }

                    if (errors.expired_at) {
                        $('#expired_at').addClass('is-invalid')
                            .siblings('p')
                            .addClass('invalid-feedback')
                            .html(errors["expired_at"]);
                    } else {
                        $('#expired_at').removeClass('is-invalid')
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

    $("#name").change(function(){

    element = $(this);
    $("button[type=submit]").prop('disabled',true);

    $.ajax({
        url : '{{ route("getSlug") }}',
        type : 'get',
        data : {title: element.val()},
        dataType : 'json',
        success :  function(response){
            if (response["status"] === true){
                $("button[type=submit]").prop('disabled',false);
                $("#slug").val(response["slug"])
            }
        }
    });

    });

    $(document).ready(function(){
        $('#starts_at').datetimepicker({
            // options here
            format:'Y-m-d H:i:s',
        });
        $('#expired_at').datetimepicker({
            // options here
            format:'Y-m-d H:i:s',
        });
    });

</script>


@endsection