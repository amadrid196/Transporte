<style>

.fa-angle-left:before{
    content: "\f0d7";
} 
.sidebar-menu .menu-open>a>.fa-angle-left, .sidebar-menu .menu-open>a>.pull-right-container>.fa-angle-left {
    -webkit-transform: rotate(
-180deg
);
    -ms-transform: rotate(-180deg);
    -o-transform: rotate(-180deg);
    transform: rotate(
-180deg
);

}
aside {
    background: rgb(17,27,84);
    background: url("{{ asset('/data_theme/img/side.png') }}") no-repeat , linear-gradient(
    180deg, rgba(17,27,84) 0%, rgba(24,34,89,1) 66%);
    background-position: center 387px, center;
}
</style>
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            
            <span class="logo-lg"><img style="height: 80px;" src="{{asset("data_theme/img/logo1.png")}}" alt="Logo">
        </div>

        <ul class="sidebar-menu" data-widget="tree">

            <li class="@if( isset($page) && $page === 'home') active @endif"><a href="{{route("home")}}"><i class="fa fa-dashboard"></i> <span>{{__("tran.Dashboard")}}</span></a></li>
            <!-- data-widget="treeview" -->
            <!-- <li ><a href="{{route("loads-index")}}"></li> -->
            
                
            <li class="treeview @if( isset($page) && ($page === 'loads-index' ||  $page === 'loads-create' ||  $page === 'search-load')) active @endif">
                <a href="#"><i class="fa fa-list-alt"></i> <span>{{__("tran.Loads")}} </span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li class="@if( isset($page) && $page === 'loads-index') active @endif loading-btn"><a href="{{route('loads-index')}}"><i class="fa fa-chevron-circle-right"></i> {{__('tran.View Loads')}}</a></li>
                    <li class="@if( isset($page) && $page === 'search-load') active @endif loading-btn"><a href="{{route('search-load-index')}}"><i class="fa fa-chevron-circle-right"></i> {{__('tran.Search Loads')}}</a></li>
                    <li class="@if( isset($page) && $page === 'loads-create') active @endif loading-btn"><a href="{{route('loads-create')}}"><i class="fa fa-chevron-circle-right"></i> {{__('tran.Build a Load')}}</a></li>
                    <!-- <li class="@if( isset($page) && $page === 'loads-index') active @endif loading-btn"><a href="{{route('loads-index')}}"><i class="fa fa-chevron-circle-right"></i> {{__('tran.Transit Insights')}}</a></li> -->
                    
                </ul>
            </li>
            <li class="treeview @if( isset($page) && ($page === 'drivers-index' ||  $page === 'trailers-index' ||  $page === 'tractors-index')) active @endif">
                <a href="#"><i class="fa fa-users"></i> <span>{{__("tran.Drivers")}} </span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li class="@if( isset($page) && $page === 'drivers-index') active @endif loading-btn"><a href="{{route("drivers-index")}}"> <span><i class="fa fa-chevron-circle-right"></i> {{__("View Drivers")}}</span></a></li>
                    <li class="@if( isset($page) && $page === 'trailers-index') active @endif loading-btn"><a href="{{route("trailers-index")}}"> <span><i class="fa fa-chevron-circle-right"></i> {{__("Trailers")}}</span></a></li>
                    <li class="@if( isset($page) && $page === 'tractors-index') active @endif loading-btn"><a href="{{route("tractors-index")}}"> <span><i class="fa fa-chevron-circle-right"></i> {{__("Tractors")}}</span></a></li>

                </ul>
            </li>
            
            <li class="treeview @if( isset($page) && ($page === 'brokers-index' ||  $page === 'customers-index' )) active @endif">
                <a href="#"><i class="fa fa-map-marker"></i> 
                <span>{{__("tran.Customers")}} </span>
                <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li class="@if( isset($page) && $page === 'brokers-index') active @endif loading-btn"><a href="{{route("brokers-index")}}"><i class="fa fa-chevron-circle-right"></i> <span> {{__("tran.Brokers")}}</span></a></li>
                    <li class="@if( isset($page) && $page === 'customers-index') active @endif loading-btn"><a href="{{route("customers-index")}}"><i class="fa fa-chevron-circle-right"></i> <span> {{__("tran.Locations")}}</span></a></li>
                </ul>
            </li>
           
            <li class="@if( isset($page) && $page === 'stats-index') active @endif loading-btn"><a href="{{route("stats-index")}}"> <i class="fa fa-file"></i><span>{{__("tran.Reporting")}}</span></a></li>

            <li class="treeview @if( isset($page) && ($page === 'paymanagement_index')) active @endif">
                <a href="#"> <i class="fa fa-dollar"></i> 
                    <span>{{__("Billing")}} </span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                <li class="@if( isset($page) && $page === 'paymanagement_index') active @endif loading-btn"><a href="{{route("drivers-paymanagement-index")}}"><span><i class="fa fa-chevron-circle-right"></i> {{__("tran.Pay Management")}}</span></a></li>
                </ul>    
            </li>
            
           


            <li class="treeview @if( isset($page) && ($page === 'listsettings-index' ||  $page === 'factoragentsetting')) active @endif">
                <a href="#"> <i class="fa fa-gear"></i> 
                    <span>{{__("Settings")}} </span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                <li class="@if( isset($page) && $page === 'listsettings-index') active @endif loading-btn"><a href="{{route('listsettings-index')}}"><i class="fa fa-chevron-circle-right"></i> <span> {{__("tran.List Settings")}}</span></a></li>
                <li class="@if( isset($page) && $page === 'factoragentsetting') active @endif loading-btn"><a href="{{route("factor-agent-setting")}}"><i class="fa fa-chevron-circle-right"></i> <span> {{__("Invoice Email Setting")}}</span></a></li>
                </ul>    
            </li>
            
            <li><a href="mailto:support@truewebinc.com?subject=I Need Tech Support for Milam Transport Truckers"><i class="fa fa-bullhorn"></i> <span>{{__("tran.Help/Feedback")}}</span></a></li>
            <!-- <li class="@if( isset($page) && $page === 'testloads') active @endif loading-btn"><a href="{{url('testload')}}"> <i class="fa fa-wrench"></i> <span>{{__("View Loads Beta")}}</span></a></li> -->
            <!-- <li class="@if( isset($page) && $page === 'testpaymanagement') active @endif loading-btn"><a href="{{url('testpayment')}}"><i class="fa fa-wrench"></i>  <span>{{__("Paymanagement Beta")}}</span></a></li> -->
            

            

            

            

            
            

            {{--        <li class="treeview active">
                        <a href="#">
                            <i class="fa fa-share"></i> <span>Multilevel</span>
                            <span class="pull-right-container">
                      <i class="fa fa-angle-left pull-right"></i>
                    </span>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="#"><i class="fa fa-circle-o"></i> Level One</a></li>
                            <li class="treeview">
                                <a href="#"><i class="fa fa-circle-o"></i> Level One
                                    <span class="pull-right-container">
                          <i class="fa fa-angle-left pull-right"></i>
                        </span>
                                </a>
                                <ul class="treeview-menu">
                                    <li><a href="#"><i class="fa fa-circle-o"></i> Level Two</a></li>
                                    <li class="treeview">
                                        <a href="#"><i class="fa fa-circle-o"></i> Level Two
                                            <span class="pull-right-container">
                              <i class="fa fa-angle-left pull-right"></i>
                            </span>
                                        </a>
                                        <ul class="treeview-menu">
                                            <li><a href="#"><i class="fa fa-circle-o"></i> Level Three</a></li>
                                            <li><a href="#"><i class="fa fa-circle-o"></i> Level Three</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                            <li><a href="#"><i class="fa fa-circle-o"></i> Level One</a></li>
                        </ul>
                    </li>--}}

        </ul>
    </section>
    <!-- /.sidebar -->
</aside>