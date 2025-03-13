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
                                        <!-- <td id="seller-{{ $data['order_id'] }}">{{ $data['seller'] ?? 'Not Assigned' }}</td> -->
                                        <td>
                                            <button class="btn btn-primary select-seller" data-pincode="{{ $data['pincode'] }}">Select Seller</button>
                                        </td>
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


    <!-- Seller Selection Modal -->
     
    <!-- Seller Modal -->
    <div class="modal fade" id="sellerModal" tabindex="-1" aria-labelledby="sellerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content custom-modal p-4">
            <div class="modal-header text-center">
                <h5 class="modal-title w-100">Sellers from Pincode: <span id="pincodeValue">110011</span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="text" id="searchSeller" class="form-control search-input" placeholder="Find Seller by Name">
                <button id="searchButton" class="btn btn-primary w-100 mt-2">Search</button>
                
                <div id="sellerList" class="mt-3">
                    <div class="seller-list"></div>
                </div>
            </div>
        </div>
    </div>
</div>


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


        .custom-modal {
    border-radius: 20px;
    text-align: center;
}

.modal-header {
    border-bottom: none;
}

.search-input {
    border-radius: 10px;
    padding: 10px;
    border: 1px solid #ccc;
}

.seller-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #fff;
    padding: 15px;
    border: 1px solid #ddd;
    border-radius: 10px;
    margin-top: 10px;
}

.seller-card img {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    margin-right: 10px;
}

.seller-info {
    flex-grow: 1;
    text-align: left;
}

.select-btn {
    border-radius: 10px;
    padding: 5px 10px;
    background: #007bff;
    color: white;
    border: none;
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

        $('.status-dropdown').each(function () {
        $(this).data('prev', $(this).val()); 
    });

    $('.status-dropdown').change(function () {
        $(this).data('prev', $(this).val()); 
        let status = $(this).val();
        let orderId = $(this).data('id');
        let prevStatus = $(this).data('prev');

        if (status === "") {
            alert("Invalid status selected.");
            $(this).val(prevStatus);
            return;
        }

        let confirmUpdate = confirm("Are you sure you want to update the order status?");
        if (!confirmUpdate) {
            $(this).val(prevStatus);
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
                $(this).data('prev', status);
            }.bind(this),
            error: function (xhr) {
                console.log("AJAX Error:", xhr.responseText);
                alert("Error updating status. Please try again.");
                $(this).val(prevStatus); 
            }.bind(this)
        });
    });
});
</script>

<script>
    $(document).ready(function () {
    $('.select-seller').click(function () {
        let orderId = $(this).data('order-id');
        let pincode = $(this).data('pincode');

        $('#selectedOrderId').val(orderId);
        $('#sellerModal').modal('show');
    });
});
</script>

<script>
        function closeModal() {
            document.getElementById("modal").style.display = "none";
        }
    </script>


<script>
   
   $(document).ready(function () {
    function fetchSellers(pincode = '', name = '') {
        $.ajax({
            url: "{{ route('get-sellers') }}",
            type: "GET",
            data: { pincode: pincode, name: name },
            success: function (response) {
                let sellerList = "";
                if (response.length > 0) {
                    response.forEach(function (seller) {
                        sellerList += `
                            <div class="seller-card">
                                <div class="seller-info">
                                    <strong>${seller.first_name}</strong>
                                    <p>${seller.address}</p>
                                </div>
                                <button class="select-btn">Select</button>
                            </div>`;
                    });
                } else {
                    sellerList = "<p>No sellers found</p>";
                }

                $("#sellerModal .seller-list").html(sellerList);
            },
            error: function () {
                alert("Failed to fetch sellers");
            },
        });
    }

    $(".select-seller").on("click", function () {
        let pincode = $(this).data("pincode");
        fetchSellers(pincode);
        $("#sellerModal").modal("show");
    });

    $("#searchButton").on("click", function () {
        let sellerName = $("#searchSeller").val().trim();
        fetchSellers('', sellerName);
    });
});




//     $(document).ready(function () {
//     $(".select-seller").on("click", function () {
//         let pincode = $(this).data("pincode");

//         $.ajax({
//             url: "{{ route('get-sellers') }}",
//             type: "GET",
//             data: { pincode: pincode },
//             success: function (response) {
//                 let sellerList = "";
//                 if (response.length > 0) {
//                     response.forEach(function (seller) {
//                         sellerList += `
//                             <div class="seller-card">
//                                 <div class="seller-info">
//                                     <strong>${seller.first_name}</strong>
//                                     <p>${seller.address}</p>
//                                 </div>
//                                 <button class="select-btn">Select</button>
//                             </div>`;
//                     });
//                 } else {
//                     sellerList = "<p>No sellers found</p>";
//                 }

//                 $("#sellerModal .seller-list").html(sellerList);
//                 $("#sellerModal").modal("show");
//             },
//             error: function () {
//                 alert("Failed to fetch sellers");
//             },
//         });
//     });
// });

    
    
    
//     $(document).ready(function () {
//     $(".select-seller").on("click", function () {
//         let pincode = $(this).data("pincode");

//         $.ajax({
//             url: "{{ route('get-sellers') }}",
//             type: "GET",
//             data: { pincode: pincode },
//             success: function (response) {
//                 console.log(response);
//                 let sellerList = "";
//                 if (response.length > 0) {
//                     response.forEach(function (seller) {
//                         sellerList += `<div>${seller.first_name} (${seller.phone}) ${seller.address} ${seller.photo}</div>`;
//                     });
//                 } else {
//                     sellerList = "<li>No sellers found</li>";
//                 }

//                 $("#sellerModal .seller-list").html(sellerList);
//                 $("#sellerModal").modal("show");
//             },
//             error: function () {
//                 alert("Failed to fetch sellers");
//             },
//         });
//     });
// });
    </script>


    <script src="{{ asset('assets') }}/js/chartjs.min.js"></script>
    @endpush
</x-layout>
