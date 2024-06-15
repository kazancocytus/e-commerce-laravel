@extends('admin.layout.app')


@section('content')
<section class="content-header">					
					<div class="container-fluid my-2">
						<div class="row mb-2">
							<div class="col-sm-6">
								<h1>Orders</h1>
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
					<div class="container-fluid">
                    @include('admin.message')
						<div class="card">
                        <form action="" method="get">
							<div class="card-header">
                                <div class="card-title">
                                    <button type="button" onclick="window.location.href='{{ route('orders') }}'" class="btn btn-default btn-sm">Reset</button>
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
							</div>
							<div class="card-body table-responsive p-0">								
								<table class="table table-hover text-nowrap">
									<thead>
										<tr>
											<th>Orders #</th>											
                                            <th>Customer</th>
                                            <th>Email</th>
                                            <th>Phone</th>
											<th>Status</th>
                                            <th>Total</th>
                                            <th>Date Purchased</th>
										</tr>
									</thead>
									<tbody>
                                        @if ($orders->isNotEmpty())
                                            @foreach ($orders as $o)
                                                
										<tr>
											<td><a href="{{ route('detail-orders',$o->id) }}">{{ $o->id }}</a></td>
											<td>{{ $o->name }}</td>
                                            <td>{{ $o->email }}</td>
                                            <td>{{ $o->mobile }}</td>
                                            <td>
                                                @if ($o->status == 'pending')
                                                <span class="badge bg-warning">Pending</span>
                                                @elseif ($o->status == 'shipped')
                                                <span class="badge bg-info">Shipped</span>
                                                @else
                                                <span class="badge bg-success">Delivered</span>
                                                @endif
                                                </td>
                                            <td>${{ number_format($o->grand_total,2) }}</td>
                                            <td>{{ \Carbon\Carbon::parse($o->created_at)->format('d M, Y') }}</td>																				
										</tr>
                                        @endforeach
                                        @else
                                        <tr>
                                            <td colspan="3">Record not Found</td>
                                        </tr>
                                        @endif
									</tbody>
								</table>										
							</div>
							<div class="card-footer clearfix">
								<ul class="pagination pagination m-0 float-right">
								  <li class="page-item"><a class="page-link" href="#">«</a></li>
								  <li class="page-item"><a class="page-link" href="#">1</a></li>
								  <li class="page-item"><a class="page-link" href="#">2</a></li>
								  <li class="page-item"><a class="page-link" href="#">3</a></li>
								  <li class="page-item"><a class="page-link" href="#">»</a></li>
								</ul>
							</div>
						</div>
					</div>
					<!-- /.card -->
				</section>
                @endsection