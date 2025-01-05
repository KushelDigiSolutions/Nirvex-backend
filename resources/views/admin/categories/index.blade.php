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
                    <div class="card">
                        <div class="col-lg-12 margin-tb">
                            <div class="pull-left">
                                @can('role-create')
                                    <a class="btn btn-success btn-sm mb-2" href="{{ route('categories.create') }}"><i class="fa fa-plus"></i> Create New Category</a>
                                @endcan
                            </div>
                        </div>
                    
                    </div>
                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th>Sr.No</th>
                                            <th>Category Name</th>
                                            <th>Images</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @forelse ($category as $rs)
                                    <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $rs->name }}</td>
                                    <td><img src="{{ url('/').'/'.$rs->image }}" height="100" width="100"></td>
                                    <td>
                                <a href="{{ route('categories.edit', encrypt($rs->id)) }}"
                                    class="btn btn-sm btn-secondary">
                                    <i class="far fa-edit"></i>
                                </a>
                            </td>
                            <td>
                                <form action="{{ route('categories.destroy', encrypt($rs->id)) }}"
                                    method="POST" onclick="confirm('Are you sure')">
                                    @method('DELETE')
                                    @csrf
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center bg-danger">Permission not created</td>
                        </tr>
                    @endforelse
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
    @push('js')
    <script src="{{ asset('assets') }}/js/chartjs.min.js"></script>
    @endpush
</x-layout>
