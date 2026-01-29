<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admission - MHIS</title>


    <link rel="stylesheet" href="/assets/compiled/css/app.css">
    <link rel="stylesheet" href="/assets/compiled/css/iconly.css">
    <link rel="stylesheet" href="/assets/extensions/bootstrap-datepicker/css/bootstrap-datepicker.css">
    <link rel="stylesheet" href="/assets/extensions/bootstrap-datepicker/css/jquery.timepicker.css">
    <link rel="stylesheet" href="/assets/extensions/select2/dist/css/select2.min.css">
    <link rel="stylesheet" href="/assets/extensions/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="/assets/extensions/toastify-js/src/toastify.css">


    @yield('content-style')
</head>

<body>
    <div id="app">
        <div id="sidebar">
            <div class="sidebar-wrapper active">
                <div class="sidebar-header position-relative">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="logo">
                            <a href="/"><img src="/assets/compiled/svg/logo.svg" alt="Logo"
                                    srcset=""></a>
                        </div>

                        <div class="sidebar-toggler  x">
                            <a href="#" class="sidebar-hide d-xl-none d-block"><i
                                    class="bi bi-x bi-middle"></i></a>
                        </div>
                    </div>
                </div>
                <div class="sidebar-menu">
                    <ul class="menu">
                        <li class="sidebar-title">Menu</li>

                        <li class="sidebar-item {{ Request::is('/') ? 'active' : '' }} ">
                            <a href="/" class='sidebar-link'>
                                <i class="bi bi-grid-fill"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>

                        <li
                            class="sidebar-item has-sub {{ Request::is('level*') || Request::is('division*') ? 'active' : '' }}">
                            <a href="#" class='sidebar-link'>
                                <i class="fa fa-graduation-cap"></i>
                                <span>Level</span>
                            </a>
                            <ul class="submenu ">
                                <li class="submenu-item"><a href="/level" class="submenu-link">Level</a></li>
                                <li class="submenu-item"><a href="/division" class="submenu-link">Division</a>
                                </li>
                            </ul>
                        </li>
                        <li class="sidebar-item has-sub {{ Request::is('schoolvisit*') ? 'active' : '' }}">
                            <a href="#" class='sidebar-link'>
                                <i class="bi bi-building"></i>
                                <span>School Visit</span>
                            </a>
                            <ul class="submenu ">
                                <li class="submenu-item"><a href="/schoolvisit" class="submenu-link">List</a></li>
                                <li class="submenu-item"><a href="/schoolvisit/setting"
                                        class="submenu-link {{ Request::is('schoolvisit/setting') ? 'text-red' : '' }}">Settings</a>
                                </li>
                                <li class="submenu-item"><a target="blank" href="/schoolvisit-form"
                                        class="submenu-link">Form</a>
                                </li>
                            </ul>
                        </li>
                        <li
                            class="sidebar-item has-sub {{ Request::is('enrolment*') || Request::is('price*') ? 'active' : '' }}">
                            <a href="#" class='sidebar-link'>
                                <i class="bi bi-currency-exchange"></i>
                                <span>Enrolment</span>
                            </a>
                            <ul class="submenu ">
                                <li class="submenu-item"><a href="/enrolment" class="submenu-link">List</a></li>
                                <li class="submenu-item"><a href="/enrolment/setting"
                                        class="submenu-link {{ Request::is('enrolment/setting') ? 'text-red' : '' }}">Settings</a>
                                </li>
                                <li class="submenu-item"><a target="blank" href="/enrolment/form/"
                                        class="submenu-link">Enrolment Form</a>
                                </li>
                            </ul>
                        </li>
                        <li class="sidebar-item has-sub {{ Request::is('observation*') ? 'active' : '' }}">
                            <a href="#" class='sidebar-link'>
                                <i class="bi bi-stack"></i>
                                <span>Observation</span>
                            </a>
                            <ul class="submenu ">
                                <li class="submenu-item"><a href="/observation" class="submenu-link">user</a></li>
                                <li class="submenu-item"><a href="/observation/setting"
                                        class="submenu-link {{ Request::is('observation/setting') ? 'text-red' : '' }}">Settings</a>
                                </li>
                                <li class="submenu-item"><a target="blank" href="/observation-form"
                                        class="submenu-link">Form</a>
                                </li>
                            </ul>
                        </li>
                        <li class="sidebar-title">Setting</li>
                        <li class="sidebar-item has-sub {{ Request::is('setting*') ? 'active' : '' }}">
                            <a href="#" class='sidebar-link'>
                                <i class="fa fa-gear"></i>
                                <span>Setting</span>
                            </a>
                            <ul class="submenu ">
                                <li class="submenu-item"><a href="/setting/form" class="submenu-link">General</a>
                                </li>
                                <li class="submenu-item"><a href="/setting/password/change"
                                        class="submenu-link">Users</a></li>
                                <li class="submenu-item"><a href="/setting/password/change"
                                        class="submenu-link">Change
                                        Password</a></li>
                                <li class="submenu-item">
                                    <form action="/logout" method="POST">
                                        @csrf
                                        <button type="submit" style="text-decoration: none"
                                            class="submenu-link btn btn-link align-baseline">
                                            Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div id="main">
            <header class="mb-3">
                <a href="#" class="burger-btn d-block d-xl-none">
                    <i class="bi bi-justify fs-3"></i>
                </a>
            </header>

            <div class="page-heading">
                <h3>{{ $title ?? 'Title' }}</h3>
            </div>
            <div class="page-content">
                @yield('content-child')
            </div>

            <footer class="mt-auto">
                <div class="pull-right">
                    Copyright © 2025 - Admission <a href="https://mutiaraharapan.sch.id/">Mutiara Harapan Islamic
                        School</a>
                </div>
                <div class="clearfix"></div>
            </footer>
        </div>
    </div>
    <script src="/assets/compiled/js/app.js"></script>
    <script src="/assets/extensions/jquery/jquery.min.js"></script>
    <script src="/assets/extensions/jquery-blockUI/jquery.blockUI.js"></script>
    <script src="/assets/extensions/moment/moment.js"></script>
    <script src="/assets/extensions/select2/dist/js/select2.full.min.js"></script>
    <script src="/assets/extensions/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
    <script src="/assets/extensions/bootstrap-datepicker/js/jquery.timepicker.min.js"></script>
    <script src="/assets/extensions/toastify-js/src/toastify.js"></script>
    <script src="/assets/compiled/js/script.js?v=1.0.5"></script>
    @yield('content-script')

</body>

</html>
