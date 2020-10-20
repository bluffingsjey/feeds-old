@unless (Auth::guest())
<div class="col-md-2">
	<div class="navbar-default sidebar left-kb-nav" role="navigation">
        <div class="sidebar-nav navbar-collapse col-kb">
            <ul class="nav" id="side-menu">
            	<li style="height:20px;">
                </li>
            	<li style="text-align:right; right:10px;">
                	<a class="navbar-brand" style="padding-top:0px;padding-bottom:0px;" href="{{ url('/') }}">
                        <img alt="H and H Farms" src="{{ asset('/images/logo.png') }}" class="home-logo" />
                    </a>
                    Hello,<strong> {{Auth::user()->username}}</strong>
                    <p class="text-info" style="font-size:11px;">Today is {{date('l')}},<br/>{{date('F d, Y')}}<br/>{{date('g:i a')}}</p>
                </li>
                <li role="separator" class="divider" style="border: 1px solid #100000;"></li>
                <li class="menu-btn-kb">
                    <a href="/"><i class="fa fa-bar-chart-o fa-fw"></i> Forecasting<span class="fa arrow"></span></a>
                </li>
                <?php /*?><li class="menu-btn-kb">
                    <a href="{{ url('/scheduling')}}"><i class="fa fa-table fa-fw"></i> Scheduling<span class="fa arrow"></span></a>
                </li><?php */?>
                <li class="menu-btn-kb">
                    <a href="/loading"><i class="fa fa-table fa-fw"></i> Scheduling<span class="fa arrow"></span></a>
                </li>
                <li class="menu-btn-kb">
                    <a href="/deliveries"><i class="fa fa-truck fa-fw"></i> Deliveries</a>
                </li>
								<li class="menu-btn-kb">
                    <a href="/livetrucks"><i class="glyphicon glyphicon-record fa-fw"></i> Driver Tracking</a>
                </li>
								<li class="menu-btn-kb">
                    <a href="/driverstracking" style="margin-left: 4px;"><img src="{{asset('/images/driver.png')}}" style="width:14px;"/></i> Driver Stats</a>
                </li>
                <li class="menu-btn-kb">
                    <a href="/animalmovement"><i class="glyphicon glyphicon-piggy-bank fa-fw"></i> Animal Movement</a>
                </li>
                <li class="menu-btn-kb">
                    <a href="/settlements"><i class="fa fa-pie-chart fa-fw"></i> Performance</a>
                </li>
            </ul>
        </div>
        <!-- /.sidebar-collapse -->
    </div>

    @if(Request::url() == url() || Request::url() == url('/home'))
    <div id="pending_del_kb">

        <div class="loading-stick-circle-pending">

            <img src="/css/images/loader-stick.gif" />
            <small>Loading pending delivery batch...</small>

        </div>

    </div><!-- /.pending_del_kb -->
    @endif


    <!-- /.navbar-static-side -->
</div>
@endif
