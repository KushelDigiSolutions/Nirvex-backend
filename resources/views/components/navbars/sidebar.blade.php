@props(['activePage'])

<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3   bg-gradient-dark"
    id="sidenav-main">
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-white opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
            aria-hidden="true" id="iconSidenav"></i>
        <!-- <a class="navbar-brand m-0 d-flex text-wrap align-items-center" href=" {{ route('admin.dashboard') }} ">
            <img src="{{ asset('assets') }}/img/logo-ct.png" class="navbar-brand-img h-100" alt="main_logo">
            <span class="ms-2 font-weight-bold text-white">Material Dashboard 2 Laravel</span>
        </a> -->
    </div>
    <hr class="horizontal light mt-0 mb-2">
    <div class="collapse navbar-collapse  w-auto  max-height-vh-100" id="sidenav-collapse-main">
        <ul class="navbar-nav">
            <!-- <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs text-white font-weight-bolder opacity-8">Laravel examples</h6>
            </li> -->
            <li class="nav-item">
                <a class="nav-link text-white {{ $activePage == 'dashboard' ? ' active bg-gradient-primary' : '' }} "
                    href="{{ route('admin.dashboard') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">dashboard</i>
                    </div>
                    <span class="nav-link-text ms-1">Dashboard</span>
                </a>
            </li>
             <li class="nav-item">
                <a class="nav-link text-white {{ $activePage == 'notification' ? ' active bg-gradient-primary' : '' }} "
                    href="{{ route('admin.notification') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">Notification</i>
                    </div>
                    <span class="nav-link-text ms-1">Note</span>
                </a>
            </li>
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs text-white font-weight-bolder opacity-8">Inventory Management</h6>
            </li>

            <li class="nav-item">
                <a class="nav-link text-white {{ $activePage == 'category' ? 'active bg-gradient-primary' : '' }} "
                    href="{{ route('categories.index') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i style="font-size: 1.2rem;" class="fa fa-list-alt ps-2 pe-2 text-center"></i>
                    </div>
                    <span class="nav-link-text ms-1">Category</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link text-white {{ $activePage == 'sub-category' ? 'active bg-gradient-primary' : '' }} "
                    href="{{ route('subcategories.index') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i style="font-size: 1.2rem;" class="fa fa-list ps-2 pe-2 text-center"></i>
                    </div>
                    <span class="nav-link-text ms-1">Sub Category</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link text-white {{ $activePage == 'products' ? 'active bg-gradient-primary' : '' }} "
                    href="{{ route('products.index') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                    <i style="font-size: 1.2rem;" class="fas fa-hard-hat ps-2 pe-2 text-center"></i>
                    </div>
                    <span class="nav-link-text ms-1">Product</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{ $activePage == 'services' ? 'active bg-gradient-primary' : '' }} "
                    href="{{ route('services.index') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i style="font-size: 1.2rem;" class="fa fa-tools ps-2 pe-2 text-center"></i>
                    </div>
                    <span class="nav-link-text ms-1">Services</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link text-white {{ $activePage == 'products' ? 'active bg-gradient-primary' : '' }} "
                    href="{{ route('pricings.index') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i style="font-size: 1.2rem;" class="fas fa-rupee-sign ps-2 pe-2 text-center"></i>
                    </div>
                    <span class="nav-link-text ms-1">Pricings</span>
                </a>
            </li>

            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs text-white font-weight-bolder opacity-8">Order Management</h6>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{ $activePage == 'orders' ? 'active bg-gradient-primary' : '' }} "
                    href="{{ route('orders.index') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i style="font-size: 1.2rem;" class="fa fa-box ps-2 pe-2 text-center"></i>
                    </div>
                    <span class="nav-link-text ms-1">Orders</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link text-white {{ $activePage == 'customers' ? 'active bg-gradient-primary' : '' }} "
                    href="{{ route('customers.index') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i style="font-size: 1.2rem;" class="fas fa-users ps-2 pe-2 text-center"></i>
                    </div>
                    <span class="nav-link-text ms-1">Customers</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link text-white {{ $activePage == 'sellers' ? 'active bg-gradient-primary' : '' }} "
                    href="{{ route('sellers.index') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i style="font-size: 1.2rem;" class="fas fa-user-tie ps-2 pe-2 text-center"></i>
                    </div>
                    <span class="nav-link-text ms-1">Sellers</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link text-white {{ $activePage == 'clients' ? 'active bg-gradient-primary' : '' }} "
                    href="{{ route('clients.index') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i style="font-size: 1.2rem;" class="fas fa-user-secret ps-2 pe-2 text-center"></i>
                    </div>
                    <span class="nav-link-text ms-1">Staff Admin</span>
                </a>
            </li>

           <!--  <li class="nav-item">
                <a class="nav-link text-white {{ $activePage == 'products' ? 'active bg-gradient-primary' : '' }} "
                    href="{{ route('products.index') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i style="font-size: 1.2rem;" class="fas fa-user-circle ps-2 pe-2 text-center"></i>
                    </div>
                    <span class="nav-link-text ms-1">Transactions</span>
                </a>
            </li> -->


            
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs text-white font-weight-bolder opacity-8">Account Management</h6>
            </li>
           <!--  <li class="nav-item">
                <a class="nav-link text-white {{ $activePage == 'user-profile' ? 'active bg-gradient-primary' : '' }} "
                    href="{{ route('profile.edit') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i style="font-size: 1.2rem;" class="fas fa-user-circle ps-2 pe-2 text-center"></i>
                    </div>
                    <span class="nav-link-text ms-1">User Profile</span>
                </a>
            </li> -->
            <li class="nav-item">
                <a class="nav-link text-white {{ $activePage == 'user-management' ? ' active bg-gradient-primary' : '' }} "
                    href="{{ route('roles.index') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i style="font-size: 1rem;" class="fas fa-lg fa-list-ul ps-2 pe-2 text-center"></i>
                    </div>
                    <span class="nav-link-text ms-1">User Management</span>
                </a>
            </li>
           

            <!-- <li class="nav-item">
                <a class="nav-link text-white " href="">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">login</i>
                    </div>
                    <span class="nav-link-text ms-1">Sign out</span>
                </a>
            </li> -->
         
        </ul>
    </div>
</aside>
