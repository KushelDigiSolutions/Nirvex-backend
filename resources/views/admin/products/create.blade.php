<x-layout bodyClass="g-sidenav-show  bg-gray-200">
@section('title')
    {{ 'Create Product' }}
@endsection


<x-navbars.sidebar activePage='product'></x-navbars.sidebar>
<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <x-navbars.navs.auth titlePage="Product"></x-navbars.navs.auth>
    <div class="container-fluid py-4" style="background-color:#fff">
        <div class="d-flex justify-content-between mb-2">
            <div class="pull-left">
                <h2>Create New Product</h2>
            </div>
        </div>
        <div class="card">   
            <div class="card-body">  
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
        <form action="{{ route('products.store') }}" method="post" enctype="multipart/form-data">
            @csrf
            <!-- Product Name & MRP -->
            <div class="row">
                <div class="col">
                    <label for="name" class="form-label">Product Name<span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" placeholder="Enter Product" name="name">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col">
                    <label for="mrp" class="form-label">MRP (RS.)<span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('mrp') is-invalid @enderror" id="mrp" placeholder="Enter MRP" name="mrp">
                    @error('mrp')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <!-- Return Policy & Physically Property -->
            <div class="row">
                <div class="col">
                    <label for="return_policy" class="form-label">Return Policy<span class="text-danger">*</span></label>
                    <textarea class="form-control @error('return_policy') is-invalid @enderror" id="return_policy" placeholder="Enter Return Policy" name="return_policy"></textarea>
                    @error('return_policy')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <label for="physically_property" class="form-label">Physically Property<span class="text-danger">*</span></label>
                    <textarea class="form-control @error('physically_property') is-invalid @enderror" id="physically_property" placeholder="Enter Physically Property" name="physically_property"></textarea>
                    @error('physically_property')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <!-- Standard & Key Benefits -->
            <div class="row">
                <div class="col">
                    <label for="standard" class="form-label">Standard<span class="text-danger">*</span></label>
                    <textarea class="form-control @error('standard') is-invalid @enderror" id="standard" placeholder="Enter Standard" name="standard"></textarea>
                    @error('standard')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <label for="benefits" class="form-label">Benefits<span class="text-danger">*</span></label>
                    <textarea class="form-control @error('benefits') is-invalid @enderror" id="benefits" placeholder="Enter Benefits" name="benefits"></textarea>
                    @error('benefits')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        <!-- Category & Sub Category -->
            <div class="row"> 
                <div class="col">
                    <label for="cat_id" class="form-label">Category<span class="text-danger">*</span></label>
                        <select class="form-select @error('cat_id') is-invalid @enderror" name="cat_id" id="cat_id">
                            <option value="">Select Category</option> 
                                @foreach($categories as $data)
                                    <option value="{{ $data->id }}">{{ $data->name }}</option>
                                @endforeach
                        </select>
                            @error('cat_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                </div>
                <div class="col">
                    <label for="sub_cat_id" class="form-label">Sub Category<span class="text-danger">*</span></label>
                        <select class="form-select @error('sub_cat_id') is-invalid @enderror" id="sub_cat_id" name="sub_cat_id">
                            <option value="">Select Sub Category</option>                      
                        </select>
                            @error('sub_cat_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                </div>
                </div>
                <!-- Description -->
                <div class="row">
                    <div class="col">
                        <label for="description" class="form-label">Discription<span class="text-danger">*</span></label>
                            <textarea  id="editor" class="form-control @error('description') is-invalid @enderror" name="description" placeholder="Discription">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                    </div>
                </div>
                <!-- Specification -->
                <div class="row">
                    <div class="col">
                        <label for="specification" class="form-label">Specifications<span class="text-danger">*</span></label>
                            <textarea class="form-control @error('specification') is-invalid @enderror" name="specification" placeholder="Specifications"></textarea>
                            @error('specification')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                    </div>
                </div>
                 <!-- Video URl -->
                 <div class="row">
                    <div class="col">
                        <label for="specification" class="form-label">Video<span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('video') is-invalid @enderror" id="video" placeholder="Video" name="video">
                            @error('availability')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                    </div>
                </div>
                <!-- Delivery Days & Status -->
                <div class="row">
                    <div class="col">
                        <label for="availability" class="form-label">Delivery Days<span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('availability') is-invalid @enderror" id="availability" placeholder="Enter Delivery Days" name="availability">
                            @error('availability')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                    </div>
                    <div class="col">
                        <label for="status" class="form-label">Status<span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" name="status">
                                <option value="">Select Status</option>
                                <option value="1" {{ old('status') == "1" ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('status') == "0" ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                    </div>
                </div>
                <!-- Category Images -->
                <div class="row">
                    <div class="col">
                        <label for="image" class="form-label">Product Images<span class="text-danger">*</span></label>
                            <input type="file" class="form-control @error('image.*') is-invalid @enderror" name="image[]" multiple>
                                @error('image.0')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                    </div>
                </div>
                <!-- Variant Form Here Logic -->
                <div class="row mt-4">
                    <div class="col">
                        <h5>Variant</h5>
                            <div id="dynamic-form">
                                <div class="variant-box">
                                    <div class="row mb-3">
                                        <div class="col">
                                            <label for="type" class="form-label">Type<span class="text-danger">*</span></label>
                                            <select class="form-select" name="options[0][type]">
                                                <option value="1">Quality</option>
                                                <option value="2">Color</option>
                                                <option value="3">Size</option>
                                            </select>
                                        </div>
                                        <div class="col">
                                            <label for="name" class="form-label">Name<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="options[0][name]" placeholder="Enter name">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col">
                                            <label for="image" class="form-label">Image<span class="text-danger">*</span></label>
                                            <input type="file" class="form-control" name="options[0][image]">
                                        </div>
                                        <div class="col">
                                            <label for="description" class="form-label">Short Description<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="options[0][short_description]" placeholder="Short description">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col">
                                            <label for="sku" class="form-label">Product Sku<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('options.0.sku') is-invalid @enderror" name="options[0][sku]">
                                            @error('options.0.sku')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col">
                                            <label for="min_quantity" class="form-label">Min Qty<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('options.0.min_quantity') is-invalid @enderror" name="options[0][min_quantity]">
                                            @error('options.0.min_quantity')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-primary btn-sm mt-2" id="add-field">Add More</button>
                        </div>
                    </div>
                </div>
<div class="row mt-4">
    <div class="col-12 d-flex justify-content-center">
        <button type="submit" class="btn btn-primary btn-sm">Submit</button>
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
                                            @error('sku')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                         <div class="col">
                                            <label for="min_quantity" class="form-label">Min Qty<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="options[${fieldCount}][min_quantity]">
                                             @error('min_quantity')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>  
                                    <div class="col d-flex justify-content-start align-items-end">
                                        <button type="button" class="btn btn-danger btn-sm remove-field">Remove</button>
                                    </div>                                 
                                </div>`;
        dynamicForm.insertAdjacentHTML('beforeend', newField);
        fieldCount++;
    });

    document.getElementById('dynamic-form').addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-field')) {
            e.target.closest('.variant-box').remove();
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
