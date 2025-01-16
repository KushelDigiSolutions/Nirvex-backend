<x-layout bodyClass="g-sidenav-show  bg-gray-200">
@section('title')
        {{ 'Show Role' }}
    @endsection
    <x-navbars.sidebar activePage='roles'></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <x-navbars.navs.auth titlePage="Role"></x-navbars.navs.auth>
        <div class="container-fluid py-4" style="background-color:#fff">
            <div class="d-flex justify-content-between mb-2">
                <div class="pull-left">
                    <h2>Show Roles</h2>
                </div>
            </div>
            <div class="card">   
                <div class="card-body">   
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            <strong>Name:</strong>
                                {{ $role->name }}
                        </div>
                    </div> 
                       <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Permissions:</strong>
            @if(!empty($rolePermissions))
                @foreach($rolePermissions as $v)
                    <label class="label label-success">{{ $v->name }},</label>
                @endforeach
            @endif
        </div>
    </div>               
                </div>
            </div>
        </div>
            <x-footers.auth></x-footers.auth>
    </main>
    <x-plugins></x-plugins>
    </div>
    @push('js')
    <script src="{{ asset('assets') }}/js/chartjs.min.js"></script>
    @endpush
</x-layout>
