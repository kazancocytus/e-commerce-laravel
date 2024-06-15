@extends('admin.layout.app')

@section('content')

<!-- Content Header (Page header) -->
<!-- Content Header (Page header) -->
<section class="content-header">					
					<div class="container-fluid my-2">
						<div class="row mb-2">
							<div class="col-sm-6">
								<h1>Edit Sub Category</h1>
							</div>
							<div class="col-sm-6 text-right">
								<a href="{{ route('sub-categories.index') }}" class="btn btn-primary">Back</a>
							</div>
						</div>
					</div>
					<!-- /.container-fluid -->
				</section>
				<!-- Main content -->
				<section class="content">
					<!-- Default box -->
					<div class="container-fluid">
                        <form action="" name="subCategoryForm" id="subCategoryForm">
                        @csrf
                        @method('PUT')
						<div class="card">
							<div class="card-body">								
								<div class="row">
                                    <div class="col-md-12">
										<div class="mb-3">
											<label for="name">Category</label>
											<select name="category" id="category" class="form-control">
                                                <option value="" id="category">Select Category</option>
                                                @if ($categories->isNotEmpty())
                                                @foreach ($categories as $category)
                                                    <option {{ ($subCategories->categories_id == $category->id) ? 'selected' : '' }} value="{{ $category->id }}">{{ $category->name }}</option>
                                                @endforeach                                                    
                                                @endif
                                            </select>
                                            <p></p>
										</div>
									</div>
									<div class="col-md-6">
										<div class="mb-3">
											<label for="name">Name</label>
											<input type="text" name="name" id="name" class="form-control" placeholder="Name" value="{{ $subCategories->name }}">	
                                            <p></p>
										</div>
									</div>
									<div class="col-md-6">
										<div class="mb-3">
											<label for="slug">Slug</label>
											<input type="text" name="slug" id="slug" readonly class="form-control" placeholder="Slug" value="{{ $subCategories->slug }}">	
                                            <p></p>
										</div>
									</div>	
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="status">Status</label>
                                            <select name="status" id="status" class="form-control">
                                                <option {{ $subCategories->status == 1 ? 'selected' : '' }} value="1">Active</option>
                                                <option {{ $subCategories->status == 0 ? 'selected' : '' }} value="0">Block</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
										<div class="mb-3">
											<label for="showHome">Show Home</label>
											<select name="showHome" id="showHome" class="form-control">
                                                <option {{ $subCategories->showHome == 'Yes' ? 'selected' : '' }} value="Yes">Yes</option>
                                                <option {{ $subCategories->showHome == 'No' ? 'selected' : '' }} value="No">No</option>
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

<script>

$("#subCategoryForm").submit(function(event){
        event.preventDefault();

        var element = $("#subCategoryForm");
        $("button[type=submit]").prop('disabled',true);

        $.ajax({
            url: '{{ route("sub-categories.update",$subCategories->id) }}',
            type: 'put',
            data: element.serializeArray(),
            dataType: 'json',
            success: function(response){
                $("button[type=submit]").prop('disabled',false);
                if (response.status == true){

                window.location.href="{{ route('sub-categories.index') }}";

                $('#name').removeClass('is-invalid')
                    .siblings('p')
                    .removeClass('invalid-feedback').html("");

                $('#slug').removeClass('is-invalid')
                    .siblings('p')
                    .removeClass('invalid-feedback').html("");
                
                $('#category').removeClass('is-invalid')
                    .siblings('p')
                    .removeClass('invalid-feedback').html("");

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

                    if (errors.category) {
                        $('#category').addClass('is-invalid')
                            .siblings('p')
                            .addClass('invalid-feedback')
                            .html(errors.category[0]);
                    } else {
                        $('#category').removeClass('is-invalid')
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