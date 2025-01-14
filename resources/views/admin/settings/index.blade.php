<x-layout bodyClass="g-sidenav-show  bg-gray-200">
    @section('title')
        {{ 'Setting' }}
    @endsection
    <x-navbars.sidebar activePage='dashboard'></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <x-navbars.navs.auth titlePage="Setting"></x-navbars.navs.auth>
        <div class="container-fluid py-4">
            <div class="row mb-4">
                <div class="col-lg-12 col-md-12 mb-md-0 mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <div class="pull-left">
                                <h2>Setting</h2>
                            </div>
                          
                        </div>
                    <div class="card mydatatable">
                        <div class="table-responsive">
                           <!--  <table id="datatable-basic" class="display nowrap" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>So.No.</th>
                                        <th>Setting Name</th>
                                        <th>Image</th>
                                        <th>Created at</th>
                                        <th>Status</th>
                                        <th>Edit</th>
                                        <th>Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                
                                    <tr>
                                        <td>1.</td>
                                        <td>10000</td>
                                        <td><img src="{{ url('/').'/'.('/uploads/products/1736022006_drake.jpg') }}" class="avatar avatar-sm me-3"></td>
                                        <td>2025-01-04 07:17:05</td>
                                        <td>Active</td>
                                        <td><a href="" class="btn btn-sm btn-secondary"><i class="far fa-edit"></i></a></td>
                                        <td>
                                            <form action=""method="POST" onclick="confirm('Are you sure')">
                                            @method('DELETE')
                                            @csrf
                                                <button type="submit" class="btn btn-danger"><i class="fas fa-trash-alt"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                </tbody>
                            </table> -->
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
