<x-layout bodyClass="g-sidenav-show  bg-gray-200">
@section('title')
        {{ 'Create Category' }}
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
                                    <a class="btn btn-success btn-sm mb-2" href="{{ route('products.index') }}">Category</a>
                                @endcan
                            </div>
                        </div>
                    </div>  
                    @if(session('success'))
                        <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @endif
                    <form action="{{ route('products.update', $products->id) }}" method="post" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col">
                            <label for="name" class="form-label">Product Name:</label>
                                <input type="text" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" id="name" placeholder="Enter Product" name="name" value="{{ $products->name }}">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col">
                            <label for="mrp" class="form-label">MRP (RS.):</label>
                            <input type="text" class="form-control {{ $errors->has('mrp') ? 'is-invalid' : '' }}" id="mrp" placeholder="Enter MRP" name="mrp" value="{{ $products->mrp }}">
                            @error('mrp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                            <label for="cat_id" class="form-label">Category:</label>
                            <select class="form-select form-select-lg @error('cat_id') is-invalid @enderror" name="cat_id" id="cat_id">
                                <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ $products->category && $products->category->id == $category->id ? 'selected' : '' }}>{{ $category->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('cat_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            </div>
                            <div class="col">
                            <label for="sub_cat_id" class="form-label">Sub Category:</label>
                            <select class="form-select form-select-lg @error('sub_cat_id') is-invalid @enderror" name="sub_cat_id" id="sub_cat_id">
                            <option value="">Select Sub Category</option>
                            @foreach($subCategories as $subCategory)
                                <option value="{{ $subCategory->id }}" {{ $products->subCategory && $products->subCategory->id == $subCategory->id ? 'selected' : '' }}>
                                    {{ $subCategory->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('sub_cat_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                            </div>
                        </div>
                        <div class="row">
                        <div class="col">
                            <label for="description" class="form-label">Discription:</label>
                            <textarea class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}" name="description" placeholder="discription">{{ $products->description }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        </div>
                        
                        <div class="row">
                        <div class="col">
                            <label for="specification" class="form-label">Specifications:</label>
                            <textarea class="form-control {{ $errors->has('specification') ? 'is-invalid' : '' }}" name="specification" placeholder="Specifications">{{ $products->specification }}</textarea>
                            @error('specification')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        </div>
                        <div class="row">
                            <div class="col">
                            <label for="delivery" class="form-label">Delivery Days:</label>
                                <input type="text" class="form-control {{ $errors->has('delivery') ? 'is-invalid' : '' }}" id="delivery" placeholder="Enter Delivery Days" name="availability" value="{{ $products->availability }}">
                            @error('delivery')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            </div>
                            <div class="col">
                            <label for="status" class="form-label">Status:</label>
                            <select class="form-select form-select-lg" name="status">
                                <option value="" {{ $products->status === null ? 'selected' : '' }}>Select status</option>
                                <option value="1" {{ $products->status == 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ $products->status == 0 ? 'selected' : '' }}>Inactive</option>
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
                            @if($products && is_array($products->image))
        @forelse($products->image as $images)
            <div class="col-md-3">
                <img src="{{ url('/').'/'.$images }}" alt="Product Image" class="img-thumbnail" style="max-width: 100%; height: auto;">
                <form action="{{ route('product.image.delete') }}" method="POST" class="mt-2">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="product_id" value="{{ $products->id }}">
                    <input type="hidden" name="image_path" value="{{ $images }}">
                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                </form>
            </div>
        @empty
            <p>No images uploaded for this product.</p>
        @endforelse
    @else
        <p>No images available.</p>
    @endif

                            </div>
                        </div>
                        <div class="col">
                            <label for="image" class="form-label">Category Images:</label>
                                <input type="file" class="form-control @error('image.*') is-invalid @enderror" name="image[]" multiple>
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

<script>
    document.getElementById('cat_id').addEventListener('change', function () {
        const catId = this.value; 
        const subCatDropdown = document.getElementById('sub_cat_id');
        subCatDropdown.innerHTML = '';
        const categories = @json($products);
        const selectedCategory = categories.find(cat => cat.id == catId);
        if (selectedCategory && selectedCategory.subcategories.length > 0) {
            selectedCategory.subcategories.forEach(subCat => {
            const option = document.createElement('option');
            option.value = subCat.id;
            option.textContent = subCat.name;
            subCatDropdown.appendChild(option);
        });
    } else {
        const option = document.createElement('option');
        option.textContent = 'No subcategories available';
        subCatDropdown.appendChild(option);
    }
    });
</script>
