<x-layout bodyClass="g-sidenav-show  bg-gray-200">
@section('title')
        {{ 'Sub Category' }}
    @endsection
<x-navbars.sidebar activePage='Sub Category'></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <x-navbars.navs.auth titlePage="Sub Category"></x-navbars.navs.auth>
        <div class="container-fluid py-4">
            <div class="row mb-4">
                <div class="col-lg-12 col-md-12 mb-md-0 mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <div class="pull-left">
                            <h2>Sub Category</h2>
                        </div>
                            <div class="pull-right">
                                @can('role-create')
                                    <a class="btn btn-success btn-sm mb-2" href="{{ route('subcategories.create') }}"><i class="fa fa-plus"></i> Create New Sub Category</a>
                                @endcan
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
                                        <th>Sr.No</th>
                                        <th>Images</th>
                                        <th>Name</th>
                                        <th>Category </th>
                                        <th>Status</th>
                                        <th>Edit</th>
                                        <th>Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($subcategories as $rs)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><img src="{{ url('/').'/'.$rs->image }}" class="avatar avatar-sm me-3"></td>
                                        <td>{{ $rs->name }}</td>
                                        <td>{{ $rs->category->name ?? 'No Category' }}</td>
                                        @if($rs->status === 1)
                                        <td>Active</td>
                                        @else
                                        <td>Inactive</td>
                                        @endif
                                        <td><a href="{{ route('subcategories.edit', encrypt($rs->id)) }}" class="btn btn-sm btn-secondary"> <i class="far fa-edit"></i></a></td>
                                        <td>
                                            <form action="{{ route('subcategories.destroy', encrypt($rs->id)) }}"
                                        method="POST" onsubmit="return confirmDelete(event)">
                                        @method('DELETE')
                                        @csrf
                                            <button type="submit" class="btn btn-danger">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
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
<script>
    function confirmDelete(event) {
        if (!confirm('Are you sure?')) {
            event.preventDefault(); 
            return false;
        }
        return true;
    }
</script>
    <script src="{{ asset('assets') }}/js/chartjs.min.js"></script>
    @endpush
</x-layout>
