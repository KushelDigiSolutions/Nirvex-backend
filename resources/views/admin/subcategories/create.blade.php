<x-layout bodyClass="g-sidenav-show  bg-gray-200">
@section('title')
        {{ 'Create Sub Category' }}
    @endsection
<x-navbars.sidebar activePage='sub category'></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <x-navbars.navs.auth titlePage="Sub Category"></x-navbars.navs.auth>
        <div class="container-fluid py-4" style="background-color:#fff">
        <div class="d-flex justify-content-between mb-2">
                            <div class="pull-left">
                                <h2>Create New Sub Category</h2>
                            </div>
                        </div>
                    <div class="card">   
                        <div class="card-body">  
                    @if(session('success'))
                        <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @endif
                    <form action="{{ route('subcategories.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col">
                            <label for="name" class="form-label">Sub Category:</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" placeholder="Enter Sub category" name="name">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col">
                            <label for="status" class="form-label">Status:</label>
                            <select class="form-select @error('status') is-invalid @enderror" name="status">
                                <option value="">Select status</option>
                                <option value="1" {{ old('status') == "1" ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('status') == "0" ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                        <div class="col">
                        <label for="category" class="form-label">Category:</label>
                            <select class="form-select @error('category') is-invalid @enderror" name="cat_id">
                                @if(empty($categories))
                                <option value="">Select status</option>
                                @else
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                                @endif
                            </select>
                            @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col">
                            <label for="image" class="form-label">Sub Category Images:</label>
                                <input type="file" class="form-control @error('image') is-invalid @enderror" name="image" />
                            @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            </div>
                        </div>
                        <div class="col">
                        <button type="submit" class="btn btn-primary btn-sm mt-2 mb-3">Submit</button>
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
