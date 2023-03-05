<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>Fingser - @yield('title')</title>
    
    <link rel="stylesheet" href="{{asset('assets/css/main/app.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/main/app-dark.css')}}">
    <link rel="shortcut icon" href="{{asset('assets/images/logo/favicon.svg')}}" type="image/x-icon">
    <link rel="shortcut icon" href="{{asset('assets/images/logo/favicon.png')}}" type="image/png">
    <link rel="stylesheet" href="{{asset('assets/css/shared/iconly.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/custom.css')}}">

    {{-- <link rel="stylesheet" href="{{asset('assets/css/pages/fontawesome.css')}}"> --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="{{asset('assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/pages/datatables.css')}}">

    {{-- toastr link --}}
    <link rel="stylesheet" type="text/css" href="{{ URL::to('https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css')}}">
    @yield('head')
</head>
<body>
    <div id="app">
        @include('admin.layout.sidebar') 
            @include('admin.layout.header')
        <div id="main">
            @yield('content')
            @include('admin.layout.footer')
        </div>
    </div>


    <script src="{{asset('assets/js/bootstrap.js')}}"></script>
    <script src="{{asset('assets/js/app.js')}}"></script>
    
    <!-- Need: Apexcharts -->
    <script src="{{asset('assets/extensions/apexcharts/apexcharts.min.js')}}"></script>
    <script src="{{asset('assets/js/pages/dashboard.js')}}"></script>

    <script src="{{asset('assets/extensions/jquery/jquery.min.js')}}"></script>
    <script src="https://cdn.datatables.net/v/bs5/dt-1.12.1/datatables.min.js"></script>
    <script src="{{asset('assets/js/pages/datatables.js')}}"></script>

    <script type="text/javascript" src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.7/jquery.validate.min.js"></script>

    {{-- toastr js link--}}
    <script src="{{ URL::to('https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js')}}"></script>

    {{-- sweetalert --}}
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    <script>
        $(document).ready(function() {
          toastr.options.timeOut = 10000;
          @if (Session::has('error'))
              toastr.error('{{ Session::get('error') }}');
          @elseif(Session::has('success'))
              toastr.success('{{ Session::get('success') }}');
          @elseif(Session::has('warning'))
              toastr.warning('{{ Session::get('warning') }}');
          @endif
      });
    </script>
    @stack('js')
</body>
</html>