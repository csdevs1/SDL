<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    @if(Auth::guest())
        @if(Route::current()->getName() != 'login')
            <script>
                window.location="/login";
            </script>
        @endif
    @endif
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('images/favicon.ico') }}">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SDL | Open Wireless Laboratories</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.0/css/bootstrap-datepicker.css" >
    <!-- Bootstrap Material CSS
    <link href="{{ asset('css/bootstrap-material-design.min.css') }}" rel="stylesheet">-->
    <link href="{{ asset('css/ripples.min.css') }}" rel="stylesheet">
    <!-- SweetAlert CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css" rel="stylesheet">
    <!-- IonIcons -->
    <link href="http://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <!-- main CSS -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Comfortaa" rel="stylesheet">
</head>
    <body>
        <div id="app">
            @if(Route::current()->getName() != 'login')
                @include('layouts.nav')
            @endif
            <section class="content">
                @yield('content')
            </section>
        </div>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
        <script src="https://code.highcharts.com/highcharts.js"></script>
        <script src="{{ asset('js/material.min.js') }}"></script>
        <script src="{{ asset('js/ripples.min.js') }}"></script>
        <script>
            $().ready(function(){
                var current_path_url=window.location.pathname;
                $('a[href="'+current_path_url+'"]').parent().addClass('active');
                if($('a[href="'+current_path_url+'"]').parent().parent().parent().hasClass('dropdown-submenu')){
                    $('a[href="'+current_path_url+'"]').parent().parent().parent().addClass('active');
                    $('a[href="'+current_path_url+'"]').parent().parent().parent().parent().parent().addClass('active');
                }else if($('a[href="'+current_path_url+'"]').parent().parent().parent().hasClass('dropdown'))
                    $('a[href="'+current_path_url+'"]').parent().parent().parent().addClass('active');
            });
        </script>
    </body>
</html>
