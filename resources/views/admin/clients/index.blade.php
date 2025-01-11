<x-layout bodyClass="g-sidenav-show  bg-gray-200">
    @section('title')
        {{ 'Clients List' }}
    @endsection
    <x-navbars.sidebar activePage='client'></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <x-navbars.navs.auth titlePage="client"></x-navbars.navs.auth>
        <div class="container-fluid py-4">
            <div class="row mb-4">
                <div class="col-lg-12 col-md-12 mb-md-0 mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <div class="pull-left">
                                <h2>Clients</h2>
                            </div>
                            <div class="pull-right">
                                @can('role-create')
                                    <a class="btn btn-success btn-sm mb-2" href=""><i class="fa fa-plus"></i>Clients</a>
                                @endcan
                            </div>
                        </div>
                    <div class="card mydatatable">
                        <div class="table-responsive">
                            <table id="datatable-basic" class="display nowrap" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Roles</th>
                                        <th width="280px">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $key => $user)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $user->first_name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>@if(!empty($user->getRoleNames()))
                                            @foreach($user->getRoleNames() as $v)
                                                <label class="badge bg-success">{{ $v }}</label>
                                            @endforeach
                                            @endif
                                        </td>
                                        <td>
                                            <a class="btn btn-info btn-sm" href="{{ route('users.show',$user->id) }}"><i class="fa-solid fa-list"></i> Show</a>
                                            <a class="btn btn-primary btn-sm" href="{{ route('users.edit',$user->id) }}"><i class="fa-solid fa-pen-to-square"></i> Edit</a>
                                            <form method="POST" action="{{ route('users.destroy', $user->id) }}" style="display:inline">
                                            @csrf
                                            @method('DELETE')
                                              <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i> Delete</button>
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
    <script src="{{ asset('assets') }}/js/chartjs.min.js"></script>
    @endpush
</x-layout>
