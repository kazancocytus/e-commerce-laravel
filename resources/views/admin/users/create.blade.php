@extends('admin.layout.app')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">					
					<div class="container-fluid my-2">
						<div class="row mb-2">
							<div class="col-sm-6">
								<h1>Create User</h1>
							</div>
							<div class="col-sm-6 text-right">
								<a href="{{ route('admin.users') }}" class="btn btn-primary">Back</a>
							</div>
						</div>
					</div>
					<!-- /.container-fluid -->
				</section>
				<!-- Main content -->
				<section class="content">
					<!-- Default box -->
					<div class="container-fluid">
                        <form action="" method="post" id="createUserForm" name="createUserForm">
                        @csrf
						<div class="card">
							<div class="card-body">								
								<div class="row">
									<div class="col-md-6">
										<div class="mb-3">
											<label for="name">Name</label>
											<input type="text" name="name" id="name" class="form-control" placeholder="Name">	
                                            <p></p>
										</div>
									</div>
									<div class="col-md-6">
										<div class="mb-3">
											<label for="email">Email</label>
											<input type="text" name="email" id="email"  class="form-control" placeholder="email">	
                                            <p></p>
										</div>
									</div>		
									<div class="col-md-6">
										<div class="mb-3">
											<label for="phone">Phone</label>
											<input type="text" name="phone" id="phone"  class="form-control" placeholder="Phone">	
                                            <p></p>
										</div>
									</div>		
									<div class="col-md-6">
										<div class="mb-3">
											<label for="password">Password</label>
											<input type="password" name="password" id="password"  class="form-control" placeholder="password">	
                                            <p></p>
										</div>
									</div>		
									<div class="col-md-6">
										<div class="mb-3">
											<label for="password_confirmation">Confirm Password</label>
											<input type="password" name="password_confirmation" id="password_confirmation"  class="form-control" placeholder="Confirm Password">	
                                            <p></p>
										</div>
									</div>						
									<div class="col-md-6">
										<div class="mb-3">
											<label for="status">Status</label>
											<select name="status" id="status" class="form-control">
                                                <option value="1">Active</option>
                                                <option value="0">Block</option>
                                            </select>	
										</div>
									</div>																	
								</div>
							</div>							
						</div>
						<div class="pb-5 pt-3">
							<button type="submit" class="btn btn-primary">Create</button>
						</div>
                        </form>
					</div>
					<!-- /.card -->
				</section>

@endsection

@section('customJs')
<script>

    $("#createUserForm").submit(function(event){
        event.preventDefault();
        var element = $(this);
        $("button[type=submit]").prop('disabled',true);

        $.ajax({
            url: '{{ route("users-store") }}',
            type: 'POST',
            data: element.serializeArray(),
            dataType: 'json',
            success: function(response){
                $("button[type=submit]").prop('disabled',false);
                if (response.status == true){

                window.location.href="{{ route('admin.users') }}";

                } else {
                    var errors = response.errors;

                    if (errors.name) {
                        $('#name').addClass('is-invalid')
                            .siblings('p')
                            .addClass('invalid-feedback')
                            .html(errors.name[0]);
                    } else {
                        $('#name').removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback')
                            .html("");
                    }

                    if (errors.email) {
                        $('#email').addClass('is-invalid')
                            .siblings('p')
                            .addClass('invalid-feedback')
                            .html(errors.email[0]);
                    } else {
                        $('#slug').removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback')
                            .html("");
                    }

                    if (errors.password) {
                        $('#password').addClass('is-invalid')
                            .siblings('p')
                            .addClass('invalid-feedback')
                            .html(errors.password[0]);
                    } else {
                        $('#password').removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback')
                            .html("");
                    }

                    if (errors.confirm_password) {
                        $('#confirm_password').addClass('is-invalid')
                            .siblings('p')
                            .addClass('invalid-feedback')
                            .html(errors.confirm_password[0]);
                    } else {
                        $('#confirm_password').removeClass('is-invalid')
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


</script>


@endsection