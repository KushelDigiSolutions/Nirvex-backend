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
                                        <td>{{ $data['status'] }}</td>
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
    <script src="{{ asset('assets') }}/js/chartjs.min.js"></script>
    @endpush
</x-layout>
