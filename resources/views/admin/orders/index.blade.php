<x-layout bodyClass="g-sidenav-show  bg-gray-200">
    @section('title')
        {{ 'Orders' }}
    @endsection
    <x-navbars.sidebar activePage='dashboard'></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <x-navbars.navs.auth titlePage="Orders"></x-navbars.navs.auth>
        <div class="container-fluid py-4">
            <div class="row mb-4">
                <div class="col-lg-12 col-md-12 mb-md-0 mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <div class="pull-left">
                                <h2>Orders</h2>
                            </div>
                            
                        </div>
                    <div class="card mydatatable">
                        <div class="table-responsive">
                            <table id="datatable-basic" class="display nowrap" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>So.No.</th>
                                        <th>Order Id</th>
                                        <th>Customer</th>
                                        <th>Pincode</th>
                                        <th>Seller</th>
                                        <th>Count</th>
                                        <th>Total Amount</th>
                                        <th>Ordered</th>
                                        <th>Status</th>
                                        <th>View</th>
                                        <th>Download Invoice</th>
                                    </tr>
                                </thead>
                                <tbody>
                                
                                @foreach($totalOrders as $data)
                                    <tr>
                                        <td>{{ $data['so_no'] }}</td>
                                        <td>{{ $data['order_id'] }}</td>
                                        <td>{{ $data['customer'] }}</td>
                                        <td>{{ $data['pincode'] }}</td>
                                        <td>{{ $data['seller'] }}</td>
                                        <td>{{ $data['count'] }}</td>
                                        <td>{{ $data['total_amount'] }}</td>
                                        <td>{{ $data['ordered'] }}</td>
                                        <td>  
                                            <select class="status-dropdown" data-id="{{ $data['order_id'] }}">
                                               <option value="0" {{ $data['status'] == 0 ? 'selected' : '' }}>Created</option>
                                                <option value="1" {{ $data['status'] == 1 ? 'selected' : '' }}>Payment Done</option>
                                                <option value="2" {{ $data['status'] == 2 ? 'selected' : '' }}>Order Accepted</option>
                                                <option value="3" {{ $data['status'] == 3 ? 'selected' : '' }}>Order Preparing</option>
                                                <option value="4" {{ $data['status'] == 4 ? 'selected' : '' }}>Order Shipped</option>
                                                <option value="5" {{ $data['status'] == 5 ? 'selected' : '' }}>Order Delivered</option>
                                                <option value="6" {{ $data['status'] == 6 ? 'selected' : '' }}>Order Completed</option>
                                                <option value="7" {{ $data['status'] == 7 ? 'selected' : '' }}>Order Rejected</option>
                                                <option value="8" {{ $data['status'] == 8 ? 'selected' : '' }}>Order Returned</option>
                                                <option value="9" {{ $data['status'] == 9 ? 'selected' : '' }}>Order Cancelled</option>
                                            </select>
                                            <div id="status-message" style="display: none; color: green; margin-top: 10px;"></div>
                                        </td>
                                        <td><a href="{{ route('orders.show', encrypt($data['order_id'])) }}" class="btn btn-sm btn-secondary"><i class="far fa-eye"></i></a></td>
                                        <td><a class="btn btn-success btn-sm mb-2" href="" target="_blabk">Download</a></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>



                    </div>
                </div>
            </div>
            <x-footers.auth></x-footers.auth>
        </div>
    </main>
    <x-plugins></x-plugins>
    </div>

    <style>
        .mydatatable .dataTables_length,.mydatatable .dataTables_filter{
            padding:20px;
        }
        .mydatatable .dataTables_info{
            padding: 20px 10px;
        }
        .mydatatable .dataTables_paginate {
            padding: 10px;
        }
    </style>


    @push('js')
    <script>
        $(document).ready(function() {
            $('#datatable-basic').DataTable({
                responsive: true, 
                pageLength: 10,   
                lengthMenu: [5, 10, 15, 20, 25], 
                language: {
                    lengthMenu: "Show _MENU_ entries per page",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries"
                }
            });
        });
    </script>

<script>
    $(document).ready(function () {
    $('.status-dropdown').change(function () {
        let status = $(this).val();
        let orderId = $(this).data('id');

        if (status === "") {
            alert("Invalid status selected.");
            return;
        }
        console.log("Updating Order ID:", orderId, "to Status:", status); 
        $.ajax({
            url: "{{ route('update.order.status') }}",
            type: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                order_id: orderId,
                order_status: status 
            },
            success: function (response) {
                console.log("Success response:", response);
                alert(response.message);
            },
            error: function (xhr) {
                console.log("AJAX Error:", xhr.responseText);
                alert("Error updating status. Please try again.");
            }
        });
    });
});

</script>


    <script src="{{ asset('assets') }}/js/chartjs.min.js"></script>
    @endpush
</x-layout>
