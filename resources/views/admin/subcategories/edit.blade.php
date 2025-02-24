<x-layout bodyClass="g-sidenav-show  bg-gray-200">
@section('title')
        {{ 'Edit Sub Category' }}
    @endsection
<x-navbars.sidebar activePage='dashboard'></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <!-- Navbar -->
        <x-navbars.navs.auth titlePage="Dashboard"></x-navbars.navs.auth>
        <!-- End Navbar -->
        <div class="container-fluid py-4" style="background-color:#fff">
        <div class="d-flex justify-content-between mb-2">
                            <div class="pull-left">
                                <h2>Edit Sub Category</h2>
                            </div>
                        </div>
                    <div class="card">   
                        <div class="card-body">  
                    @if(session('success'))
                        <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @endif
                    <form action="{{ route('subcategories.update', $SubCategory->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col">
                            <label for="category" class="form-label">Sub Category:</label>
                                <input type="text" class="form-control" id="name" placeholder="Enter Sub category" name="name"
                                value="{{ old('name', $SubCategory->name) }}">
                            </div>
                            <div class="col">
                            <label for="status" class="form-label">Status:</label>
                            <select class="form-select" name="status">
                                <option value="" {{ $SubCategory->status === null ? 'selected' : '' }}>Select status</option>
                                <option value="1" {{ $SubCategory->status == 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ $SubCategory->status == 0 ? 'selected' : '' }}>Inactive</option>
                            </select>
                            </div>
                        </div>
                        <div class="row">
                        <div class="col">
                        <label for="category" class="form-label">Category:</label>
                            <select class="form-select" name="cat_id">
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ $category->id == $category->cat_id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                            </select>
                        </div>
                        </div>
                        <div class="row">
                        <div class="col">
                            <label for="image" class="form-label">Sub Category Images:</label>
                            @if (!empty($SubCategory->image)) 
                            <div class="mb-3">
                                <img src="{{ url('/').'/'.$SubCategory->image }}" alt="Current Category Image" style="max-height: 150px;">
                            </div>
                            @else
                                <p>No image available</p>
                            @endif
                               <input type="file" class="form-control" name="image" />
                        </div>
                        </div>
                        <div class="col">
                        <button type="submit" class="btn btn-primary btn-sm mt-2 mb-3">Update</button>
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
