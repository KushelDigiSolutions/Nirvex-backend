<x-layout bodyClass="g-sidenav-show  bg-gray-200">
@section('title')
        {{ 'Edit Pricing' }}
    @endsection
    <x-navbars.sidebar activePage='dashboard'></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <!-- Navbar -->
        <x-navbars.navs.auth titlePage="Dashboard"></x-navbars.navs.auth>
        <!-- End Navbar -->
        <div class="container-fluid py-4" style="background-color:#fff">
        <div class="d-flex justify-content-between mb-2">
                            <div class="pull-left">
                                <h2>Edit Pricing</h2>
                            </div>
                        </div>
                    <div class="card">   
                        <div class="card-body">   
                    @if(session('success'))
                        <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @endif
                    <form action="{{ route('pricings.update', $pricing->id) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row">
                    <div class="col">
                        <label>Pincode</label>
                            <input type="text" name="pincode" class="form-control @error('pincode') is-invalid @enderror" value="{{ $pricing->pincode }}" required>
                            @error('pincode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                    </div>
                    <div class="col">
                        <label>Product ID</label>
                        <input type="number" name="product_id" class="form-control @error('product_id') is-invalid @enderror" value="{{ $pricing->product_id }}" required>
                        @error('product_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    </div>
                    <div class="row">
                    <div class="col">
                        <label>Product SKU ID</label>
                            <input type="text" name="product_sku_id" class="form-control @error('product_sku_id') is-invalid @enderror" value="{{ $pricing->product_sku_id }}" required>
                            @error('product_sku_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                    </div>
                    <div class="col">
                        <label>MRP</label>
                            <input type="number" name="mrp" step="0.01" class="form-control @error('mrp') is-invalid @enderror" value="{{ $pricing->mrp }}" required>
                            @error('mrp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                    </div>  
                    </div>
                    <div class="row">
                    <div class="col">
                        <label>Price</label>
                            <input type="number" name="price" step="0.01" class="form-control @error('price') is-invalid @enderror" value="{{ $pricing->price }}" required>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                    </div>
                    <div class="col">
                        <label>Tax Type</label>
                            <select name="tax_type" class="form-control @error('tax_type') is-invalid @enderror">
                                <option value="0" {{ $pricing->tax_type == 0 ? 'selected' : '' }}>Percentage</option>
                                <option value="1" {{ $pricing->tax_type == 1 ? 'selected' : '' }}>Flat</option>
                                @error('tax_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            </select>
                    </div>
                    </div>
                    <div class="row">
                    <div class="col">
                        <label>Tax Value</label>
                        <input type="number" name="tax_value" step="0.01" class="form-control @error('tax_value') is-invalid @enderror" value="{{ $pricing->tax_value }}" required>
                        @error('tax_value')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col">
                        <label>Shipping Charges</label>
                            <input type="number" name="ship_charges" step="0.01" class="form-control @error('ship_charges') is-invalid @enderror" value="{{ $pricing->ship_charges }}" required>
                            @error('shmop_charges')
                                    <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                    <div class="col">
                        <label>Valid Upto</label>
                            <input type="datetime-local" name="valid_upto" class="form-control @error('valid_upto') is-invalid @enderror" value="{{ $pricing->valid_upto }}" required>
                            
                            @error('valid_upto')
                                    <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                    </div>
                    <div class="col">
                            <label for="status" class="form-label">Status:</label>
                            <select class="form-select @error('status') is-invalid @enderror" name="status">
                                <option value="" {{ $pricing->status === null ? 'selected' : '' }}>Select status</option>
                                <option value="1" {{ $pricing->status == 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ $pricing->status == 0 ? 'selected' : '' }}>Inactive</option>
                            </select>
                            
                            @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Update</button>
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
