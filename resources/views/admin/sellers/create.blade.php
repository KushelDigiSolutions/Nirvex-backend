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
                        </div>
                     </div>
                     <div class="col-xs-6 col-sm-6 col-md-6">
                        <div class="form-group">
                           <strong>Last Name:</strong>
                           <input type="text" name="last_name" placeholder="Last Name" class="form-control">
                        </div>
                     </div>
                     <div class="col-xs-6 col-sm-6 col-md-6">
                        <div class="form-group">
                           <strong>Email:</strong>
                           <input type="email" name="email" placeholder="Email" class="form-control">
                        </div>
                     </div>
                     <div class="col-xs-6 col-sm-6 col-md-6">
                        <div class="form-group">
                           <strong>Phone:</strong>
                           <input type="text" name="phone" placeholder="Phone" class="form-control">
                        </div>
                     </div>
                     <div class="col-xs-6 col-sm-6 col-md-6">
                        <div class="form-group">
                           <strong>Pincode:</strong>
                           <input type="text" name="pincode" placeholder="Pincode" class="form-control">
                        </div>
                     </div>
                     <div class="col-xs-6 col-sm-6 col-md-6">
                        <div class="form-group">
                           <strong>Password:</strong>
                           <input type="password" name="password" placeholder="Password" class="form-control">
                        </div>
                     </div>
                     <div class="col-xs-6 col-sm-6 col-md-6">
                        <div class="form-group">
                           <strong>Confirm Password:</strong>
                           <input type="password" name="confirm-password" placeholder="Confirm Password" class="form-control">
                        </div>
                     </div>
                     <div class="col-xs-6 col-sm-6 col-md-6 text-center">
                        <button type="submit" class="btn btn-primary btn-sm mt-2 mb-3"><i class="fa-solid fa-floppy-disk"></i> Submit</button>
                     </div>
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