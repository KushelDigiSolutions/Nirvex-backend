<x-layout bodyClass="g-sidenav-show  bg-gray-200">
@section('title')
    {{ 'Edit Product' }}
@endsection
<x-navbars.sidebar activePage='product'></x-navbars.sidebar>
<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
    <x-navbars.navs.auth titlePage="Product"></x-navbars.navs.auth>
    <div class="container-fluid py-4" style="background-color:#fff">
        <div class="row mb-4">
            <div class="col-lg-10 col-md-10 mb-md-0 mb-4"></div>
            <div class="col-lg-2 col-md-2 mb-md-0 mb-4">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-left">
                        @can('role-create')
                            <a class="btn btn-success btn-sm mb-2" href="{{ route('products.index') }}">Product</a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>

@if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
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
                            <option value="{{ $category->id }}" {{ $products->category && $products->category->id == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
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
                    <label for="description" class="form-label">Description:</label>
                    <textarea class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}" name="description" placeholder="description">{{ $products->description }}</textarea>
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
                            @foreach($products->image as $images)
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
                            @endforeach
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
            <div class="row mt-4">
                <div class="col">
                    <h5>Variant</h5>
                    <div id="dynamic-form">
                        @if ($products->variants && $products->variants->count())
                            @foreach ($products->variants as $index => $variant)
                                <div class="row mb-3">
                                    <div class="col">
                                        <label for="type" class="form-label">Type:</label>
                                        <select class="form-select" name="options[{{ $index }}][type]">
                                            <option value="1" {{ $variant->type == 1 ? 'selected' : '' }}>Quality</option>
                                            <option value="2" {{ $variant->type == 2 ? 'selected' : '' }}>Color</option>
                                            <option value="3" {{ $variant->type == 3 ? 'selected' : '' }}>Size</option>
                                        </select>
                                    </div>
                                    <div class="col">
                                        <label for="name" class="form-label">Name:</label>
                                        <input type="text" class="form-control" name="options[{{ $index }}][name]" value="{{ $variant->name }}" placeholder="Enter name">
                                    </div>
                                    <div class="col">
                                        <label for="image" class="form-label">Image:</label>
                                        <input type="file" class="form-control" name="options[{{ $index }}][image]">
                                        @if ($variant->images)
                                            <img src="{{ url('/').'/'. $variant->images }}" alt="Variant Image" class="img-thumbnail mt-2" width="100">
                                        @endif
                                    </div>
                                    <div class="col">
                                        <label for="description" class="form-label">Short Description:</label>
                                        <input type="text" class="form-control" name="options[{{ $index }}][short_description]" value="{{ $variant->short_description }}" placeholder="Short description">
                                    </div>
                                    <div class="col d-flex align-items-end">
                                        <button type="button" class="btn btn-danger btn-sm remove-field">Remove</button>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p>No variants available. Add new below.</p>
                        @endif
                    </div>
                    <button type="button" id="add-field" class="btn btn-primary btn-sm mt-2">Add Variant</button>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col">
                    <button type="submit" class="btn btn-primary btn-sm">Update</button>
                </div>
            </div>
        </form>
    </div>
</main>
<x-plugins></x-plugins>
</x-layout>

<script>
    let fieldCount = 1;

    document.getElementById('add-field').addEventListener('click', function () {
        const dynamicForm = document.getElementById('dynamic-form');
        const newField = `
            <div class="row mb-3">
                <div class="col">
                    <label for="type" class="form-label">Type:</label>
                    <select class="form-select" name="options[${fieldCount}][type]">
                        <option value="1">Quality</option>
                        <option value="2">Color</option>
                        <option value="3">Size</option>
                    </select>
                </div>
                <div class="col">
                    <label for="name" class="form-label">Name:</label>
                    <input type="text" class="form-control" name="options[${fieldCount}][name]" placeholder="Enter name">
                </div>
                <div class="col">
                    <label for="image" class="form-label">Image:</label>
                    <input type="file" class="form-control" name="options[${fieldCount}][image]">
                </div>
                <div class="col">
                    <label for="description" class="form-label">Short Description:</label>
                    <input type="text" class="form-control" name="options[${fieldCount}][short_description]" placeholder="Short description">
                </div>
                <div class="col d-flex align-items-end">
                    <button type="button" class="btn btn-danger btn-sm remove-field">Remove</button>
                </div>
            </div>
        `;
        dynamicForm.insertAdjacentHTML('beforeend', newField);
        fieldCount++;
    });

    document.getElementById('dynamic-form').addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-field')) {
            e.target.closest('.row').remove();
        }
    });
</script>
<script>
    document.getElementById('cat_id').addEventListener('change', function () {
        const catId = this.value; 
        const subCatDropdown = document.getElementById('sub_cat_id');
        subCatDropdown.innerHTML = '';
        const categories = @json($categories);
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