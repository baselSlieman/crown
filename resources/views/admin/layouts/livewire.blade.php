<!DOCTYPE html>
<html lang="{{ session('locale') }}" @if (session('locale')==='ar')
    dir="rtl"
@endif data-bs-theme="{{session('mode')}}">

<head>
    <meta charset="utf-8">
    <title>@yield('title')</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">
    {{-- <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests"> --}}
    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">



    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="{{asset('dashboard/lib/owlcarousel/assets/owl.carousel.min.css')}}" rel="stylesheet">
    <link href="{{asset('dashboard/lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css')}}" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    {{-- <link href="{{asset('dashboard/css/bootstrap.min.css')}}" rel="stylesheet"> --}}


    @if (session('locale')==='ar')
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.rtl.min.css" integrity="sha384-q8+l9TmX3RaSz3HKGBmqP2u5MkgeN7HrfOJBLcTgZsQsbrx8WqqxdA5PuwUV9WIx" crossorigin="anonymous">
        @if(session('mode')==='dark')
        <link id="dark-mode-stylesheet" href="{{asset('dashboard/css/styleAr-dark.css')}}" rel="stylesheet">
        @else
        <link id="dark-mode-stylesheet" href="{{asset('dashboard/css/styleAr.css')}}" rel="stylesheet">
        @endif
    @else
        <!-- Google Web Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600;700&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
        @if(session('mode')==='dark')
        <link  id="dark-mode-stylesheet" href="{{asset('dashboard/css/style-dark.css')}}" rel="stylesheet">
        @else
        <link  id="dark-mode-stylesheet" href="{{asset('dashboard/css/style.css')}}" rel="stylesheet">
        @endif
    @endif
    <!-- Template Stylesheet -->

    @livewireStyles
</head>

<body>

    <div class="container-fluid position-relative bg-white d-flex p-0">
        <!-- Spinner Start -->
        <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <!-- Spinner End -->

        <!-- Sidebar Start -->
        <div class="sidebar pe-2 pb-3">
            <nav class="navbar bg-light navbar-light">
                <a href="{{route('admin_dashboard')}}" class="navbar-brand mx-4 mb-3">
                    <h3 class="text-primary"><img src="{{asset('dashboard/img/logo.png')}}" style="width: 100%"/></h3>

                </a>
                <div class="d-flex align-items-center ms-4 mb-4">
                    <div class="position-relative">
                        <img class="rounded-circle" src="{{asset('dashboard/img/user.jpg')}}" alt="" style="width: 40px; height: 40px;">
                        <div class="bg-success rounded-circle border border-2 border-white position-absolute end-0 bottom-0 p-1"></div>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-0">{{Auth::user()->name}}</h6>
                        <span>{{__(Auth::user()->role)}}</span>
                    </div>
                </div>
                <div class="navbar-nav w-100">
                    <a href="{{route('admin_dashboard')}}" class="nav-item nav-link" wire:navigate wire:current="active"><i class="fa fa-tachometer-alt me-2"></i>@lang('Dashboard')</a>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle show" data-bs-toggle="dropdown"><i class="fab fa-telegram me-2"></i> @lang('Telegram')</a>
                        <div class="dropdown-menu bg-transparent border-0 show">
                            <a href="{{route('chats.index')}}" class="dropdown-item text-primary-emphasis border-bottom" wire:navigate wire:current="active text-light"><i class="fab fa-telegram-plane me-2"></i>@lang('chats')</a>
                            <a href="{{route('charges.index')}}" class="dropdown-item text-primary-emphasis border-bottom" wire:navigate wire:current="active  text-light"><i class="fas fa-download me-2"></i>@lang('Chrges')</a>
                            <a href="{{route('withdraws.index')}}" class="dropdown-item text-primary-emphasis border-bottom" wire:navigate  wire:current="active  text-light"><i class="fas fa-upload me-2"></i>@lang('Withdraws')</a>
                            <a href="{{route('ichancies.index')}}" class="dropdown-item text-primary-emphasis border-bottom" wire:navigate  wire:current="active  text-light"><i class="fas fa-rocket me-2"></i>@lang('Ichancy')</a>
                            <a href="{{ route('gifts') }}" class="dropdown-item text-primary-emphasis border-bottom" wire:navigate  wire:current="active  text-light"><i class="fas fa-gift me-2"></i>@lang('Gifts')</a>
                            <a href="{{ route('affiliates') }}" class="dropdown-item text-primary-emphasis border-bottom" wire:navigate  wire:current="active  text-light"><i class="fas fa-percent me-2"></i>@lang('Affiliates')</a>
                            <a href="{{ route('cashbacks') }}" class="dropdown-item text-primary-emphasis border-bottom" wire:navigate  wire:current="active  text-light"><i class="fas fa-retweet me-2"></i>@lang('Cashback')</a>
                            <a href="{{ route('syriatelcash') }}" class="dropdown-item text-primary-emphasis border-bottom" wire:navigate  wire:current="active  text-light"><i class="fas fa-qrcode me-2"></i>@lang('SyriatelCash')</a>
                            <a href="{{ route('transfers') }}" class="dropdown-item text-primary-emphasis border-bottom" wire:navigate  wire:current="active  text-light"><i class="fas fa-compress-arrows-alt me-2"></i>@lang('Transfers')</a>
                            <a href="{{ route('wheels') }}" class="dropdown-item text-primary-emphasis border-bottom" wire:navigate  wire:current="active  text-light"><i class="fas fa-podcast me-2"></i>@lang('wheels')</a>
                            <a href="{{ route('broadcasts') }}" class="dropdown-item text-primary-emphasis border-bottom" wire:navigate  wire:current="active  text-light"><i class="fas fa-broadcast-tower me-2"></i>@lang('Broadcast')</a>
                            <a href="{{ route('settings') }}" class="dropdown-item text-primary-emphasis border-bottom" wire:navigate  wire:current="active  text-light"><i class="fas fa-tools me-2"></i>@lang('Settings')</a>
                        </div>
                    </div>
                    <a href="{{route('categories.index')}}" class="nav-item nav-link"><i class="fa fa-th me-2"></i>@lang('Categories')</a>
                    <a href="{{route('products.index')}}" class="nav-item nav-link"><i class="fa fa-table me-2"></i>@lang('Products')</a>
                    {{-- <a href="form.html" class="nav-item nav-link"><i class="fa fa-keyboard me-2"></i>Forms</a>
                    <a href="chart.html" class="nav-item nav-link"><i class="fa fa-chart-bar me-2"></i>Charts</a>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="far fa-file-alt me-2"></i>Pages</a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="signin.html" class="dropdown-item">Sign In</a>
                            <a href="signup.html" class="dropdown-item">Sign Up</a>
                            <a href="404.html" class="dropdown-item">404 Error</a>
                            <a href="blank.html" class="dropdown-item">Blank Page</a>
                        </div>
                    </div> --}}
                </div>
            </nav>
        </div>
        <!-- Sidebar End -->

<!-- Content Start -->
<div class="content">
    <!-- Navbar Start -->
    <nav class="navbar navbar-expand bg-light navbar-light sticky-top px-4 py-0">
        <a href="{{route('admin_dashboard')}}" class="navbar-brand d-flex d-lg-none me-4">
            <h2 class="text-primary mb-0"><img src="{{asset('dashboard/img/tree.png')}}" width="30"/></h2>
        </a>
        <a href="#" class="sidebar-toggler flex-shrink-0 text-decoration-none">
            <i class="fas fa-bars"></i>
        </a>
        <form class="d-none d-md-flex ms-4">
            <input class="form-control border-0" type="search" placeholder="@lang('Search')">
        </form>
        <div class="navbar-nav align-items-center ms-auto">
                @livewire('night')
                @livewire('language')




            {{-- <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fa fa-envelope me-lg-2"></i>
                    <span class="d-none d-lg-inline-flex">Message</span>
                </a>
                <div class="dropdown-menu dropdown-menu-end bg-light border-0 rounded-0 rounded-bottom m-0">
                    <a href="#" class="dropdown-item">
                        <div class="d-flex align-items-center">
                            <img class="rounded-circle" src="{{asset('dashboard/img/user.jpg')}}" alt="" style="width: 40px; height: 40px;">
                            <div class="ms-2">
                                <h6 class="fw-normal mb-0">Jhon send you a message</h6>
                                <small>15 minutes ago</small>
                            </div>
                        </div>
                    </a>
                    <hr class="dropdown-divider">
                    <a href="#" class="dropdown-item">
                        <div class="d-flex align-items-center">
                            <img class="rounded-circle" src="{{asset('dashboard/img/user.jpg')}}" alt="" style="width: 40px; height: 40px;">
                            <div class="ms-2">
                                <h6 class="fw-normal mb-0">Jhon send you a message</h6>
                                <small>15 minutes ago</small>
                            </div>
                        </div>
                    </a>
                    <hr class="dropdown-divider">
                    <a href="#" class="dropdown-item">
                        <div class="d-flex align-items-center">
                            <img class="rounded-circle" src="{{asset('dashboard/img/user.jpg')}}" alt="" style="width: 40px; height: 40px;">
                            <div class="ms-2">
                                <h6 class="fw-normal mb-0">Jhon send you a message</h6>
                                <small>15 minutes ago</small>
                            </div>
                        </div>
                    </a>
                    <hr class="dropdown-divider">
                    <a href="#" class="dropdown-item text-center">See all message</a>
                </div>
            </div> --}}
            {{-- <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fa fa-bell me-lg-2"></i>
                    <span class="d-none d-lg-inline-flex">Notificatin</span>
                </a>
                <div class="dropdown-menu dropdown-menu-end bg-light border-0 rounded-0 rounded-bottom m-0">
                    <a href="#" class="dropdown-item">
                        <h6 class="fw-normal mb-0">Profile updated</h6>
                        <small>15 minutes ago</small>
                    </a>
                    <hr class="dropdown-divider">
                    <a href="#" class="dropdown-item">
                        <h6 class="fw-normal mb-0">New user added</h6>
                        <small>15 minutes ago</small>
                    </a>
                    <hr class="dropdown-divider">
                    <a href="#" class="dropdown-item">
                        <h6 class="fw-normal mb-0">Password changed</h6>
                        <small>15 minutes ago</small>
                    </a>
                    <hr class="dropdown-divider">
                    <a href="#" class="dropdown-item text-center">See all notifications</a>
                </div>
            </div> --}}
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                    <img class="rounded-circle me-lg-2" src="{{asset('dashboard/img/user.jpg')}}" alt="" style="width: 40px; height: 40px;">
                    <span class="d-none d-lg-inline-flex">{{Auth::user()->name}}</span>
                </a>
                <div class="dropdown-menu dropdown-menu-end bg-light border-0 rounded-0 rounded-bottom m-0">
                    <a href="{{ route('userprofile') }}" class="dropdown-item">@lang('My Profile')</a>
                    <a href="{{ route('settings') }}" class="dropdown-item">@lang('Settings')</a>
                    <a href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();" class="dropdown-item">@lang('Log Out')</a>
                                                     <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                </div>
            </div>
        </div>
    </nav>
    <!-- Navbar End -->

{{ $slot }}

        </div>
        <!-- Content End -->


        <!-- Back to Top -->
        <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
    </div>

    <!-- JavaScript Libraries -->

    <script src="{{asset('dashboard/js/jquery-3.7.1.min.js')}}"></script>
    <script data-navigate-once src="{{asset('dashboard/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('dashboard/lib/chart/chart.min.js')}}"></script>
    <script src="{{asset('dashboard/lib/easing/easing.min.js')}}"></script>
    <script src="{{asset('dashboard/lib/waypoints/waypoints.min.js')}}"></script>
    <script src="{{asset('dashboard/lib/owlcarousel/owl.carousel.min.js')}}"></script>
    <script src="{{asset('dashboard/lib/tempusdominus/js/moment.min.js')}}"></script>
    <script src="{{asset('dashboard/lib/tempusdominus/js/moment-timezone.min.js')}}"></script>
    <script src="{{asset('dashboard/lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js')}}"></script>
    <!-- Template Javascript -->
    <script src="{{asset('dashboard/js/main.js')}}"></script>

    @livewireScripts
    <script>
        window.addEventListener('change-theme',(event)=>{
            window.location.reload();
        });
    </script>
</body>

</html>
