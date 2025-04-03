<x-layout bodyClass="g-sidenav-show bg-gray-200">
    @section('title')
        {{ 'Sellers' }}
    @endsection
    <x-navbars.sidebar activePage='sellers'></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="sellers"></x-navbars.navs.auth>
        <div class="container-fluid py-4">
            <div class="row mb-4">
                <div class="col-lg-12 col-md-12 mb-md-0 mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <div class="pull-left">
                            <h2>Seller Management</h2>
                        </div>
                        <div class="pull-right">
                            
                                <a class="btn btn-success btn-sm mb-2" href="{{ route('sellers.create') }}">
                                    <i class="fa fa-plus"></i> Create New Seller
                                </a>
                        </div>
                    </div>
                    @if(session('success'))
                        <div class="alert alert-success">
                    {{ session('success') }}
                        </div>
                    @endif
                    <div class="card mydatatable">
                        <div class="table-responsive">
                            <table id="datatable-basic" class="display nowrap" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Pincode</th>
                                        <th>Seller Status</th>
                                        <th>View</th>
                                        <th>Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @if(isset($data) && count($data) > 0)
                                    @foreach ($data as $key => $user)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $user->first_name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>
                                            {{ $user->pincode }}
                                            </td>
                                            
                                            <!-- <td>{{ $user->created_at }}</td> -->
                                            <td>
                                                <select class="form-select form-select-sm seller-active-dropdown" data-id="{{ $user->id }}">
                                                    <option value="0" {{ $user->seller_active == 0 ? 'selected' : '' }}>Not Active</option>
                                                    <option value="1" {{ $user->seller_active == 1 ? 'selected' : '' }}>Active</option>
                                                    <option value="2" {{ $user->seller_active == 2 ? 'selected' : '' }}>Temporarily Disabled</option>
                                                </select>
                                            </td>
                                            <td>
                                                <a href="{{ route('sellers.show', encrypt($user->id)) }}" 
                                                   class="btn btn-sm btn-secondary"><i class="far fa-eye"></i>
                                                </a>
                                            </td>
                                        <td>
                                            <form action="{{ route('sellers.destroy', $user->id) }}"
                                            method="POST" onclick="confirm('Are you sure')">
                                            @method('DELETE')
                                            @csrf
                                                <button type="submit" class="btn btn-danger" style=".btn display: inline-block !important;">
                                                <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @else
                                    <tr>
                                        <td colspan="4"> No Seller Found</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Footer -->
            <x-footers.auth></x-footers.auth>

        </div>
    </main>

    <!-- Plugins -->
    <x-plugins></x-plugins>
    <div class="modal fade" id="changeSO" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Change SO</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            <div class="search-dropdown">
                <div class="dropdown-display" 
                    id="dropdownDisplay">Select Sales Officer</div>
                <div class="dropdown-content"
                    id="dropdownContent">
                    <input type="text" 
                        class="search-input" 
                        id="selectedSO"
                        placeholder="Select SO">
                    <input type="hidden" 
                        class="search-input" 
                        id="selectedCustomer">
                    <ul id="dropdownList">
                        <li>No SO</li>
                    </ul>
                </div>
            </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary"  onclick="updateCustomerSO()">Update SO</button>
            </div>
            </div>
        </div>
        </div>
    <!-- DataTable Custom Styles -->
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

<style>
      

        .search-dropdown {
            width: 300px;
            margin: 20px auto;
            position: relative;
        }

        .dropdown-display {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .dropdown-content {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: #fff;
            z-index: 1000;
            display: none;
            max-height: 200px;
            overflow-y: auto;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        .search-input {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: none;
            border-bottom: 1px solid #ddd;
            box-sizing: border-box;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .dropdown-content ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        .dropdown-content ul li {
            padding: 10px;
            cursor: pointer;
        }

        .dropdown-content ul li:hover {
            background-color: #f1f1f1;
        }
    </style>
 <script>
        $(document).ready(function () {
            $('#dropdownDisplay').on('click', function () {
                $('#dropdownContent').toggle();
            });

            $('#selectedSO').on('input', function () {
                let value = $(this).val().toLowerCase();
                $('#dropdownList li').filter(function () {
                    $(this).toggle($(this).text()
                           .toLowerCase().indexOf(value) > -1);
                });
            });

            $('#dropdownList').on('click', 'li', function () {
                $('#dropdownDisplay').text($(this).text());
                $("#selectedSO").val($(this).attr("data-id"));
                $('#dropdownContent').hide();
            });

            $(document).on('click', function (e) {
                if (!$(e.target).closest('.search-dropdown').length) {
                    $('#dropdownContent').hide();
                }
            });
        });
    </script>
    <!-- DataTable Script & AJAX for Seller Active Update -->
    @push('js')
    <script src="{{ asset('assets') }}/js/chartjs.min.js"></script>

    <!-- DataTable Initialization -->
    <script>
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

        // Event delegation for dynamically rendered rows
        $(document).on('change', '.seller-active-dropdown', function() {
            let userId = $(this).data('id');
            let sellerActive = $(this).val();
            let updateUrl = "{{ route('sellers.updateSellerActive') }}"; 
            // Confirmation Box
            if (confirm("Are you sure you want to update this user's seller active status?")) {
                $.ajax({
                    url: updateUrl, 
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: userId,
                        seller_active: sellerActive
                    },
                    success: function(response) {
                        alert(response.message); // Show success message
                    },
                    error: function(xhr) {
                        alert("An error occurred while updating the status.");
                    }
                });
            } else {
                // Reset dropdown to previous value if canceled
                $(this).val($(this).data('original-value'));
            }
        });
    });

    function changeSo(id){
        $("#selectedCustomer").val(id);
        $.ajax({
            url: "", // Route for updating status
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {

                $('#dropdownList').empty();
                
                // Loop through the data array and append new list items
                response.data.forEach(item => {
                    const listItem = `<li data-id="${item.id}">${item.first_name} / ${item.email}</li>`;
                    $('#dropdownList').append(listItem);
                });
            },
            error: function(xhr) {
                alert("An error occurred while updating the status.");
            }
        });
        $("#changeSO").modal('show');
    }

    function updateCustomerSO(){
       const csId = $("#selectedCustomer").val();
       const soId = $("#selectedSO").val();

       $.ajax({
            url: "", // Route for updating status
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                cs_id: csId,
                so_id: soId
            },
            success: function(response) {

                $('#dropdownList').empty();
                location.reload();
                // Loop through the data array and append new list items
                response.data.forEach(item => {
                    const listItem = `<li data-id="${item.id}">${item.first_name} / ${item.email}</li>`;
                    $('#dropdownList').append(listItem);
                });
            },
            error: function(xhr) {
                alert("An error occurred while updating the status.");
            }
        });
    }
</script>


    @endpush
</x-layout>
