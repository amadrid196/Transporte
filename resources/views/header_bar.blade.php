<header class="main-header">
    <!-- Logo -->
    <div href="#" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <!-- <span class="logo-mini"><b>M</b>T</span> -->
        <!-- logo for regular state and mobile devices -->
        <!-- <span class="logo-lg"><img style="height: 32px;" src="{{asset("data_theme/img/logo.png")}}" alt="Logo"> -->
        <!-- <a href="http://localhost/truc/logout" class="logo-mini-Logout-btn float-right"><i class="fa fa-sign-out"></i></a>
        <a href="http://localhost/truc/profile" class="logo-mini-profile-btn float-right"><i class="fa fa-user"></i></a> -->

        
    </span>
    
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </a>
        
        
        
        
    </li>

</div>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </a>

      
    </nav>
</header>
<style>
#custom_menu{    
    position: absolute;
    top: 0px;
    right: 0px;
    z-index: 1500;
}
#custom_menu .user-header{
    background-color: #3c8dbc;
}
@media only screen and (max-width: 768px) {
    #custom_menu {
    display: none;
  }
}
</style>
<div class="navbar-custom-menu" id="custom_menu" >
            <ul class="nav navbar-nav">
                <!-- User Account: style can be found in dropdown.less -->
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <img src="{{ asset('data_theme/dist/img/user2-160x160.jpg') }}" class="user-image" alt="User Image">
                        <span class="hidden-xs text-capitalize"> <b>{{auth()->user()->name}}</b></span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header">
                           <img src="{{ asset('data_theme/dist/img/user2-160x160.jpg') }}" class="img-circle" alt="User Image">
                            <p class="text-capitalize">
                                {{auth()->user()->name}}
                                <small>{{__("tran.Member since")}}  {{Carbon\Carbon::parse(auth()->user()->created_at)->format("d/m/Y")}}</small>
                            </p>
                        </li>
                      
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-left">
                                <a href="{{route("profile")}}" class="btn btn-default btn-flat">{{__("tran.Profile")}}</a>
                            </div>
                            <div class="pull-right">
                                <a href="{{route("logout")}}" class="btn btn-default btn-flat">{{__("tran.Logout")}}</a>
                            </div>
                        </li>
                    </ul>
                </li>
                <!-- Control Sidebar Toggle Button -->
            </ul>
        </div>