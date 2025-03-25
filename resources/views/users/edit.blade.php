<x-layout bodyClass="g-sidenav-show  bg-gray-200">
   @section('title')
   {{ 'Edit Seller' }}
   @endsection
   <x-navbars.sidebar activePage='seller'></x-navbars.sidebar>
   <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
      <x-navbars.navs.auth titlePage="Seller"></x-navbars.navs.auth>
      <div class="container-fluid py-4" style="background-color:#fff">
         <div class="d-flex justify-content-between mb-2">
            <div class="pull-left">
               <h2>Edit Seller</h2>
            </div>
         </div>
         <div class="card">
            <div class="card-body">
               @if(session('success'))
               <div class="alert alert-success">
                  {{ session('success') }}
               </div>
               @endif
               @if($errors->any())
               <div class="alert alert-danger">
                  <ul>
                     @foreach($errors->all() as $error)
                     <li>{{ $error }}</li>
                     @endforeach
                  </ul>
               </div>
               @endif
               <form method="POST" action="{{ route('users.update', $user->id) }}">
                  @csrf
                  @method('PUT')
                  <div class="row">
                     <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                           <strong>Name:</strong>
                           <input type="text" name="name" placeholder="Name" class="form-control" value="{{ $user->first_name }}">
                        </div>
                     </div>
                     <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                           <strong>Email:</strong>
                           <input type="email" name="email" placeholder="Email" class="form-control" value="{{ $user->email }}">
                        </div>
                     </div>
                     <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                           <strong>Password:</strong>
                           <input type="password" name="password" placeholder="Password" class="form-control">
                        </div>
                     </div>
                     <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                           <strong>Confirm Password:</strong>
                           <input type="password" name="confirm-password" placeholder="Confirm Password" class="form-control">
                        </div>
                     </div>
                     <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                           <strong>Role:</strong>
                           <select name="roles[]" class="form-control" multiple="multiple">
                           @foreach ($roles as $value => $label)
                           <option value="{{ $value }}" {{ in_array($value, $userRole) ? 'selected' : '' }}>
                           {{ $label }}
                           </option>
                           @endforeach
                           </select>
                        </div>
                     </div>
                     <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                        <button type="submit" class="btn btn-primary btn-sm mt-2 mb-3"><i class="fa-solid fa-floppy-disk"></i> Update</button>
                     </div>
                  </div>
               </form>
            </div>
         </div>
      </div>
   </main>
   <x-plugins></x-plugins>
</x-layout>