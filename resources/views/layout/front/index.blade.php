<!doctype html>
<html lang="en">
<head>
    @include('layout.admin.include.head')
</head>
<body>

<div class="page-container page-error">
    <div class="page-content">
        <!-- Page Inner -->
        <div class="page-inner">
            <div id="main-wrapper" class="container-fluid">
                @yield('main-content')
            </div><!-- Main Wrapper -->
        </div><!-- /Page Inner -->
    </div><!-- /Page Content -->
</div>
@include('layout.admin.include.footer')
</body>
</html>