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
        @include('main-layout.sidebar')
        <div id="main">
            <header class="mb-3">
                <a href="#" class="burger-btn d-block d-xl-none">
                    <i class="bi bi-justify fs-3"></i>
                </a>
            </header>

            <div>
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
