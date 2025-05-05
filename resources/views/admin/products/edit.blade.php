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
                    <label for="pname" class="form-label">Product Name:</label>
                    <input type="text" class="form-control @error('pname') is-invalid @enderror" id="pname" placeholder="Enter Product" name="pname" value="{{ $product->name }}">
                    @error('pname')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col">
                    <label for="mrp" class="form-label">MRP (RS.):</label>
                    <input type="text" class="form-control @error('mrp') is-invalid @enderror" id="mrp" placeholder="Enter MRP" name="mrp" value="{{ $product->mrp }}">
                    @error('mrp')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <!-- Return Policy & Physically Property -->
            <div class="row">
                <div class="col">
                    <label for="return_policy" class="form-label">Return Policy:</label>
                    <textarea class="form-control @error('return_policy') is-invalid @enderror" id="return_policy" placeholder="Enter Return Policy" name="return_policy">{{ $product->return_policy }}</textarea>
                    @error('return_policy')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <label for="physical_property" class="form-label">Physically Property:</label>
                    <textarea class="form-control @error('physical_property') is-invalid @enderror" id="physical_property" placeholder="Enter Physically Property" name="physical_property">{{ $product->physical_property }}</textarea>
                    @error('physical_property')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <!-- Standard & Key Benefits -->
            <div class="row">
                <div class="col">
                    <label for="standards" class="form-label">Standard:</label>
                    <textarea class="form-control @error('standards') is-invalid @enderror" id="standards" placeholder="Enter Standard" name="standards">{{ $product->standards }}</textarea>
                    @error('standards')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <label for="key_benefits" class="form-label">Benefits:</label>
                    <textarea class="form-control @error('key_benefits') is-invalid @enderror" id="key_benefits" placeholder="Enter Benefits" name="key_benefits">{{ $product->key_benefits }}</textarea>
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
                                    <option value="{{ $data->id }}" {{ $product->cat_id == $data->id ? 'selected' : '' }}>{{ $data->name }}</option>
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
                    <label for="descriptions" class="form-label">Description:</label>
                        <textarea id="editor" class="form-control @error('descriptions') is-invalid @enderror" name="descriptions" placeholder="Description">{{ $product->description }}</textarea>
                        @error('descriptions')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                </div>
            </div>
            <!-- Specification -->
            <div class="row">
                <div class="col">
                    <label for="specification" class="form-label">Specifications:</label>
                        <textarea class="form-control @error('specification') is-invalid @enderror" name="specification" placeholder="Specifications">{{ $product->specification }}</textarea>
                        @error('specification')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                </div>
            </div>
            <!-- Delivery Days & Status -->
            <div class="row">
                <div class="col">
                    <label for="availability" class="form-label">Delivery Days:</label>
                            <input type="text" class="form-control @error('availability') is-invalid @enderror" id="availability" placeholder="Enter Delivery Days" name="availability" value="{{  $product->availability }}">
                        @error('availability')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                </div>
                <div class="col">
                    <label for="status" class="form-label">Status:</label>
                        <select class="form-select @error('status') is-invalid @enderror" name="status">
                            <option value="">Select Status</option>
                            <option value="1" {{ $product->status == "1" ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ $product->status == "0" ? 'selected' : '' }}>Inactive</option>
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
                    @if (!empty($product->image))
                    @foreach($product->image as $images)
                
                        <div class="col-md-3">
                                        <div class="image-container">
                                            <img src="{{ url('/') . '/' . $images }}" alt="Product Image" class="img-thumbnail" style="max-width: 100%; height: auto;">
                                            <button type="button" class="btn btn-danger btn-sm delete-image" data-image="{{ $images }}">Delete</button>
                                            <input type="hidden" name="delete_images[]" value="{{ $images }}" class="delete-input">
                                        </div>
                                </div>
                        @endforeach
                        @endif
                    </div>
                </div>
            </div>
            <!-- Variant Form Here Logic -->
           <!-- Variant Form Here Logic -->
           <div class="row mt-4">
                    <div class="col">
                        <h5>Variant</h5>
                            <div id="dynamic-form">
                            @if ($product->variants && $product->variants->count())
                            @foreach ($product->variants as $index => $variant)
                                <div class="variant-box">
                                    <div class="row mb-3">
                                        <div class="col">
                                            <label for="type" class="form-label">Type<span class="text-danger">*</span></label>
                                            <select class="form-select" name="options[{{ $index }}][type]">
                                                <option value="1" {{ $variant->type == 1 ? 'selected' : '' }}>Quality</option>
                                                <option value="2" {{ $variant->type == 2 ? 'selected' : '' }}>Color</option>
                                                <option value="3" {{ $variant->type == 3 ? 'selected' : '' }}>Size</option>
                                            </select>
                                        </div>
                                        <div class="col">
                                            <label for="name" class="form-label">Name<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="options[{{ $index }}][name]" value="{{ $variant->name }}" placeholder="Enter name">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col">
                                            <label for="image" class="form-label">Image<span class="text-danger">*</span></label>
                                            <input type="file" class="form-control" name="options[{{ $index }}][image]">
                                            @if ($variant->images)
                                            <img src="{{ url('/').'/'. $variant->images }}" alt="Variant Image" class="img-thumbnail mt-2" width="100">
                                        @endif
                                        </div>
                                        <div class="col">
                                            <label for="description" class="form-label">Short Description<span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="options[{{ $index }}][short_description]" value="{{ $variant->short_description }}" placeholder="Short description">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col">
                                            <label for="sku" class="form-label">Product Sku<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('options.0.sku') is-invalid @enderror" name="options[{{ $index }}][sku]" value="{{$variant->sku}}">
                                            <!-- @error('options.0.sku')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror -->
                                        </div>
                                        <!-- <div class="col d-flex align-items-end">      
                                        </div> -->
                                        <div class="col">
                                            <label for="size_amount" class="form-label">Amount<span class="text-danger">*</span></label>
                                            <input type="number" class="form-control @error('options.0.size_amount') is-invalid @enderror" name="options[{{ $index }}][size_amount]" value="{{$variant->size_amount}}">
                                            
                                        </div>
                                    </div>

                                </div>
                            </div>
                            @endforeach
                            @endif
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-primary btn-sm mt-2" id="add-field">Add More</button>
                        </div>
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
        const newField = `<div class="variant-box">
                                    <div class="row mb-3">
                                        <div class="col">
                                            <label for="type" class="form-label">Type<span class="text-danger">*</span></label>
                                            <select class="form-select" name="options[${fieldCount}][type]">
                                                <option value="1">Quality</option>
                                                <option value="2">Color</option>
                                                <option value="3">Size</option>
                                            </select>
                                        </div>
                                        <div class="col">
                                            <label for="name" class="form-label">Name<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="options[${fieldCount}][name]" placeholder="Enter name">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col">
                                            <label for="image" class="form-label">Image<span class="text-danger">*</span></label>
                                            <input type="file" class="form-control" name="options[${fieldCount}][image]">
                                        </div>
                                        <div class="col">
                                            <label for="description" class="form-label">Short Description<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="options[${fieldCount}][short_description]" placeholder="Short description">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col">
                                            <label for="sku" class="form-label">Product Sku<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="options[${fieldCount}][sku]">
                                        </div>
                                        <div class="col d-flex align-items-end">
                                        </div>
                                    </div>
                                    <div class="col d-flex justify-content-start align-items-end">
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
    document.querySelectorAll('.delete-image').forEach(button => {
    button.addEventListener('click', function () {
        const imagePath = this.getAttribute('data-image');
        this.closest('.image-container').remove();
        document.querySelector(`input[value="${imagePath}"]`).remove();
    });
});

</script>


<script>
  tinymce.init({
    selector: 'textarea',
    plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
  });
</script>
