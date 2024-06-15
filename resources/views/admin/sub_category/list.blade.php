@extends('admin.layout.app')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">					
					<div class="container-fluid my-2">
						<div class="row mb-2">
							<div class="col-sm-6">
								<h1>Sub Category</h1>
							</div>
							<div class="col-sm-6 text-right">
								<a href="{{ route('sub-categories.create') }}" class="btn btn-primary">New Sub Category</a>
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
						<div class="card">
                            <form action="" method="get">
							<div class="card-header">
                            <div class="card-title">
                                    <button type="button" onclick="window.location.href='{{ route("sub-categories.index") }}'" class="btn btn-default btn-sm">Reset</button>
                                </div>
								<div class="card-tools">
                                    <div class="input-group input-group" style="width: 250px;">
										<input type="text" value="{{ Request::get('keyword') }}" name="keyword" id="keyword" class="form-control float-right" placeholder="Search">
					
										<div class="input-group-append">
										  <button type="submit" class="btn btn-default">
											<i class="fas fa-search"></i>
										  </button>
										</div>
									  </div>
								</div>
							</div>
                            </form>
							<div class="card-body table-responsive p-0">								
								<table class="table table-hover text-nowrap">
									<thead>
										<tr>
											<th width="60">ID</th>
											<th>Name</th>
                                            <th>Category</th>
											<th>Slug</th>
											<th width="100">Status</th>
											<th width="100">Action</th>
										</tr>
									</thead>
									<tbody>
                                    @if ($subCategories->isNotEmpty())
                                        @foreach ($subCategories as $subCategory)
                                            
										<tr>
                                            <td>{{ $subCategory->id }}</td>
											<td>{{ $subCategory->name }}</td>
											<td>{{ $subCategory->categoryName }}</td>
											<td>{{ $subCategory->slug }}</td>
											<td>
                                                @if ($subCategory->status == 1)
												<svg class="text-success-500 h-6 w-6 text-success" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
													<path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
												</svg>
                                                @elseif ($subCategory->status == 0)

                                                <svg class="text-danger-500 h-6 w-6 text-danger" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
													<path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
												</svg>

                                                @endif
											</td>
											<td>
												<a href="{{ route('sub-categories.edit',$subCategory->id) }}">
													<svg class="filament-link-icon w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
														<path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
													</svg>
												</a>
												<a href="" onclick="deleteSubCategory({{ $subCategory->id }})" class="text-danger w-4 h-4 mr-1">
													<svg wire:loading.remove.delay="" wire:target="" class="filament-link-icon w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
														<path	ath fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
												  	</svg>
												</a>
											</td>
										</tr>
                                        @endforeach
                                    @else

                                    <tr>
                                        <td colspan="5">Records not found</td>
                                    </tr>

                                    @endif
									</tbody>
								</table>										
							</div>
							<div class="card-footer clearfix">
                                {!! $subCategories->appends(request()->query())->links() !!}
							</div>
						</div>
					</div>
					<!-- /.card -->
				</section>

@endsection

@section('customJs')

<script>
	function deleteSubCategory(id) 
{
    var url = '{{ route("sub-categories.delete", ":id") }}';
    var newUrl = url.replace(':id', id);

    if (confirm("Are you sure you want to delete this subcategory?")) {
        $.ajax({
            url: newUrl,
            type: 'DELETE',
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.status) {
                    window.location.href = "{{ route('sub-categories.index') }}";
                } else {
                    alert('Failed to delete category.');
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