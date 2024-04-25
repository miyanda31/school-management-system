<!DOCTYPE html>
<html>
<head>
    <!-- Basic Page Info -->
    <meta charset="utf-8">
    <title>Eduket - {{$title??'Home'}}</title>

    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="{{asset('css/core.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('css/icon-font.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('css/style.min.css')}}">

    @yield('header')

</head>
<body class="{{$page??'page'}}">
<div class="login-header">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <div class="brand-logo">
            <a href="{{route('home')}}">
                <img src="{{asset('img/logots.png')}}" alt="">
            </a>
        </div>
    </div>
</div>
    @yield('content')

    @yield('footer')
</body>
</html>
