<x-layout bodyClass="g-sidenav-show  bg-gray-200">
   @section('title')
   {{ 'Create Seller' }}
   @endsection
   <x-navbars.sidebar activePage='seller'></x-navbars.sidebar>
   <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
      <x-navbars.navs.auth titlePage="Seller"></x-navbars.navs.auth>
      <div class="container-fluid py-4" style="background-color:#fff">
         <div class="d-flex justify-content-between mb-2">
            <div class="pull-left">
               <h2>Create Seller</h2>
            </div>
         </div>
         <div class="card">
            <div class="card-body">
               @if(session('success'))
               <div class="alert alert-success">
                  {{ session('success') }}
               </div>
               @endif
               <form method="POST" action="{{ route('sellers.store') }}">
                  @csrf
                  <input type="hidden" name="user_type" class="form-control" value="3">
                  <div class="row">
                     <div class="col-xs-6 col-sm-6 col-md-6">
                        <div class="form-group">
                           <strong>First Name:</strong>
                           <input type="text" name="first_name" placeholder="First Name" class="form-control">
                           @error('first_name')
        <small class="text-danger">{{ $message }}</small>
    @enderror
                        </div>
                     </div>
                     <div class="col-xs-6 col-sm-6 col-md-6">
                        <div class="form-group">
                           <strong>Last Name:</strong>
                           <input type="text" name="last_name" placeholder="Last Name" class="form-control">
                           @error('last_name')
        <small class="text-danger">{{ $message }}</small>
    @enderror
                        </div>
                     </div>
                     <div class="col-xs-6 col-sm-6 col-md-6">
                        <div class="form-group">
                           <strong>Email:</strong>
                           <input type="email" name="email" placeholder="Email" class="form-control">
                           @error('email')
        <small class="text-danger">{{ $message }}</small>
    @enderror

                        </div>
                     </div>
                     <div class="col-xs-6 col-sm-6 col-md-6">
                        <div class="form-group">
                           <strong>Phone:</strong>
                           <input type="text" name="phone" placeholder="Phone" class="form-control">
                           @error('phone')
        <small class="text-danger">{{ $message }}</small>
    @enderror
                        </div>
                     </div>
                     <div class="col-xs-6 col-sm-6 col-md-6">
                        <div class="form-group">
                           <strong>Pincode:</strong>
                           <input type="text" name="pincode" placeholder="Pincode" class="form-control">
                           @error('pincode')
        <small class="text-danger">{{ $message }}</small>
    @enderror
                        </div>
                     </div>
                     <div class="col-xs-6 col-sm-6 col-md-6">
                        <div class="form-group">
                           <strong>Password:</strong>
                           <input type="password" name="password" placeholder="Password" class="form-control">
                           @error('password')
        <small class="text-danger">{{ $message }}</small>
    @enderror
                        </div>
                     </div>
                     <div class="col-xs-6 col-sm-6 col-md-6">
                        <div class="form-group">
                           <strong>Confirm Password:</strong>
                           <input type="password" name="confirm-password" placeholder="Confirm Password" class="form-control">
                           @error('confirm-password')
        <small class="text-danger">{{ $message }}</small>
    @enderror
                        </div>
                     </div>
                     <div class="row mt-4">
                        <div class="col-12">
                           <div class="alert alert-info">
                              Seller Adddress Enter Below
                           </div>
                        </div>
                     </div>


                     
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Name*</label>
                            <input type="text" name="name" id="mname" class="form-control">
                            @error('name')
        <small class="text-danger">{{ $message }}</small>
    @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone*</label>
                            <input type="text" name="sphone" id="sphone" class="form-control" maxlength="12">
                            @error('sphone')
        <small class="text-danger">{{ $message }}</small>
    @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Address Line 1*</label>
                            <input type="text" name="address1" id="maddress1" class="form-control">
                            @error('address1')
        <small class="text-danger">{{ $message }}</small>
    @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Address Line 2</label>
                            <input type="text" name="address2" id="maddress2" class="form-control">
                            @error('address2')
        <small class="text-danger">{{ $message }}</small>
    @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Landmark</label>
                            <input type="text" name="landmark" id="mlandmark" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Pincode*</label>
                            <input type="text" name="pincode" id="mpincode" class="form-control" maxlength="10">
                            @error('pincode')
        <small class="text-danger">{{ $message }}</small>
    @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">City*</label>
                            <input type="text" name="city" id="mcity" class="form-control">
                            @error('city')
        <small class="text-danger">{{ $message }}</small>
    @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">State*</label>
                            <input type="text" name="state" id="mstate" class="form-control">
                            @error('state')
        <small class="text-danger">{{ $message }}</small>
    @enderror
                        </div>
                       
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Status*</label>
                            <select name="status" id="mstatus" class="form-select">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                                @error('status')
        <small class="text-danger">{{ $message }}</small>
    @enderror
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Set as Default</label>
                            <select name="is_default" id="mis_default" class="form-select">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-12 text-center">
    <button type="submit" class="btn btn-primary btn-sm mt-2 mb-3">
        <i class="fa-solid fa-floppy-disk"></i> Submit
    </button>
</div>
                     <!-- <div class="col-xs-6 col-sm-6 col-md-6 text-center">
                        <button type="submit" class="btn btn-primary btn-sm mt-2 mb-3"><i class="fa-solid fa-floppy-disk"></i> Submit</button>
                     </div> -->
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