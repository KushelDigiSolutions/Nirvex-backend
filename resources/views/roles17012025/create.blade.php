<x-layout bodyClass="g-sidenav-show  bg-gray-200">
@section('title')
        {{ 'Role Management' }}
    @endsection
    <x-navbars.sidebar activePage='dashboard'></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <x-navbars.navs.auth titlePage="Dashboard"></x-navbars.navs.auth>
        <div class="container-fluid py-4">
            <div class="row mb-4">
                <div class="col-lg-12 col-md-12 mb-md-0 mb-4">
                <div class="d-flex justify-content-between mb-2">
                            <div class="pull-left">
                                <h2>Role Management</h2>
                            </div>
                            <div class="pull-right">
                                @can('role-create')
                                    <a class="btn btn-success btn-sm mb-2" href="{{ route('roles.index') }}"><i class="fa fa-plus"></i>Create New Role</a>
                                @endcan
                            </div>
                        </div>

                        @session('success')
                            <div class="alert alert-success" role="alert"> 
                        {{ $value }}
                        </div>
                        @endsession

                        <form method="POST" action="{{ route('roles.store') }}">
                        @csrf
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group">
                                        <strong>Name:</strong>
                                            <input type="text" name="name" placeholder="Name" class="form-control">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group">
                                        <strong>Permission:</strong>
                                            <br/>
                                    @foreach($permission as $value)
                                    <label>
                                        <input type="checkbox" name="permission[{{$value->id}}]" value="{{$value->id}}" class="name">
                                    {{ $value->name }}</label>
                                    <br/>
                                    @endforeach
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                    <button type="submit" class="btn btn-primary btn-sm mb-3"><i class="fa-solid fa-floppy-disk"></i> Submit</button>
                </div>
            </div>
        </form>

                   
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