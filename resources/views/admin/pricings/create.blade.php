<x-layout bodyClass="g-sidenav-show  bg-gray-200">
@section('title')
    {{ 'Create Pricing' }}
@endsection
<x-navbars.sidebar activePage='pricing'></x-navbars.sidebar>
<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <x-navbars.navs.auth titlePage="Pricing"></x-navbars.navs.auth>
    <div class="container-fluid py-4" style="background-color:#fff">
        <div class="d-flex justify-content-between mb-2">
                            <div class="pull-left">
                                <h2>Create New Price</h2>
                            </div>
                        </div>
                    <div class="card">   
                        <div class="card-body">   
                            @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                            @endif
        <form action="{{ route('pricings.store') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="row">
            <div class="col">
            <label for="search-product">Search Product</label>
            <!-- <input type="text" id="search-product" name="search_product"> -->
            <!-- <input type="text" id="search-product" name="search_product" onkeyup="searchProduct(this.value)" class="form-control @error('search-product') is-invalid @enderror">
            <input type="hidden" id="product_id" name="product_id"> -->
            <select id="search-product" name="product_id" class="form-control">
            <option value="">Select Products</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </select>
            @error('search-product')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div id="search-results"></div>
        </div>
        <div class="col">
            <label for="variant-name">Select Product Sku</label>
            <select id="product-sku-id" name="product_sku_id" class="form-control">
            <option value="">Select Product SKU</option>
                </select>
        </div>
        </div>
        <div class="row">
            <div class="col">
                <label for="mrp">MRP</label>
                <input type="text" id="mrp" class="form-control" name="mrp">
            </div>
            <div class="col">
                <label for="sale-price">Sale Price</label>
                <input type="text" id="sale-price" class="form-control" name="price">
            </div>
        </div>
        <div class="row">
          
            <div class="col">
                <label for="tax-value">Tax Value(%)</label>
                <select class="form-select" id="tax-value" name="tax_value">
                    @for($i = 1; $i <= 100; $i++)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>
            </div>
            <div class="col">
                <label for="tax-value">Ship Charges</label>
                <input type="text" id="ship-charges" name="ship_charges" class="form-control" >
            </div>
           
        </div>
        <div class="row">
            <div class="col">
                <label for="status">Product Status:</label>
                    <select class="form-select @error('status') is-invalid @enderror" name="status">
                        <option value="">Select Status</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
            </div>
            <div class="col">
                <label for="valid-upto">Valid Upto</label>
                <input type="datetime-local" id="valid-upto" class="form-control" name="valid_upto">
            </div>
        </div>
        <div class="row">
        <div class="col">
            <label for="pin-code">Pin Code</label>
            <input type="text" id="pin_code" class="form-control" name="pincode">
            @error('pincode')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col">
            <label for="status">Cash On Delivery:</label>
                <select class="form-select @error('is_cash') is-invalid @enderror" name="is_cash">
                    <option value="1">Avaiable</option>
                    <option value="0">Not Available</option>
                </select>
                    @error('is_cash')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
            </div>
        </div>
        <div class="form-group datetime-selector">
        <button type="submit" class="btn btn-primary btn-sm">Submit</button>
        </div>

        </form>
        </div>
                    </div>
            </div>
</main>
<x-plugins></x-plugins>
</x-layout>
<style>
        .form-container {
            display: flex;
            flex-direction: column;
            gap: 15px;
            max-width: 400px;
            margin: auto;
        }
        .form-group {
            display: flex;
            flex-direction: column;
        }
        .form-group label {
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input,
        .form-group select {
            padding: 8px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .inline-group {
            display: flex;
            gap: 10px;
        }
        .inline-group .form-group {
            flex: 1;
        }
        .datetime-selector {
            margin-top: 10px;
        }
    </style>

    <script>
        $(document).ready(function () {
    $('#search-product').on('change', function () {
        let productId = $(this).val();
        let baseUrl = "{{ url('admin/search-product') }}";

        if (productId) {
            $.ajax({
                url: baseUrl,
                type: 'GET',
                data: { product_id: productId },
                success: function (response) {
                    console.log("Product Response:", response);

                    if (response) {
                        $('#product_id').val(response.id);
                        $('#product-name').val(response.name || '');
                        $('#mrp').val(response.mrp || '');

                        if (response.variants.length > 0) {
                            populateVariantDropdown(response.variants);
                        } else {
                            clearVariantDropdown();
                        }
                    } else {
                        clearAllFields();
                    }
                },
                error: function () {
                    alert('An error occurred while fetching the product data.');
                }
            });
        } else {
            clearAllFields();
        }
    });

    function populateVariantDropdown(variants) {
        console.log("Variants:", variants);

        $('#product-sku-id').empty();
        // $('#product-sku-id').append('<option value="">Select Product SKU</option>');

        variants.forEach(variant => {
            $('#product-sku-id').append(
                `<option value="${variant.id}">${variant.sku}</option>`
            );
        });

        console.log("#product-sku-id:", $('#product-sku-id').html());
    }

    function clearVariantDropdown() {
        $('#product-sku-id').empty().append('<option value="">Select Product SKU</option>');
    }

    function clearAllFields() {
        $('#product_id').val('');
        $('#product-name').val('');
        $('#mrp').val('');
        clearVariantDropdown();
    }
});

    </script>
<!-- <script>
$(document).ready(function () {
    $('#search-product').on('change', function () {
        let productId = $(this).val();
        var baseUrl = "{{ url('admin/search-product') }}";

        if (productId) {
            $.ajax({
                url: baseUrl,
                type: 'GET',
                data: { product_id: productId }, 
                success: function (response) {
                    console.log("Product Response:", response); // Debugging log

                    if (response) {
                        const product = response; 
                        const variants = product.variants || [];  

                        $('#product_id').val(product.id);
                        $('#product-name').val(product.name || '');
                        $('#mrp').val(product.mrp || '');

                        if (variants.length > 0) {
                            populateVariantDropdown(variants);
                        } else {
                            clearVariantDropdown();
                        }
                    } else {
                        clearAllFields(); 
                    }
                },
                error: function () {
                    alert('An error occurred while fetching the product data.');
                }
            });
        } else {
            clearAllFields();
        }
    });

    let loadedVariants = [];

    function populateVariantDropdown(variants) {
        console.log("Variants:", variants); // Debugging log

        loadedVariants = variants;
        $('#product-sku-id').empty(); // Clear existing options
        $('#product-sku-id').append('<option value="">Select Product SKU</option>');

        variants.forEach(variant => {
            $('#product-sku-id').append(
                `<option value="${variant.sku}">${variant.sku}</option>`
            );
        });

        console.log("#product-sku-id:", $('#product-sku-id').html()); 
    }

    function clearVariantDropdown() {
        $('#product-sku-id').empty();
        $('#product-sku-id').append('<option value="">Select Product SKU</option>');
    }

    function clearAllFields() {
        $('#product_id').val('');
        $('#product-name').val('');
        $('#mrp').val('');
        clearVariantDropdown();
    }
});


</script> -->