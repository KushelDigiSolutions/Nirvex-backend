<x-layout bodyClass="g-sidenav-show  bg-gray-200">
   @section('title')
   {{ 'Create Service' }}
   @endsection
   <x-navbars.sidebar activePage='service'></x-navbars.sidebar>
   <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
      <x-navbars.navs.auth titlePage="Service"></x-navbars.navs.auth>
      <div class="container-fluid py-4" style="background-color:#fff">
         <div class="d-flex justify-content-between mb-2">
            <div class="pull-left">
               <h2>Create Service</h2>
            </div>
         </div>
         <div class="card">
            <div class="card-body">
               @if(session('success'))
               <div class="alert alert-success">
                  {{ session('success') }}
               </div>
               @endif
               <form method="POST" action="{{ route('users.update', $user->id) }}">
                  @csrf
                  @method('PUT')
                  <div class="row">
                     <div class="col-xs-6 col-sm-6 col-md-6">
                        <div class="form-group">
                           <strong>Name:</strong>
                           <input type="text" name="name" placeholder="Name" class="form-control" value="{{ $user->name }}">
                        </div>
                     </div>
                     <div class="col-xs-6 col-sm-6 col-md-6">
                        <div class="form-group">
                           <strong>Email:</strong>
                           <input type="email" name="email" placeholder="Email" class="form-control" value="{{ $user->email }}">
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
                     <div class="col-xs-6 col-sm-6 col-md-6">
                        <div class="form-group">
                           <strong>Role:</strong>
                           <select name="roles[]" class="form-control" multiple="multiple">
                           @foreach ($roles as $value => $label)
                           <option value="{{ $value }}" {{ isset($userRole[$value]) ? 'selected' : ''}}>
                           {{ $label }}
                           </option>
                           @endforeach
                           </select>
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