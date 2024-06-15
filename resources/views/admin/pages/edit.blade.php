@extends('admin.layout.app')

@section('content')
<section class="content-header">					
					<div class="container-fluid my-2">
						<div class="row mb-2">
							<div class="col-sm-6">
								<h1>Create Page</h1>
							</div>
							<div class="col-sm-6 text-right">
								<a href="{{ route('page') }}" class="btn btn-primary">Back</a>
							</div>
						</div>
					</div>
					<!-- /.container-fluid -->
				</section>
				<!-- Main content -->
				<section class="content">
					<!-- Default box -->
					<div class="container-fluid">
                    <form action="" method="post" name="editPageForm" id="editPageForm">	
                        <input type="hidden" id="id" name="id" value="{{ $pages->id }}">						
						<div class="card">
							<div class="card-body">	
								<div class="row">
									<div class="col-md-6">
										<div class="mb-3">
											<label for="name">Name</label>
											<input type="text" name="name" id="name" class="form-control" placeholder="Name" value="{{ $pages->name }}">	
                                            <p></p>
										</div>
									</div>
									<div class="col-md-6">
										<div class="mb-3">
											<label for="slug">Slug</label>
											<input type="text" name="slug" id="slug" readonly class="form-control" placeholder="Slug" value="{{ $pages->slug }}">	
                                            <p></p>
										</div>
									</div>	
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="content">Content</label>
                                            <textarea name="content" id="content" class="summernote" cols="30" rows="10">{{ $pages->content }}</textarea>
                                        </div>								
                                    </div> 
                                    <div class="col-md-6">
										<div class="mb-3">
											<label for="showHome">Show on Home</label>
											<select name="showHome" id="showHome" class="form-control">
                                                <option {{ $pages->showHome == 'Yes' ? 'selected' : '' }} value="Yes">Yes</option>
                                                <option {{ $pages->showHome == 'No' ? 'selected' : '' }} value="No">No</option>
                                            </select>	
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

<script type="text/javascript">
    $("#editPageForm").submit(function(event) {
        event.preventDefault()

        $.ajax({
            url : '{{ route("update-page") }}',
            type : 'post',
            data : $(this).serializeArray(),
            dataType : 'json',
            success : function(response) {
                if (response.status == true) {
                    window.location.href="{{ route('page') }}"
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

                    if (errors.slug) {
                        $('#slug').addClass('is-invalid')
                            .siblings('p')
                            .addClass('invalid-feedback')
                            .html(errors.slug[0]);
                    } else {
                        $('#slug').removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback')
                            .html("");
                    }
                }
            }
        })
    })

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