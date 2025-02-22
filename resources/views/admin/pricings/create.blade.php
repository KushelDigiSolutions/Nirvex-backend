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
        <form action="{{ route('products.store') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
            <label for="search-product">Search Product</label>
            <!-- <input type="text" id="search-product" name="search_product"> -->
            <input type="text" id="search-product" name="search_product" onkeyup="searchProduct(this.value)" class="form-control @error('search-product') is-invalid @enderror">
            @error('search-product')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div id="search-results"></div>
        </div>

        <div class="form-group">
    <label for="variant-name">Variant Type</label>
    <select id="variant-type" name="variant_type">
        <option value="">Select Variant</option>
    </select>
</div>
        <div class="inline-group">
        <div class="form-group">
            <label for="pin-code">Pin Code</label>
            <input type="text" id="pin-code" name="pin_code">
        </div>
        <div class="form-group datetime-selector">
            <label for="valid-upto">Valid Upto</label>
            <input type="datetime-local" id="valid-upto" name="valid_upto">
        </div>
        </div>

       
        <div class="inline-group">
            <div class="form-group">
                <label for="mrp">MRP</label>
                <input type="text" id="mrp" name="mrp">
            </div>

            <div class="form-group">
                <label for="sale-price">Sale Price</label>
                <input type="text" id="sale-price" name="sale_price">
            </div>
        </div>

        <div class="inline-group">
            <div class="form-group">
                <label for="tax-type">Tax Type</label>
                <select id="tax-type" name="tax_type">
                    <option value="type1">Percentage</option>
                    <option value="type2">Flat</option>
                </select>
            </div>

            <div class="form-group">
                <label for="tax-value">Tax Value</label>
                <input type="text" id="tax-value" name="tax_value">
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
    $('#search-product').on('keyup', function () {
        let query = $(this).val();

        if (query.length > 2) {
            $.ajax({
                url: 'http://localhost/nvbackend/public/admin/search-product',
                type: 'GET',
                data: { query: query },
                success: function (response) {
                    if (response.length > 0) {
                        const product = response[0]; 
                        const variants = product.variant; 

                        $('#product-name').val(product.name || '');
                        $('#mrp').val(product.mrp || '');

                        if (variants && variants.length > 0) {
                            populateVariantFields(variants[0]); 
                            populateVariantDropdown(variants); 
                        } else {
                            clearVariantFields(); 
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

    $('#variant-name').on('change', function () {
        const selectedVariantId = $(this).val();

        const selectedVariant = loadedVariants.find(v => v.id == selectedVariantId);
        if (selectedVariant) {
            populateVariantFields(selectedVariant);
        } else {
            clearVariantFields();
        }
    });

    let loadedVariants = [];

    function populateVariantFields(variant) {
        $('#variant-sku').val(variant.sku || '');
        $('#variant-description').val(variant.short_description || '');
        $('#variant-name').val(variant.name || '');
    }

    function populateVariantDropdown(variants) {
    loadedVariants = variants; 
    $('#variant-type').empty(); 
    $('#variant-type').append('<option value="">Select Variant</option>');

    const typeMap = {
        1: 'Quality',
        2: 'Color',
        3: 'Size'
    };

    variants.forEach(variant => {
        const typeName = typeMap[variant.type] || 'Unknown'; 
        $('#variant-type').append(
            `<option value="${variant.id}">${typeName}</option>`
        );
    });
}

    function clearAllFields() {
        $('#product-name').val('');
        $('#mrp').val('');
        clearVariantFields();
    }

    function clearVariantFields() {
        $('#variant-sku').val('');
        $('#variant-description').val('');
        $('#variant-name').empty();
        $('#variant-name').append('<option value="">Select Variant</option>');
    }
});


</script>

