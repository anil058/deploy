<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    {{-- Base Meta Tags --}}
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Title --}}
    <title>
        @yield('title_prefix', config('jumboconfig.title_prefix', ''))
        @yield('title', config('jumboconfig.title', 'Jumbocoder'))
        @yield('title_postfix', config('jumboconfig.title_postfix', ''))
    </title>

    @include('partials.headincluds')

    {{-- Custom stylesheets --}}
	@yield('custom_css')

</head>
<body>


    <!-- Main navbar -->
	@include('partials.navtopbar')
    <!-- /main navbar -->

	<!-- Page content -->
	<div class="page-content">

		<!-- Main sidebar -->
        @include('partials.sidebar')
		<!-- /main sidebar -->


		<!-- Main content -->
		<div class="content-wrapper">

			<!-- Page header -->
            @include('partials.pageheader')
			<!-- /page header -->


			<!-- Content area -->
            @yield('content-area')
			<!-- /content area -->


			<!-- Footer -->
			@include('partials.footer')
			<!-- /footer -->

		</div>
		<!-- /main content -->

	</div>
	<!-- /page content -->

</body>
</html>
