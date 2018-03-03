<!doctype html>
<html lang="en">
<head>
    @include('layout.admin.include.head')
</head>
<body>

<div class="page-container">
    @include('layout.admin.include.sidebar')

    <!-- Page Content -->
    <div class="page-content">
        <!-- Page Header -->
        @include('layout.admin.include.header')
        <!-- /Page Header -->
        <!-- Page Inner -->
        <div class="page-inner">
            @include('layout.admin.include.breadcrumb')
            <div id="main-wrapper">
                <div class="row">
                    <div class="col-md-12">
                        @include('flash::message')
                    </div>
                </div>
                <div class="row">
                    @yield('main-content')
                </div><!-- Row -->
            </div><!-- Main Wrapper -->
            <div class="page-footer">
                <p>Made with <i class="fa fa-heart"></i> by stacks</p>
            </div>
        </div><!-- /Page Inner -->
    </div><!-- /Page Content -->
</div>
@include('layout.admin.include.footer')
</body>
</html>