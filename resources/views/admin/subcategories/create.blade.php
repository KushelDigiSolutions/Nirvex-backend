<x-layout bodyClass="g-sidenav-show  bg-gray-200">
@section('title')
        {{ 'Create Sub Category' }}
    @endsection
<x-navbars.sidebar activePage='dashboard'></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <!-- Navbar -->
        <x-navbars.navs.auth titlePage="Dashboard"></x-navbars.navs.auth>
        <!-- End Navbar -->
        <div class="container-fluid py-4" style="background-color:#fff">
            <div class="row mb-4">
            <div class="col-lg-10 col-md-10 mb-md-0 mb-4"></div>
                <div class="col-lg-2 col-md-2 mb-md-0 mb-4">
                        <div class="col-lg-12 margin-tb">
                            <div class="pull-left">
                                @can('role-create')
                                    <a class="btn btn-success btn-sm mb-2" href="{{ route('subcategories.index') }}">Sub Category</a>
                                @endcan
                            </div>
                        </div>
                    </div>  
                    @if(session('success'))
                        <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @endif
                    <form action="{{ route('subcategories.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col">
                            <label for="category" class="form-label">Category:</label>
                                <input type="text" class="form-control" id="name" placeholder="Enter category" name="name">
                            </div>
                            <div class="col">
                            <label for="status" class="form-label">Status:</label>
                            <select class="form-select form-select-lg" name="status">
                                <option value="">Select status</option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                            </div>
                        </div>
                        <div class="row">
                        <div class="col">
                        <label for="category" class="form-label">Category:</label>
                            <select class="form-select form-select-lg" name="cat_id">
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            <label for="image" class="form-label">Category Images:</label>
                                <input type="file" class="form-control" name="image" />
                            </div>
                        </div>
                        <div class="col">
                        <button type="submit" class="btn btn-primary btn-sm mt-2 mb-3">Submit</button>
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
