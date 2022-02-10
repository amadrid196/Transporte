<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}"/>

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield("title")| {{ config('app.name', 'CallCenter') }}</title>
    @stack("header_top") {{--less priority css--}}
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="{{ asset('data_theme/bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('data_theme/bower_components/font-awesome/css/font-awesome.min.css') }}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="{{ asset('data_theme/bower_components/Ionicons/css/ionicons.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('data_theme/dist/css/AdminLTE.min.css') }}">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="{{ asset('data_theme/dist/css/skins/_all-skins.min.css') }}">
    
    <link href="{{ asset('css/waitMe.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    
    <link type="text/css" rel="stylesheet" href="{{ asset('css/image-uploader.css') }}">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- Google Font -->
{{--    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">--}}

<!-- jQuery 3 -->
    <script src="{{ asset('data_theme/bower_components/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('data_theme/plugins/jQuery.resizableColumns.js')}}"></script>
    @stack("header")
    <style>
        .user-panel > .info {
            position: initial;
        }
    </style>
</head>
<body class="hold-transition skin-blue sidebar-mini" id="container">
<!-- Site wrapper -->
<div class="wrapper">

@include("layouts.header_bar")

<!-- =============================================== -->

    <!-- Left side column. contains the sidebar -->
@include("layouts.left_side_bar")

<!-- =============================================== -->

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper" >
        <!-- Content Header (Page header) -->
         <section class="content-header">
            <div>
            <ol class="breadcrumb">
                <li><a href="{{url('/')}}"><i class="fa fa-home"></i> Home</a></li>
                <?php $segments = ''; ?>
                @foreach(Request::segments() as $key=>$segment)
                    <?php $segments .= '/'.$segment;
                    $breadCrumbList = [];
                    
                    $breadCrumbList['home'] = "DashBoard";

                    $breadCrumbList['loads-index'] = "Loads";
                    $breadCrumbList['loads-create'] = " Create Loads";
                    $breadCrumbList['loads-edit'] = " Edit Loads";
                    
                    $breadCrumbList['smstemplate'] = "Edit SMS";

                    $breadCrumbList['drivers-index'] = "Drivers";
                    $breadCrumbList['drivers-create'] = "Create Driver";
                    $breadCrumbList['drivers-edit'] = " Edit Driver";
                    
                    $breadCrumbList['customers-index'] = "Locations";
                    $breadCrumbList['customers-create'] = "Add New Location";
                    $breadCrumbList['customers-edit'] = "Edit Location";
                    
                    $breadCrumbList['tractors-index'] = "Tractors";
                    $breadCrumbList['tractors-create'] = "Create Tractor";
                    $breadCrumbList['tractors-edit'] = "Edit Tractor";

                    
                    $breadCrumbList['trailers-index'] = "Trailers";
                    $breadCrumbList['trailers-create'] = "Create Trailer";
                    $breadCrumbList['trailers-edit'] = " Edit Trailer";
                    
                    $breadCrumbList['brokers-index'] = "Brokers";
                    $breadCrumbList['brokers-create'] = "Create Broker";
                    $breadCrumbList['brokers-edit'] = " Edit Broker";
                    
                    $breadCrumbList['drivers-rate'] = "Driver Rate";
                    $breadCrumbList['drivers-paymanagement'] = "Paymanagement";
                    
                    $breadCrumbList['listsettings-index'] = "List Management";
                    $breadCrumbList['listsettings-create'] = "Create ListSetting";
                    $breadCrumbList['listsettings-edit'] = " Edit ListSetting";
                    
                    $breadCrumbList['profile'] = "Profile";
                    
                    $breadCrumbList['stats'] = "Reporting";

                    $breadCrumbList['search-load'] = "Search Loads";
                    
                    ?>
                    <li>
                        @if($segment == 'loads-edit')
                        <a href="{{ url('loads-index') }}" ><b>{{isset($breadCrumbList[$segment])? $breadCrumbList[$segment] :$segment}}</b></a>
                        @elseif($segment == 'smstemplate')
                        <a href="#" ><b>{{isset($breadCrumbList[$segment])? $breadCrumbList[$segment] :$segment}}</b></a>
                        @elseif($segment == 'drivers-edit')
                        <a href="{{ url('drivers-index') }}" ><b>{{isset($breadCrumbList[$segment])? $breadCrumbList[$segment] :$segment}}</b></a>
                        @elseif($segment == 'brokers-edit')
                        <a href="{{ url('brokers-index') }}" ><b>{{isset($breadCrumbList[$segment])? $breadCrumbList[$segment] :$segment}}</b></a>
                        @elseif($segment == 'customers-edit')
                        <a href="{{ url('customers-index') }}" ><b>{{isset($breadCrumbList[$segment])? $breadCrumbList[$segment] :$segment}}</b></a>
                        @elseif($segment == 'tractors-edit')
                        <a href="{{ url('tractors-index') }}" ><b>{{isset($breadCrumbList[$segment])? $breadCrumbList[$segment] :$segment}}</b></a>
                        @elseif($segment == 'trailers-edit')
                        <a href="{{ url('trailers-index') }}" ><b>{{isset($breadCrumbList[$segment])? $breadCrumbList[$segment] :$segment}}</b></a>
                        @elseif($segment == 'brokers-edit')
                        <a href="{{ url('brokers-index') }}" ><b>{{isset($breadCrumbList[$segment])? $breadCrumbList[$segment] :$segment}}</b></a>
                        @elseif($segment == 'listsettings-edit')
                        <a href="{{ url('listsettings-index') }}" ><b>{{isset($breadCrumbList[$segment])? $breadCrumbList[$segment] :$segment}}</b></a>
                        @else
                        <a href="{{$segments}}" @if($key == (count(Request::segments())-1)) class="active" @endif><b>{{isset($breadCrumbList[$segment])? $breadCrumbList[$segment] :$segment}}</b></a>
                        @endif
                        
                    </li>
                @endforeach
            </ol>
            <div>
        </section>
    <!-- Main content -->
        <section class="content">

            <!-- Default box -->
        @yield('content')
        <!-- /.box -->

        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    @include("layouts.footer")

    @include("layouts.right_side_bar")
</div>
<!-- ./wrapper -->

@include("layouts.error_messages")



<!-- Bootstrap 3.3.7 -->
<script src="{{ asset('data_theme/bower_components/bootstrap/dist/js/bootstrap.js') }}"></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.1/js/bootstrap.min.js"></script> -->
<!-- SlimScroll -->
<script src="{{ asset('data_theme/bower_components/jquery-slimscroll/jquery.slimscroll.min.js') }}"></script>
<!-- FastClick -->
<script src="{{ asset('data_theme/bower_components/fastclick/lib/fastclick.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('data_theme/dist/js/adminlte.min.js') }}"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{ asset('data_theme/dist/js/demo.js') }}"></script>

<!-- Jquery Loader -->
<script src="{{ asset('/js/waitMe.min.js') }}"></script>
<script src="{{ asset('js/image-uploader.js') }}"></script>
<script>
    function hideLoading()
    {
        $('#container').waitMe('hide');
	}

    function showLoading()
    {
        $('#container').waitMe({
            effect : 'roundBounce',
            text : 'Loading ...',
            bg : 'rgba(255,255,255,0.7)',
            color : '#000'
        });
    }

    $(document).ready(function () {
        $('.sidebar-menu').tree();
        
    })

    $(document).on("click", ".loading-btn", function(){
        showLoading()
    })
</script>

@stack("footer")

</body>
</html>
