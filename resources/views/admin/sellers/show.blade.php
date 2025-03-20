<x-layout bodyClass="g-sidenav-show bg-gray-200">
    @section('title')
        {{ 'Seller Details' }}
    @endsection
    <x-navbars.sidebar activePage='seller'></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="Seller Details"></x-navbars.navs.auth>
        <div class="container-fluid py-4" style="background-color:#fff">
            <div class="row mb-4">
                <div class="col-lg-10 col-md-10 mb-md-0 mb-4"></div>
                <div class="col-lg-2 col-md-2 mb-md-0 mb-4">
                    <div class="pull-right">
                        <a class="btn btn-success btn-sm mb-2" href="{{ route('sellers.index') }}">Back to Seller</a>
                        <input type="button" class="btn btn-success btn-sm mb-2" id="updateInfo" value="Update the Seller" />
                    </div>
                </div>  
                
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- User Basic Information -->
                <div class="row">
                    <div class="col-md-4 text-center">
                        <img src="{{ $user->image ? asset($user->image) : asset('assets/img/demo_profile.png') }}" 
                             class="img-fluid rounded-circle" 
                             style="width: 200px; height: 200px; object-fit: cover;"
                             alt="User Image">
                    </div>
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col">
                                <label class="form-label">First Name:</label>
                                <input type="text" id="firstName" class="form-control" value="{{ $user->first_name }}" >
                            </div>
                            <div class="col">
                                <label class="form-label">Last Name:</label>
                                <input type="text" id="lastName" class="form-control" value="{{ $user->last_name ?? 'N/A' }}" >
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col">
                                <label class="form-label">Email:</label>
                                <input type="text" id="email" class="form-control" value="{{ $user->email }}" >
                            </div>
                            <div class="col">
                                <label class="form-label">Phone:</label>
                                <input type="text" id="phone" class="form-control" value="{{ $user->phone }}" >
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col">
                                <label class="form-label">Email Verified:</label>
                                <input type="text" class="form-control" 
                                       value="{{ $user->email_verified_at ? 'Yes' : 'No' }}" readonly>
                            </div>
                            <div class="col">
                                <label class="form-label">Seller Status:</label>
                                <select class="form-select seller-active-dropdown" data-id="{{ $user->id }}">
                                    <option value="0" {{ $user->seller_active == 0 ? 'selected' : '' }}>Not Active</option>
                                    <option value="1" {{ $user->seller_active == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="2" {{ $user->seller_active == 2 ? 'selected' : '' }}>Temporary Disabled</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
    <div class="row mt-4">
        <h4>Seller Prices</h4>
        <div class="card mydatatable">
            <div class="table-responsive">
                <table id="datatable-basic" class="display nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Download CSV</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>2025-03-20</td>
                            <td><button class="btn btn-success btn-sm mb-2" onclick="downloadCSV(1)">Download CSV</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
</div>

    </div>
                    
<div class="row mt-4">
    <h4>Address Details <button type="button" style="float:right" class="btn-success btn-sm mb-2" data-bs-toggle="modal" data-bs-target="#addressModal">Add New Address</button></h4>
    @if($user->addresses && $user->addresses->isNotEmpty())
        @foreach($user->addresses as $address)
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-8">
                            <h5 class="card-title">
                                {{ $address->name }}
                                <span class="badge bg-{{ $address->status ? 'success' : 'secondary' }}">
                                    {{ $address->status ? 'Active' : 'Inactive' }}
                                </span>
                            </h5>
                        </div>
                        <div class="col-4 text-end">
                            <span class="badge bg-info">
                                {{ $address->address_type == 0 ? 'Home' : 'Office' }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="row mt-2">
                        <div class="col-12">
                            <p class="mb-1">
                                {{ $address->address1 }}<br>
                                {{ $address->address2 }}<br>
                                @if($address->landmark)
                                    Landmark: {{ $address->landmark }}<br>
                                @endif
                                {{ $address->city }}, {{ $address->state }}<br>
                                PIN: {{ $address->pincode }}
                            </p>
                            <p class="mb-0">
                                <strong>Contact:</strong> {{ $address->phone }}
                            </p>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col">
                            <button class="btn btn-warning btn-sm edit-address" data-id="{{ $address->id }}">Edit</button>
                            <button class="btn btn-info btn-sm delete-address" data-id="{{ $address->id }}">Delete</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    @else
        <div class="col-12">
            <div class="alert alert-info">
                No addresses found for this user
            </div>
        </div>
    @endif
</div>

            </div>

            <x-footers.auth></x-footers.auth>
        </div>
    </main>


    <!-- Add/Edit Address Modal -->
<div class="modal fade" id="addressModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="addressForm" method="POST">
                @csrf
                <input type="hidden" name="user_id" value="{{ $user->id }}">
                <input type="hidden" name="id" id="address_id">
                
                <div class="modal-header">
                    <h5 class="modal-title" id="addressModalLabel">Add New Address</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Name*</label>
                            <input type="text" name="name" id="mname" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone*</label>
                            <input type="text" name="phone" id="mphone" class="form-control" required maxlength="12">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Address Line 1*</label>
                            <input type="text" name="address1" id="maddress1" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Address Line 2</label>
                            <input type="text" name="address2" id="maddress2" class="form-control">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Landmark</label>
                            <input type="text" name="landmark" id="mlandmark" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Pincode*</label>
                            <input type="text" name="pincode" id="mpincode" class="form-control" required maxlength="10">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">City*</label>
                            <input type="text" name="city" id="mcity" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">State*</label>
                            <input type="text" name="state" id="mstate" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Address Type*</label>
                            <select name="address_type" id="maddress_type" class="form-select" required>
                                <option value="0">Home</option>
                                <option value="1">Office</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Status*</label>
                            <select name="status" id="mstatus" class="form-select" required>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Set as Default</label>
                            <select name="is_default" id="mis_default" class="form-select">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Address</button>
                </div>
            </form>
        </div>
    </div>
</div>


    <!-- Add/Edit Address Modal -->
    <div class="modal fade" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="updateForm" method="PUT">
                @csrf
                @method('PUT') 
                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                    <input type="hidden" name="id" id="address_id">
                    
                    <div class="modal-header">
                        <h5 class="modal-title" id="addressModalLabel">Update Address</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Name*</label>
                                <input type="text" name="name" id="uname" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone*</label>
                                <input type="text" name="phone" id="uphone" class="form-control" required maxlength="12">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Address Line 1*</label>
                                <input type="text" name="address1" id="uaddress1" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Address Line 2</label>
                                <input type="text" name="address2" id="uaddress2" class="form-control">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Landmark</label>
                                <input type="text" name="landmark" id="ulandmark" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Pincode*</label>
                                <input type="text" name="pincode" id="upincode" class="form-control" required maxlength="10">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">City*</label>
                                <input type="text" name="city" id="ucity" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">State*</label>
                                <input type="text" name="state" id="ustate" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Address Type*</label>
                                <select name="address_type" id="uaddress_type" class="form-select" required>
                                    <option value="0">Home</option>
                                    <option value="1">Office</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Status*</label>
                                <select name="status" id="ustatus" class="form-select" required>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Set as Default</label>
                                <select name="is_default" id="uis_default" class="form-select">
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update Address</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <style>
        .mydatatable .dataTables_length, .mydatatable .dataTables_filter {
            padding: 20px;
        }
        .mydatatable .dataTables_info {
            padding: 20px 10px;
        }
        .mydatatable .dataTables_paginate {
            padding: 10px;
        }
    </style>
    @push('js')
    <script>
        $(document).ready(function() {
            $('#addressForm').on('submit', function(e) {
                e.preventDefault();
                
                let formData = new FormData(this);
                
                $.ajax({
                    url: "{{ route('addresses.store') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $('#addressModal').modal('hide');
                        alert('Address added successfully!');
                        window.location.reload(); // This will reload the current page
                    },
                    error: function(xhr) {
                        alert('Error adding address. Please try again.');
                        console.log(xhr.responseText);
                    }
                });
            });
        });



        $(document).ready(function() {
    // Handle edit button click
    $('.edit-address').on('click', function() {
        const addressId = $(this).data('id');
        
        // Fetch address details
        $.ajax({
            url: `{{url('/')}}/admin/addresses/${addressId}/edit`,
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(data) {
                // Populate form fields
                $('#address_id').val(data.id);
                $('#uname').val(data.name);
                $('#uphone').val(data.phone);
                $('#uaddress1').val(data.address1);
                $('#uaddress2').val(data.address2);
                $('#ulandmark').val(data.landmark);
                $('#upincode').val(data.pincode);
                $('#ucity').val(data.city);
                $('#ustate').val(data.state);
                $('#uaddress_type').val(data.address_type);
                $('#ustatus').val(data.status);
                $('#uis_default').val(data.is_default);
                // Show the modal
                $('#updateModal').modal('show');
            },
            error: function(xhr) {
                alert('Error fetching address details');
            }
        });
    });

    // Handle form submission
    $('#updateForm').on('submit', function(e) {
        e.preventDefault();
        const addressId = $('#address_id').val();
        const formData = $(this).serialize();

        $.ajax({
            url: `{{url('/')}}/admin/addresses/${addressId}`,
            type: 'PUT',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-HTTP-Method-Override': 'PUT'
            },
            success: function(response) {
                $('#updateModal').modal('hide');
                alert('Address updated successfully!');
                window.location.reload(); // Reload to show updated data
            },
            error: function(xhr) {
                alert('Error updating address. Please try again.');
            }
        });
    });
});

$('.delete-address').on('click', function() {
    const addressId = $(this).data('id');
    const customerId = "{{ $user->id }}"; // Get customer ID from the view
    
    if (confirm('Are you sure you want to delete this address?')) {
        $.ajax({
            url: `{{url('/')}}/admin/addresses/${addressId}`,
            type: 'DELETE',
            data: {
                customer_id: customerId // Pass customer_id to controller
            },
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                alert('Address deleted successfully!');
                window.location.reload();
            },
            error: function(xhr) {
                alert('Error deleting address. Please try again.');
            }
        });
    }
});



        // Seller Active Dropdown Script
        $('.seller-active-dropdown').on('change', function() {
            let userId = $(this).data('id');
            let sellerActive = $(this).val();

            if (confirm("Are you sure you want to update this user's status?")) {
                $.ajax({
                    url: "{{ route('sellers.updateSellerActive') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: userId,
                        seller_active: sellerActive
                    },
                    success: function(response) {
                        alert(response.message);
                    },
                    error: function(xhr) {
                        alert("Error updating status");
                        $(this).val($(this).data('previous-value'));
                    }
                });
            } else {
                $(this).val($(this).data('previous-value'));
            }
        });

        $('#updateInfo').on('click', function () {
    if (confirm("Are you sure you want to update this user details?")) {
        // Get values from input fields
        let firstName = $("#firstName").val();
        let lastName = $("#lastName").val();
        let email = $("#email").val();
        let phone = $("#phone").val();
        let shop_name = $("#shop_name").val();
        let shop_owner_name = $("#shop_owner").val();
        let gst_number = $("#gst_number").val(); // Define gst_number
        let pan_number = $("#pan_number").val(); // Define pan_number


        // Make AJAX request
        $.ajax({
            url: "{{ route('sellers.updateSellerDetails') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                id: {{ $user->id }}, // Ensure $user->id is available in your blade template
                first_name: firstName,
                last_name: lastName,
                email: email,
                phone: phone,
                shop_name: shop_name,
                shop_owner_name: shop_owner_name,
                gst_number: gst_number, // Pass gst_number
                pan_number: pan_number, // Pass pan_number
            },
            success: function (response) {
                alert(response.message);
            },
            error: function (xhr) {
                alert("Error updating status");
            }
        });
    }
});


$(document).ready(function() {
    // Trigger file input on image click
    $('#shopImage').on('click', function() {
        $('#shopImageUploader').click();
    });

    // Handle file selection and send via AJAX
    $('#shopImageUploader').on('change', function(event) {
        var file = event.target.files[0];
        if (file) {
            var formData = new FormData();
            formData.append('shop_image', file);

            // Add user_id to the FormData
            var userId = "{{ $user->id }}"; // Replace this with the actual user ID from your backend
            formData.append('user_id', userId);

            $.ajax({
                url: "{{ route('sellers.updateCustomerShopImage') }}", // Laravel route
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}' // CSRF token for security
                },
                success: function(response) {
                    alert('Image updated successfully!');
                    // Update image preview
                    $('#shopImageSrc').attr('src', URL.createObjectURL(file));
                },
                error: function(xhr) {
                    alert('Failed to update image. Please try again.');
                }
            });
        }
    });
});
$(document).ready(function() {
        // Initialize DataTable
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

function downloadCSV(id) {
        // Example logic for downloading a CSV file
        const csvContent = `ID,Date\n${id},2025-03-${20 - id}`;
        const blob = new Blob([csvContent], { type: 'text/csv' });
        const url = URL.createObjectURL(blob);

        const link = document.createElement('a');
        link.href = url;
        link.download = `data_${id}.csv`;
        link.click();

        URL.revokeObjectURL(url);
    }
    </script>
    @endpush
</x-layout>
<style>
    .userDataImgs .col-md-3{
        position: relative;
    display: flex;
    flex-direction: column;
    text-align: center;
    padding: 10px;
    }
    .userDataImgs .col-md-3 img{
        height:250px;
    }
    </style>