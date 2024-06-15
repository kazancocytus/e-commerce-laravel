@extends('admin.layout.app')


@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">					
					<div class="container-fluid my-2">
						<div class="row mb-2">
							<div class="col-sm-6">
								<h1>Order: #{{ $orders->id }}</h1>
							</div>
							<div class="col-sm-6 text-right">
                                <a href="{{ route('orders') }}" class="btn btn-primary">Back</a>
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
						<div class="row">
                            <div class="col-md-9">
                                <div class="card">
                                    <div class="card-header pt-3">
                                        <div class="row invoice-info">
                                            <div class="col-sm-4 invoice-col">
                                            <h1 class="h5 mb-3">Shipping Address</h1>
                                            <address>
                                                <strong>{{ $orders->first_name .' '.$orders->last_name }}</strong><br>
                                                {{ $orders->address }}<br>
                                                {{ $orders->city }}, {{ $orders->zip }} , {{ $orders->countryName }}<br>
                                                Phone: {{ $orders->mobile }}<br>
                                                Email: {{ $orders->email }}
                                            </address>
                                            <strong>Shipped Date : </strong>
                                            @if (!empty($orders->shipped_date))
                                                {{ \Carbon\Carbon::parse($orders->shipped_date)->format('d M, Y') }}
                                            @else
                                                N/A
                                            @endif
                                            </div>
                                            
                                            
                                            
                                            <div class="col-sm-4 invoice-col">
                                                <b>Invoice #007612</b><br>
                                                <br>
                                                <b>Order ID:</b> {{ $orders->id }}<br>
                                                <b>Total:</b> ${{ number_format($orders->grand_total,2) }}<br>
                                                <b>Status:</b>
                                                @if ($orders->status == 'pending')
                                                    <span class="text-warning">Pending</span>
                                                @elseif ($orders->status == 'shipped')
                                                    <span class="text-info">Shipped</span>
                                                @elseif ($orders->status == 'delivered')
                                                    <span class="text-success">Delivered</span>
                                                @else
                                                    <span class="text-danger">Cancelled</span>
                                                @endif
                                                <br>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body table-responsive p-3">								
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Product</th>
                                                    <th width="100">Price</th>
                                                    <th width="100">Qty</th>                                        
                                                    <th width="100">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if ($ordersItem->isNotEmpty())
                                                    @foreach ($ordersItem as $o)
                                                    <tr>
                                                        <td>{{ $o->name }}</td>
                                                        <td>${{ number_format($o->price,2) }}</td>                                        
                                                        <td>{{ $o->qty }}</td>
                                                        <td>${{ number_format($o->total,2) }}</td>
                                                    </tr>
                                                    @endforeach
                                                <tr>
                                                    <th colspan="3" class="text-right">Subtotal:</th>
                                                    <td>${{ number_format($orders->subtotal,2) }}</td>
                                                </tr>
                                                
                                                <tr>
                                                    <th colspan="3" class="text-right">Shipping:</th>
                                                    <td>${{ number_format($orders->shipping,2) }}</td>
                                                </tr>

                                                <tr>
                                                    <th colspan="3" class="text-right">Discount  {{ !empty($orders->coupon_code) ? '('.$orders->coupon_code.')' : '' }}:</th>
                                                    <td>${{ number_format($orders->discount,2) }}</td>
                                                </tr>
                                                <tr>
                                                    <th colspan="3" class="text-right">Grand Total:</th>
                                                    <td>${{ number_format($orders->grand_total,2) }}</td>
                                                </tr>
                                                @endif
                                            </tbody>
                                        </table>								
                                    </div>                            
                                </div>
                            </div>
                            <div class="col-md-3">
                                <form action="" method="post" id="changeOrderStatusForm" name="changeOrderStatusForm">
                                <div class="card">
                                    <div class="card-body">
                                        <h2 class="h4 mb-3">Order Status</h2>
                                        <div class="mb-3">
                                            <select name="status" id="status" class="form-control">
                                                <option value="pending" {{ $orders->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="shipped" {{ $orders->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                                                <option value="delivered" {{ $orders->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                                <option value="cancelled" {{ $orders->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="shipped_date">Shipped Date</label>
                                            <input type="text" placeholder="Shiped date" value="{{ $orders->shipped_date }}" name="shipped_date" id="shipped_date" class="form-control">
                                        </div>
                                        <div class="mb-3">
                                            <button type="submit" class="btn btn-primary">Update</button>
                                        </div>
                                    </div>
                                </div>
                                </form>
                                <div class="card">
                                    <div class="card-body">
                                        <form action="" method="post" name="sendInvoice" id="sendInvoice">
                                        <h2 class="h4 mb-3">Send Inovice Email</h2>
                                        <div class="mb-3">
                                            <select name="userType" id="userType" class="form-control">
                                                <option value="customer">Customer</option>                                                
                                                <option value="admin">Admin</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <button class="btn btn-primary">Send</button>
                                        </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
					</div>
					<!-- /.card -->
				</section>

@endsection

@section('customJs')

<script type="text/javascript">
    $(document).ready(function(){
        $("#shipped_date").datetimepicker({
            format:'Y-m-d H:i:s',
        })
    })

    $("#changeOrderStatusForm").submit(function(event) {
        event.preventDefault()

        $.ajax({
            url : '{{ route("update-orders",$orders->id) }}',
            type : 'post',
            data : $(this).serializeArray(),
            dataType : 'json',
            success : function(response) {

                window.location.href = "{{ route('detail-orders',$orders->id) }}"

            }
        })
    })

    $("#sendInvoice").submit(function(event) {
        event.preventDefault()

        if (confirm('are you sure you want to send this email? This will take time so please wait')) {
            $.ajax({
                url : '{{ route("send-invoice",$orders->id) }}',
                type : 'post',
                data : $(this).serializeArray(),
                dataType : 'json',
                success : function(response) {
    
                    window.location.href = "{{ route('detail-orders',$orders->id) }}"
    
                }
            })
        }
    })

</script>

@endsection