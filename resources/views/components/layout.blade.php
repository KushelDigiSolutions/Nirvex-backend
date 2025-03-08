
@props(['bodyClass'])
<!DOCTYPE html>
<html lang="en">
<head>
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('assets') }}/img/apple-icon.png">
    <link rel="icon" type="image/png" href="{{ asset('assets') }}/img/favicon.png">
    <!-- <title>Nirviex Login</title> -->
    <title> @yield('title', 'Admin') | {{ config('app.name') }}</title>
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900|Roboto+Slab:400,700" />
    <link href="{{ asset('assets') }}/css/nucleo-icons.css" rel="stylesheet" />
    <link href="{{ asset('assets') }}/css/nucleo-svg.css" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <link id="pagestyle" href="{{ asset('assets') }}/css/material-dashboard.css?v=3.0.0" rel="stylesheet" />
    <link href="{{ asset('assets') }}/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('assets') }}/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
  
    <!-- jQuery (required by DataTables) -->
    <script src="{{ asset('assets') }}/js/jquery-3.6.0.min.js"></script>

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="{{ asset('assets') }}/css/jquery.dataTables.min.css">

    <!-- DataTables JavaScript -->
    <script src="{{ asset('assets') }}/js/jquery.dataTables.min.js"></script>

    <!-- Responsive Extension CSS -->
    <link rel="stylesheet" href="{{ asset('assets') }}/css/responsive.dataTables.min.css">

    <!-- Responsive Extension JavaScript -->
    <script src="{{ asset('assets') }}/js/dataTables.responsive.min.js"></script>
    <!-- Place the first <script> tag in your HTML's <head> -->
<script src="{{ asset('assets') }}/tinymce.min.js" referrerpolicy="origin"></script>



</head>
<body class="{{ $bodyClass }}">

{{ $slot }}

<script src="{{ asset('assets') }}/js/popper.min.js"></script>
<script src="{{ asset('assets') }}/js/bootstrap.min.js"></script>
<script src="{{ asset('assets') }}/js/perfect-scrollbar.min.js"></script>
<script src="{{ asset('assets') }}/js/smooth-scrollbar.min.js"></script>
@stack('js')
<script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
        var options = {
            damping: '0.5'
        }
        Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }

</script>
<script async defer src="{{ asset('assets') }}/js/buttons.js"></script>
<script src="{{ asset('assets') }}/js/material-dashboard.min.js?v=3.0.0"></script>
</body>
</html>
