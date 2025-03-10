<x-layout bodyClass="g-sidenav-show  bg-gray-200">
@section('title')
    {{ 'Edit Product' }}
@endsection
<x-navbars.sidebar activePage='product'></x-navbars.sidebar>
<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <x-navbars.navs.auth titlePage="Product"></x-navbars.navs.auth>
    <div class="container-fluid py-4" style="background-color:#fff">
        <div class="d-flex justify-content-between mb-2">
            <div class="pull-left">
                <h2>Edit Product</h2>
            </div>
        </div>
        <div class="card">   
            <div class="card-body">  
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
            <form action="{{ route('products.update', $product->id) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <!-- Product Name & MRP -->
            <div class="row">
                <div class="col">
                    <label for="name" class="form-label">Product Name:</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" placeholder="Enter Product" name="name" value="{{ old('name', $product->name) }}">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col">
                    <label for="mrp" class="form-label">MRP (RS.):</label>
                    <input type="text" class="form-control @error('mrp') is-invalid @enderror" id="mrp" placeholder="Enter MRP" name="mrp" value="{{ old('mrp', $product->mrp) }}">
                    @error('mrp')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <!-- Return Policy & Physically Property -->
            <div class="row">
                <div class="col">
                    <label for="return_policy" class="form-label">Return Policy:</label>
                    <textarea class="form-control @error('return_policy') is-invalid @enderror" id="return" placeholder="Enter Return Policy" name="return">{{ old('return', $product->return_policy) }}</textarea>
                    @error('return_policy')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <label for="physical_property" class="form-label">Physically Property:</label>
                    <textarea class="form-control @error('physical_property') is-invalid @enderror" id="physical_property" placeholder="Enter Physically Property" name="physical_property">{{ old('physical_property', $product->physical_property) }}</textarea>
                    @error('physical_property')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <!-- Standard & Key Benefits -->
            <div class="row">
                <div class="col">
                    <label for="standards" class="form-label">Standard:</label>
                    <textarea class="form-control @error('standards') is-invalid @enderror" id="standards" placeholder="Enter Standard" name="standards">{{ old('standards', $product->standards) }}</textarea>
                    @error('standards')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <label for="key_benefits" class="form-label">Benefits:</label>
                    <textarea class="form-control @error('key_benefits') is-invalid @enderror" id="key_benefits" placeholder="Enter Benefits" name="key_benefits">{{ old('key_benefits', $product->key_benefits) }}</textarea>
                    @error('key_benefits')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <!-- Category & Sub Category -->
            <div class="row"> 
                <div class="col">
                    <label for="cat_id" class="form-label">Category:</label>
                        <select class="form-select @error('cat_id') is-invalid @enderror" name="cat_id" id="cat_id">
                            <option value="">Select Category</option> 
                                @foreach($categories as $data)
                                    <option value="{{ $data->id }}" {{ old('cat_id', $product->cat_id) == $data->id ? 'selected' : '' }}>{{ $data->name }}</option>
                                @endforeach
                        </select>
                            @error('cat_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                </div>
                <div class="col">
                    <label for="sub_cat_id" class="form-label">Sub Category:</label>
                        <select class="form-select @error('sub_cat_id') is-invalid @enderror" id="sub_cat_id" name="sub_cat_id">
                                @foreach($subCategories as $data)
                                    <option value="{{ $data->id }}" {{ old('sub_cat_id', $product->sub_cat_id) == $data->id ? 'selected' : '' }}>{{ $data->name }}</option>
                                @endforeach
                        </select>
                            @error('sub_cat_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                </div>
            </div>
            <!-- Description -->
            <div class="row">
                <div class="col">
                    <label for="description" class="form-label">Description:</label>
                        <textarea id="editor" class="form-control @error('description') is-invalid @enderror" name="description" placeholder="Description">{{ old('description', $product->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                </div>
            </div>
            <!-- Specification -->
            <div class="row">
                <div class="col">
                    <label for="specification" class="form-label">Specifications:</label>
                        <textarea class="form-control @error('specification') is-invalid @enderror" name="specification" placeholder="Specifications">{{ old('specification', $product->specification) }}</textarea>
                        @error('specification')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                </div>
            </div>
            <!-- Delivery Days & Status -->
            <div class="row">
                <div class="col">
                    <label for="availability" class="form-label">Delivery Days:</label>
                            <input type="text" class="form-control @error('availability') is-invalid @enderror" id="availability" placeholder="Enter Delivery Days" name="availability" value="{{ old('availability', $product->availability) }}">
                        @error('availability')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                </div>
                <div class="col">
                    <label for="status" class="form-label">Status:</label>
                        <select class="form-select @error('status') is-invalid @enderror" name="status">
                            <option value="">Select Status</option>
                            <option value="1" {{ old('status', $product->status) == "1" ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('status', $product->status) == "0" ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                </div>
            </div>
            <!-- Category Images -->
            <div class="row">
                <div class="col">
                    <label for="image" class="form-label">Category Images:</label>
                        <input type="file" class="form-control @error('image.*') is-invalid @enderror" name="image[]" multiple>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                </div>
            </div>
            <!-- Existing Images -->
            <div class="row mt-3">
                <div class="col">
                    <label class="form-label">Existing Images:</label>
                    <div class="d-flex flex-wrap">
                        <?php 
                      
                        ?>
                        @foreach($product->image as $image)
                
                            <div class="me-2 mb-2">
                                <img src="{{ asset($image) }}" alt="Product Image" style="width: 100px; height: 100px; object-fit: cover;">
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <!-- Variant Form Here Logic -->
            <div class="row mt-4">
                <div class="col">
                    <h5>Variants</h5>
                    <div id="dynamic-form">
                        @foreach($product->variants as $index => $variant)
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
                                    <input type="text" class="form-control" name="options[{{ $index }}][name]" placeholder="Enter name" value="{{ $variant->name }}">
                                </div>
                                <div class="col">
                                    <label for="image" class="form-label">Image:</label>
                                    <input type="file" class="form-control" name="options[{{ $index }}][image]">
                                    @if($variant->images)
                                        <img src="{{ asset($variant->images) }}" alt="Variant Image" style="width: 50px; height: 50px; object-fit: cover; margin-top: 5px;">
                                    @endif
                                </div>
                                <div class="col">
                                    <label for="description" class="form-label">Short Description:</label>
                                    <input type="text" class="form-control" name="options[{{ $index }}][short_description]" placeholder="Short description" value="{{ $variant->short_description }}">
                                </div>
                                <input type="hidden" name="options[{{ $index }}][id]" value="{{ $variant->id }}">
                                <div class="col d-flex align-items-end">
                                    <button type="button" class="btn btn-danger btn-sm remove-field">Remove</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-primary btn-sm mt-2" id="add-field">Add More</button>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col">
                    <button type="submit" class="btn btn-primary btn-sm">Update</button>
                </div>
            </div>
        </form>  
        </div>
    </div>
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
<script>
  tinymce.init({
    selector: 'textarea',
    plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
  });
</script>
