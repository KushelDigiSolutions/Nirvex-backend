<x-layout bodyClass="g-sidenav-show  bg-gray-200">
    @section('title')
        {{ 'Pricing' }}
    @endsection
    <x-navbars.sidebar activePage='dashboard'></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <x-navbars.navs.auth titlePage="Pricing"></x-navbars.navs.auth>
        <div class="container-fluid py-4">
            <div class="row mb-4">
                <div class="col-lg-12 col-md-12 mb-md-0 mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <div class="pull-left">
                                <h2>Pricing</h2>
                            </div>
                            <div class="pull-right">
                                @can('role-create')
                                <a class="btn btn-success btn-sm mb-2" href="{{ route('pricings.create') }}" class="btn btn-primary mb-3"><i class="fa fa-plus"></i> Create New Pricings</a>
                                    <!-- <a class="btn btn-success btn-sm mb-2" href=""><i class="fa fa-plus"></i> Upload Bulk Pricings</a> -->
                                @endcan
                            </div>
                        </div>
                    <div class="card mydatatable">
                        <!-- <div class="table-responsive">
                            <table id="datatable-basic" class="display nowrap" style="width:100%"> -->
                            <div class="table-responsive" style="overflow-x: auto; white-space: nowrap;">
    <table id="datatable-basic" class="display nowrap" style="width:100%">

                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Pincode</th>
                                    <th>Product ID</th>
                                    <th>Product Name</th>
                                    <th>SKU</th>
                                    <th>MRP</th>
                                    <th>Price</th>
                                    <th>Tax Type</th>
                                    <th>Tax Value</th>
                                    <th>Shipping Charges</th>
                                    <th>Valid Upto</th>
                                    <th>Status</th>
                                    <th>Cash</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($pricings as $pricing)
                                <tr>
                                    <td>{{ $pricing->id }}</td>
                                    <td>{{ $pricing->pincode }}</td>
                                    <td>{{ $pricing->product_id }}</td>
                                    <td>{{ $pricing->product->name ?? 'N/A' }}</td>
                                    <td>{{ $pricing->product_sku_id }}</td>
                                    <td>{{ $pricing->mrp }}</td>
                                    <td>{{ $pricing->price }}</td>
                                    <td>{{ $pricing->tax_type == 0 ? 'Percentage' : 'Flat' }}</td>
                                    <td>{{ $pricing->tax_value }}</td>
                                    <td>{{ $pricing->ship_charges }}</td>
                                    <td>{{ $pricing->valid_upto }}</td>
                                    <td>{{ $pricing->status ? 'Active' : 'Inactive' }}</td>
                                    <td>{{ $pricing->is_cash ? 'Yes' : 'No' }}</td>
                                    <td>
                                        <a href="{{ route('pricings.edit', encrypt($pricing->id)) }}" class="btn btn-warning btn-sm">Edit</a>
                                        <form action="{{ route('pricings.destroy', encrypt($pricing->id)) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                    </td>
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
 


        #datatable-basic_wrapper {
    overflow-x: auto;
    width: 100%;
}

#datatable-basic {
    width: 100% !important;
    border-collapse: collapse; /* Ensures borders align correctly */
    white-space: nowrap;
}

#datatable-basic th, 
#datatable-basic td {
    border: 1px solid #dee2e6; /* Bootstrap border color */
    text-align: center;
    vertical-align: middle;
}

.table-responsive {
    overflow-x: auto;
    min-width: 100%;
}



    </style>


    @push('js')
    <script>
$(document).ready(function() {
    $('#datatable-basic').DataTable({
        scrollX: true,  
        scrollY: "400px", 
        scrollCollapse: true, 
        paging: false, 
        fixedHeader: true,
        language: {
            lengthMenu: "Show _MENU_ entries per page",
            info: "Showing _START_ to _END_ of _TOTAL_ entries"
        }
    });
});

    </script>

<script>

    <script src="{{ asset('assets') }}/js/chartjs.min.js"></script>
    @endpush
</x-layout>
