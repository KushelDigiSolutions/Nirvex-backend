<x-layout bodyClass="g-sidenav-show  bg-gray-200">
@section('title')
        {{ 'Create Staff Admin' }}
    @endsection
<x-navbars.sidebar activePage='Staff Admin'></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <!-- Navbar -->
        <x-navbars.navs.auth titlePage="Dashboard"></x-navbars.navs.auth>
        <!-- End Navbar -->
        <div class="container-fluid py-4" style="background-color:#fff">
        <div class="d-flex justify-content-between mb-2">
                            <div class="pull-left">
                                <h2>Create Staff Admin</h2>
                            </div>
                        </div>
                    <div class="card">   
                        <div class="card-body">   
                            @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                            @endif
                            <form action="{{ route('staff.store') }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col">
                                    <label for="first_name" class="form-label">First Name:</label>
                                        <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" placeholder="Enter First Name" name="first_name">
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col">
                                    <label for="last_name" class="form-label">Last Name:</label>
                                        <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" placeholder="Enter Last Name" name="last_name">
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                
                                </div>
                                
                                <div class="row">
                                
                                <div class="col">
                                    <label for="last_name" class="form-label">Email:</label>
                                        <input type="text" class="form-control @error('email') is-invalid @enderror" id="email" placeholder="Enter Email" name="email">
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col">
                                    <label for="phone" class="form-label">Phone:</label>
                                        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" placeholder="Enter Phone No." name="phone">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    </div>

                                
                                    <div class="row">
                                
                                <div class="col">
                                    <label for="Password" class="form-label">Password:</label>
                                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" placeholder="Enter Password" name="password">
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col">
                                    <label for="password_confirmation" class="form-label">Confirm Password:</label>
                                        <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" id="password_confirmation" placeholder="Enter Confirm Password" name="password_confirmation">
                                        @error('confirm_password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    </div>


                                    <div class="row">
                                    <div class="col">
                                    <label for="status" class="form-label">Status:</label>
                                    <select class="form-select @error('status') is-invalid @enderror" name="status">
                                        <option value="">Select status</option>
                                        <option value="1" {{ old('status') == "1" ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ old('status') == "0" ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    </div>
                                
                                
                                <div class="col">
                                    <label for="image" class="form-label">Profile Pic:</label>
                                        <input type="file" class="form-control @error('image') is-invalid @enderror" name="image" />
                                        @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    </div>
                                </div>
                                <div class="col">
                                <button type="submit" class="btn btn-primary btn-sm mt-2 mb-3">Submit</button>
                                </div>
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
