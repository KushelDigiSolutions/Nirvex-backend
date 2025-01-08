<x-layout bodyClass="g-sidenav-show  bg-gray-200">
@section('title')
        {{ 'Edit Service' }}
    @endsection
<x-navbars.sidebar activePage='service'></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <!-- Navbar -->
        <x-navbars.navs.auth titlePage="Service"></x-navbars.navs.auth>
        <!-- End Navbar -->
        <div class="container-fluid py-4" style="background-color:#fff">
            <div class="row mb-4">
            <div class="col-lg-10 col-md-10 mb-md-0 mb-4"></div>
                <div class="col-lg-2 col-md-2 mb-md-0 mb-4">
                        <div class="col-lg-12 margin-tb">
                            <div class="pull-left">
                                @can('role-create')
                                    <a class="btn btn-success btn-sm mb-2" href="{{ route('services.index') }}">Service</a>
                                @endcan
                            </div>
                        </div>
                    </div>  
                    @if(session('success'))
                        <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @endif
                    <form action="{{ route('services.update', $services->id) }}" method="post" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col">
                            <label for="name" class="form-label">Service Name:</label>
                                <input type="text" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" id="name" placeholder="Enter Service" name="name" value="{{ $services->name }}">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>                        
                        <div class="row">
                            <div class="col">
                            <label for="status" class="form-label">Status:</label>
                            <select class="form-select form-select-lg" name="status">
                                <option value="" {{ $services->status === null ? 'selected' : '' }}>Select status</option>
                                <option value="1" {{ $services->status == 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ $services->status == 0 ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            </div>
                        </div>
                        <div class="row">
                        <div class="col mt-3">
                        <label class="form-label">Existing Images:</label>
                            <div class="row">
                                <div class="col-md-3">
                <img src="{{ url('/').'/'.$services->image }}" alt="Service Image" class="img-thumbnail" style="max-width: 100%; height: auto;">
            </div>
            </div>
                        </div>
                        <div class="col">
                            <label for="image" class="form-label">Service Images:</label>
                                <input type="file" class="form-control @error('image.*') is-invalid @enderror" name="image">
                                @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        </div>
                        <div class="col">
                        <button type="submit" class="btn btn-primary btn-sm mt-2 mb-3">Update</button>
                        </div>
                    </form>                
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