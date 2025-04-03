<x-layout bodyClass="g-sidenav-show  bg-gray-200">
@section('title')
        {{ 'Show Order' }}
    @endsection
    <x-navbars.sidebar activePage='dashboard'></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <!-- Navbar -->
        <x-navbars.navs.auth titlePage="Dashboard"></x-navbars.navs.auth>
        <!-- End Navbar -->
        <div class="container-fluid py-4" style="background-color:#fff">
            <div class="row mb-4">
            <div class="col-lg-10 col-md-10 mb-md-0 mb-4"></div>
                <div class="col-lg-2 col-md-2 mb-md-0 mb-4">
                        <div class="col-lg-12 margin-tb">
                            <div class="pull-left">
                                @can('role-create')
                                    <a class="btn btn-success btn-sm mb-2" href="{{ route('orders.index') }}">Back to orders</a>
                                @endcan
                            </div>
                        </div>
                    </div>  
                    @if(session('success'))
                        <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @endif
                        <div class="row">
                            <div class="col">
                            <label for="name" class="form-label">Name:</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ ($orders->users->first_name ?? 'N/A') . ' ' . ($orders->users->last_name ?? 'N/A') }}" readOnly>
                            </div>
                            <div class="col">
                            <label for="status" class="form-label">Phone:</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="{{ $orders->users->phone ?? 'N/A' }}" readOnly>
                            </div>
                        </div>
                        <div class="row">
                        <div class="col">
                            <label for="status" class="form-label">Total Price:</label>
                            <input type="text" class="form-control" id="total_price" name="total_price" value="{{ $orders->total_price ?? 'N/A' }}" readOnly>
                            </div>
                            <div class="col">
                            <label for="total_tax" class="form-label">Total Tax:</label>
                            <input type="text" class="form-control" id="total_tax" name="total_tax" value="{{ $orders->total_tax ?? 'N/A' }}" readOnly>
                            </div>
                        </div>
                        <div class="row">
                        <div class="col">
                            <label for="sale_price" class="form-label">Sale Price:</label>
                            <input type="text" class="form-control" id="sale_price" name="sale_price" value="{{ $orders->sale_price ?? 'N/A' }}" readOnly>
                            </div>
                            <div class="col">
                            <label for="sale_tax" class="form-label">Sale Tax:</label>
                            <input type="text" class="form-control" id="sale_tax" name="sale_tax" value="{{ $orders->sale_tax ?? 'N/A' }}" readOnly>
                            </div>
                        </div>
                        
                        <div class="row">
                        <div class="col">
                            <label for="order_amount" class="form-label">Order Product Count:</label>
                            <input type="text" class="form-control" id="order_amount" name="order_amount" value="{{ $orders->order_amount ?? 'N/A' }}" readOnly>
                            </div>
                            <div class="col">
                            <label for="payment_id" class="form-label">Payment Id:</label>
                            <input type="text" class="form-control" id="payment_id" name="payment_id" value="{{ $orders->payment_id ?? 'N/A' }}" readOnly>
                            </div>
                        </div>
                        <div class="row">
                        <div class="col">
                            <label for="razor_order_id" class="form-label">PG Order Id:</label>
                            <input type="text" class="form-control" id="razor_order_id" name="razor_order_id" value="{{ $orders->razor_order_id ?? 'N/A' }}" readOnly>
                            </div>
                            <div class="col">
                            <label for="order_delevered_date" class="form-label">Order Delevered Date:</label>
                            <input type="text" class="form-control" id="order_delevered_date" name="order_delevered_date" value="{{ $orders->order_delevered_date ?? 'N/A' }}" readOnly>
                            </div>
                        </div>
                        <div class="row">
                        <div class="col">
                            <label for="created_at" class="form-label"> Order Created date:</label>
                            <input type="text" class="form-control" id="razor_order_id" name="created_at" value="{{ $orders->created_at ?? 'N/A' }}" readOnly>
                            </div>
                            <div class="col">
                            <label for="order_cancelled_date" class="form-label">Order Cancelled Date:</label>
                            <input type="text" class="form-control" id="order_cancelled_date" name="order_cancelled_date" value="{{ $orders->order_cancelled_date ?? 'N/A' }}" readOnly>
                            </div>
                        </div>
                        <!-- Order Items Section -->
        <div class="row mt-4">
            <h5>Order Items</h5>
            @if($orderItems->isEmpty())
                <p>No items found for this order.</p>
            @else
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product Name</th>
                            <th>Weight</th>
                            <th>Quantity</th>
                            <th>Sale Price</th>
                            <th>Total Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orderItems as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <!-- Product details from the relationship -->
                                <td>{{ $item->product->name ?? 'N/A' }}</td> 
                                <td>
                                @php
    $weight = optional($item->product)->weight;
    $weight_type = optional($item->product)->weight_type;

    if ($weight === null || $weight_type === null) {
        $converted = 'N/A';
        $unit = '';
    } else {
        if ($weight_type == 1) {
            $converted = $weight / 1000;
            $unit = 'KG';
        } elseif ($weight_type == 2) {
            $converted = $weight / 1000;
            $unit = 'L';
        } elseif ($weight_type == 3) {
            $converted = $weight;
            $unit = 'Packets';
        } else {
            $converted = 'N/A';
            $unit = '';
        }
    }
@endphp


                                {{ is_numeric($converted) ? number_format($converted, 1) : $converted }} {{ $unit }}




                                </td> 
                                <!-- Order item-specific details -->
                                <td>{{ $item->qty }}</td> 
                                <td>{{ Number::currency($item->sale_price  , 'INR', locale: 'en-IN') }}</td> 
                                <td>{{ Number::currency($item->total_price  , 'INR', locale: 'en-IN') }}</td> 
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <div class="row mt-4">
    <h5>Address Details</h5>
    <div class="col">
        <label for="address1" class="form-label">Address Line 1:</label>
        <input type="text" class="form-control" id="address1" name="address1" value="{{ $address['address1'] ?? 'N/A' }}" readOnly>
    </div>
    <div class="col">
        <label for="address2" class="form-label">Address Line 2:</label>
        <input type="text" class="form-control" id="address2" name="address2" value="{{ $address['address2'] ?? 'N/A' }}" readOnly>
    </div>
</div>

<div class="row mt-2">
    <div class="col">
        <label for="landmark" class="form-label">Landmark:</label>
        <input type="text" class="form-control" id="landmark" name="landmark" value="{{ $address['landmark'] ?? 'N/A' }}" readOnly>
    </div>
    <div class="col">
        <label for="city" class="form-label">City:</label>
        <input type="text" class="form-control" id="city" name="city" value="{{ $address['city'] ?? 'N/A' }}" readOnly>
    </div>
</div>

<div class="row mt-2">
    <div class="col">
        <label for="state" class="form-label">State:</label>
        <input type="text" class="form-control" id="state" name="state" value="{{ $address['state'] ?? 'N/A' }}" readOnly>
    </div>
    <div class="col">
        <label for="pincode" class="form-label">Pincode:</label>
        <input type="text" class="form-control" id="pincode" name="pincode" value="{{ $address['pincode'] ?? 'N/A' }}" readOnly>
    </div>
</div>

<div class="row mt-2">
    <div class="col">
        <label for="" class="">Address Type:</label>
        <input type="text" class="form-control" readOnly readonly value="{{ $address['address_type_label'] ?? 'N/A' }}">
    </div>
</div>

<div class="row mt-2">
    <div class="col">
        <label for="" class="">Invoice</label>
        <a class="btn btn-success btn-sm mb-2" href="" target="_blabk"> Invoice Download</a>
    </div>
</div>

                </div>
            </div>
            <x-footers.auth></x-footers.auth>
        </div>
    </main>
    <x-plugins></x-plugins>
    </div>
    @push('js')
    <script src="{{ asset('assets') }}/js/chartjs.min.js"></script>
    @endpush
</x-layout>
