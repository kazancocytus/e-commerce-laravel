@extends('admin.layout.app')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">					
					<div class="container-fluid my-2">
						<div class="row mb-2">
							<div class="col-sm-6">
								<h1>Change Password</h1>
							</div>
							<div class="col-sm-6 text-right">
							</div>
						</div>
					</div>
					<!-- /.container-fluid -->
				</section>
				<!-- Main content -->
				<section class="content">
					<!-- Default box -->
                     @include('admin.message');
					<div class="container-fluid">
                        <form action="" method="post" id="changePasswordForm" name="changePasswordForm">
                        @csrf
						<div class="card">
							<div class="card-body">								
								<div class="row">
									<div class="col-md-6">
										<div class="mb-3">
											<label for="old_password">Old Password</label>
											<input type="password" name="old_password" id="old_password" class="form-control" placeholder="Old Password">	
                                            <p></p>
										</div>
									</div>
									<div class="col-md-6">
										<div class="mb-3">
											<label for="new_password">New Password</label>
											<input type="password" name="new_password" id="new_password"  class="form-control" placeholder="New Password">	
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
								</div>
							</div>							
						</div>
						<div class="pb-5 pt-3">
							<button type="submit" class="btn btn-primary">Save</button>
						</div>
                        </form>
					</div>
					<!-- /.card -->
				</section>

@endsection

@section('customJs')
<script>

    $("#changePasswordForm").submit(function(event){
        event.preventDefault();
        var element = $(this);
        $("button[type=submit]").prop('disabled',true);

        $.ajax({
            url: '{{ route("proccess-change-password-admin") }}',
            type: 'POST',
            data: element.serializeArray(),
            dataType: 'json',
            success: function(response){
                $("button[type=submit]").prop('disabled',false);
                if (response.status == true){

                window.location.href="{{ route('show-page-change-password') }}";

                } else {
                    var errors = response.errors;

                    if (errors.old_password) {
                        $('#old_password').addClass('is-invalid')
                            .siblings('p')
                            .addClass('invalid-feedback')
                            .html(errors.old_password[0]);
                    } else {
                        $('#old_password').removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback')
                            .html("");
                    }

                    if (errors.new_password) {
                        $('#new_password').addClass('is-invalid')
                            .siblings('p')
                            .addClass('invalid-feedback')
                            .html(errors.new_password[0]);
                    } else {
                        $('#new_password').removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback')
                            .html("");
                    }

                    if (errors.password_confirmation) {
                        $('#password_confirmation').addClass('is-invalid')
                            .siblings('p')
                            .addClass('invalid-feedback')
                            .html(errors.password_confirmation[0]);
                    } else {
                        $('#password_confirmation').removeClass('is-invalid')
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