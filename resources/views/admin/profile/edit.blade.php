<x-layout bodyClass="g-sidenav-show  bg-gray-200">
@section('title')
        {{ 'Update Profile' }}
    @endsection
<x-navbars.sidebar activePage='profile'></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <x-navbars.navs.auth titlePage="Service"></x-navbars.navs.auth>
            <div class="container-fluid py-4" style="background-color:#fff">
                <div class="d-flex justify-content-between mb-2">
                    <div class="pull-left">
                        <h2>Update Profile</h2>
                    </div>
                </div>
                <div class="card">   
                    <div class="card-body">   
                        @if(session('success'))
                            <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                        @endif
                                  
                </div>
            </div>
            </div>
            <x-footers.auth></x-footers.auth>
    </main>
    <x-plugins></x-plugins>
    @push('js')
    <script src="{{ asset('assets') }}/js/chartjs.min.js"></script>
    @endpush
</x-layout>
