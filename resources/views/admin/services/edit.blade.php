<x-layout bodyClass="g-sidenav-show  bg-gray-200">
@section('title')
    {{ 'Edit service' }}
@endsection
<x-navbars.sidebar activePage='dashboard'></x-navbars.sidebar>
<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
    <x-navbars.navs.auth titlePage="Edit Service"></x-navbars.navs.auth>
    <div class="container-fluid py-4" style="background-color:#fff">
        <div class="row mb-4">
            <div class="col-lg-10 col-md-10 mb-md-0 mb-4"></div>
            <div class="col-lg-2 col-md-2 mb-md-0 mb-4">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-right">
                        @can('role-create')
                            <!-- <a class="btn btn-success btn-sm mb-2" href="{{ route('services.index') }}">Back to Service</a> -->
                            <a class="btn btn-success btn-sm mb-2" href="{{ route('services.index') }}"><i class="fa fa-plus"></i> Back to Service</a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
            <form action="{{ route('services.update', $services->id) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col">
                    <label for="name" class="form-label">Service Name:</label>
                    <input type="text" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" id="name" placeholder="Enter Service" name="name" value="{{ $services->name }}">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                </div>
                <div class="col">
                       <label for="status" class="form-label">Status:</label>
                                    <select class="form-select" name="status">
                                        <option value="" {{ $services->status === null ? 'selected' : '' }}>Select status</option>
                                        <option value="1" {{ $services->status == 1 ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ $services->status == 0 ? 'selected' : '' }}>Inactive</option>
                                    </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <label for="type" class="form-label">Type:</label>
                    <select class="form-select" name="type">
                        <option value="1" {{ $services->type == 1 ? 'selected' : '' }}>Services</option>
                        <option value="2" {{ $services->type == 2 ? 'selected' : '' }}>Properties</option>
                    </select>
                </div>
                <div class="col">
                    <label for="description" class="form-label">Descriptions:</label>
                        <textarea class="form-control" id="editor1" name="description">{{ $services->description }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                </div>
            <div class="row">
                <div class="col mt-3">
                    <label class="form-label">Existing Images:</label>
                    <div class="row">
                        @if($services && is_array($services->image))
                            @forelse($services->image as $images)
                                <div class="col-md-3">
                                    <div class="image-container">
                                        <img src="{{ url('/') . '/' . $images }}" alt="Service Image" class="img-thumbnail" style="max-width: 100%; height: auto;">
                                            <button type="button" class="btn btn-danger btn-sm delete-image" data-image="{{ $images }}">Delete</button>
                                            <input type="hidden" name="delete_images[]" value="{{ $images }}" class="delete-input">
                                        </div>
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
                    <label for="image" class="form-label">Service Images:</label>
                    <input type="file" class="form-control @error('image.*') is-invalid @enderror" name="image[]" multiple>
                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col mt-3">
                    <button type="submit" class="btn btn-primary btn-sm">Update</button>
                </div>
            </div>
        </form>
    </div>
    <x-footers.auth></x-footers.auth>
</main>
<x-plugins></x-plugins>
@push('js')
<script src="{{ asset('assets') }}/js/chartjs.min.js"></script>
@endpush
</x-layout>
<script>
    document.querySelectorAll('.delete-image').forEach(button => {
    button.addEventListener('click', function () {
        const imagePath = this.getAttribute('data-image');
        this.closest('.image-container').remove();
        document.querySelector(`input[value="${imagePath}"]`).remove();
    });
});
</script>