<x-layout bodyClass="g-sidenav-show bg-gray-200">
@section('title')
    {{ 'Create Seller' }}
@endsection

<x-navbars.sidebar activePage='dashboard'></x-navbars.sidebar>
<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <!-- Navbar -->
    <x-navbars.navs.auth titlePage="Create Seller"></x-navbars.navs.auth>
    <!-- End Navbar -->
    <div class="container-fluid py-4" style="background-color:#fff">
        <div class="row mb-4">
            <div class="col-lg-10 col-md-10 mb-md-0 mb-4"></div>
            <div class="col-lg-2 col-md-2 mb-md-0 mb-4">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-left">
                        @can('role-create')
                            <a class="btn btn-success btn-sm mb-2" href="{{ route('sellers.index') }}">Create New Seller</a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>

        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <strong>Whoops!</strong> There were some problems with your input.<br><br>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('sellers.storeSeller') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="user_type" value="2">
            <div class="row">
                <!-- First Name -->
                <div class="col-md-6 mb-3">
                    <label for="first_name" class="form-label">First Name:</label>
                    <input type="text" class="form-control" id="first_name" name="first_name" placeholder="Enter first name" required>
                </div>

                <!-- Last Name -->
                <div class="col-md-6 mb-3">
                    <label for="last_name" class="form-label">Last Name:</label>
                    <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Enter last name (optional)">
                </div>
            </div>

            <div class="row">
                <!-- Email -->
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Email:</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter email address" required>
                </div>

                <!-- Phone -->
                <div class="col-md-6 mb-3">
                    <label for="phone" class="form-label">Phone:</label>
                    <input type="text" class="form-control" id="phone" name="phone" placeholder="Enter phone number (optional)">
                </div>
            </div>

            <div class="row">
                <!-- Password -->
                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label">Password:</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter password" required>
                </div>

                <!-- Confirm Password -->
                <div class="col-md-6 mb-3">
                    <label for="confirm-password" class="form-label">Confirm Password:</label>
                    <input type="password" class="form-control" id="confirm-password" name="confirm-password"
                           placeholder="Confirm password" required>
                </div>
            </div>

            <!-- Image Upload -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="image" class="form-label">Profile Image:</label>
                    <input type="file" class="form-control" id="image" name="image">
                </div>

                <!-- Seller Active Status -->
                <div class="col-md-6 mb-3">
                    <label for="seller_active" class="form-label">Seller Active Status:</label>
                    <select name="seller_active" id="seller_active" class="form-select form-select-lg">
                        <option value="">Select status</option>
                        <option value="0">Not Active</option>
                        <option value="1">Active</option>
                        <option value="2">Temporary Disabled</option>
                    </select>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary btn-sm mt-2 mb-3">Submit</button>

        </form>                
    </div>

    <!-- Footer -->
    <x-footers.auth></x-footers.auth>
</main>

<x-plugins></x-plugins>

@push('js')
<script src="{{ asset('assets') }}/js/chartjs.min.js"></script>
@endpush
</x-layout>
