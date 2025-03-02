<x-layout bodyClass="g-sidenav-show  bg-gray-200">
@section('title')
        {{ 'Service' }}
    @endsection
    <x-navbars.sidebar activePage='service'></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <x-navbars.navs.auth titlePage="Service"></x-navbars.navs.auth>
        <div class="container-fluid py-4">
            <div class="row mb-4">
                <div class="col-lg-12 col-md-12 mb-md-0 mb-4">
                <div class="d-flex justify-content-between mb-2">
                            <div class="pull-left">
                                <h2>Service</h2>
                            </div>
                            <div class="pull-right">
                                @can('role-create')
                                    <a class="btn btn-success btn-sm mb-2" href="{{ route('services.create') }}"><i class="fa fa-plus"></i> Create New Service</a>
                                @endcan
                            </div>
                        </div>
                    <div class="card mydatatable">
                        <div class="table-responsive">
                            <table id="datatable-basic" class="display nowrap" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Sr.No</th>
                                            <th>Images</th>
                                            <th>Service Name</th>
                                            <th>Type</th>
                                            <th>Status</th>
                                            <th>Edit</th>
                                            <th>Delete</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($services as $rs)
                                    <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                    @php
                                        $images = explode(',', $rs->image); 
                                    @endphp
                                    @if (!empty($images[0]))  
                                        <img src="{{ asset(trim($images[0])) }}" class="avatar avatar-sm me-3" style="width:50px; height:50px;">
                                    @endif
                                    </td>
                                    <td>{{ $rs->name }}</td>
                                    <td>
                                         @if($rs->type === 1) 
                                            Service
                                        @else  
                                            Property
                                        @endif</td>
                                
                                   
                                    @if($rs->status === 1)
                                        <td>Active</td>
                                        @else
                                        <td>Inactive</td>
                                        @endif
                                    <td>
                                <a href="{{ route('services.edit', encrypt($rs->id)) }}"
                                    class="btn btn-sm btn-secondary">
                                    <i class="far fa-edit"></i>
                                </a>
                            </td>
                            <td>
                                <form action="{{ route('services.destroy', encrypt($rs->id)) }}"
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