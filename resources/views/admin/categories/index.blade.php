<x-layout bodyClass="g-sidenav-show  bg-gray-200">
    @section('title')
        {{ 'Category' }}
    @endsection
    <x-navbars.sidebar activePage='dashboard'></x-navbars.sidebar>

   


    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <!-- Navbar -->
        <x-navbars.navs.auth titlePage="Dashboard"></x-navbars.navs.auth>
        <!-- End Navbar -->
        <div class="container-fluid py-4">
            <div class="row mb-4">
                <div class="col-lg-12 col-md-12 mb-md-0 mb-4">
                <div class="col-lg-12 margin-tb">
                            <div class="pull-right">
                                @can('role-create')
                                    <a class="btn btn-success btn-sm mb-2" href="{{ route('categories.create') }}"><i class="fa fa-plus"></i> Create New Category</a>
                                @endcan
                            </div>
                        </div>



                    





                    <div class="card mydatatable">
                        <div class="table-responsive">
                            <table id="datatable-basic" class="display nowrap" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>So.No.</th>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Created at</th>
                                        <th>Edit</th>
                                        <th>Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach ($category as $rs)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><img src="{{ url('/').'/'.$rs->image }}" class="avatar avatar-sm me-3"></td>
                                        <td>{{ $rs->name }}</td>
                                        <td>{{ $rs->created_at }}</td>
                                        <td><a href="{{ route('categories.edit', encrypt($rs->id)) }}" class="btn btn-sm btn-secondary"><i class="far fa-edit"></i></a></td>
                                        <td><form action="{{ route('categories.destroy', encrypt($rs->id)) }}"
                                    method="POST" onclick="confirm('Are you sure')">
                                    @method('DELETE')
                                    @csrf
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form></td>
                                    </tr>
                                @endforeach
                                    <!-- Add more rows as needed -->
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
                responsive: true, // Enable responsive behavior
                pageLength: 10,   // Default number of rows per page
                lengthMenu: [5, 10, 15, 20, 25], // Options for rows per page
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
