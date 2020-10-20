@unless (Auth::guest())

<nav class="navbar navbar-default nav-feeds-main">
    <div class="container">


        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">Toggle Navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>

        </div>


        <div class="collapse navbar-collapse nav-feeds" id="bs-example-navbar-collapse-1">
        	<ul class="nav navbar-nav navbar-left">

            </ul>

            <ul class="nav navbar-nav navbar-right">
            	<!--<li class="dropdown">
                	<a href="#" class="dropdown-toggle nav-config" data-toggle="dropdown" role="button" aria-expanded="false">
                        <span style="margin-right:2px;">Welcome, {{ Auth::user()->username }}</span>
                    </a>
                </li>-->
                <!--Messages Dropdown-->
                <li class="dropdown">
                	<a class="dropdown-toggle nav-config msg-noti-badge" data-toggle="dropdown" href="#">
                        <i class="fa fa-envelope fa-fw"></i> <i class="fa fa-caret-down"></i>
                    </a>
                    <!--<a class="dropdown-toggle nav-config" data-toggle="dropdown" href="#">
                        <span class="badge" style="padding:3px 4px; background-color: #5CB85C;">17</span><i class="fa fa-envelope fa-fw"></i>  <i class="fa fa-caret-down"></i>
                    </a>-->
                    <ul class="dropdown-menu dropdown-messages msg-notifications">
                        <li>
                            <a href="#">
                                <div>
                                    <strong>Bill.long</strong>
                                    <span class="pull-right text-muted">
                                        <em>Today</em>
                                    </span>
                                </div>
                                <div>Updated bin 6 for farm ville 1234...</div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="#">
                                <div>
                                    <strong>Larry</strong>
                                    <span class="pull-right text-muted">
                                        <em>Today</em>
                                    </span>
                                </div>
                                <div>Updated bin 4 for farm ville 4321...</div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="#">
                                <div>
                                    <strong>Rocky</strong>
                                    <span class="pull-right text-muted">
                                        <em>Today</em>
                                    </span>
                                </div>
                                <div>Updated bin 2 for farm ville 123...</div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li model="{{$user = \App\User::select('id')->where('id','!=',Auth::user()->id)->orderBy('username')->take(1)->get()}}">
                            <a class="text-center" href="/msg/{{$user[0]['id']}}">
                                <strong>Read All Messages</strong>
                                <i class="fa fa-angle-right"></i>
                            </a>
                        </li>
                    </ul>
                </li>
                <!--End of messages dropdown-->
                <!--Notifications Dropdown-->
                <li class="dropdown"  style="display:none">
                    <a class="dropdown-toggle nav-config" data-toggle="dropdown" href="#">
                        <span class="badge" style="padding:3px 4px; background-color: #5CB85C;">17</span><i class="fa fa-bell fa-fw"></i> <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-alerts">
                        <li>
                            <a href="#">
                                <div>
                                    <i class="fa fa-comment fa-fw"></i> New Comment
                                    <span class="pull-right text-muted small">4 minutes ago</span>
                                </div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="#">
                                <div>
                                    <i class="fa fa-twitter fa-fw"></i> 3 New Followers
                                    <span class="pull-right text-muted small">12 minutes ago</span>
                                </div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="#">
                                <div>
                                    <i class="fa fa-envelope fa-fw"></i> Message Sent
                                    <span class="pull-right text-muted small">4 minutes ago</span>
                                </div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="#">
                                <div>
                                    <i class="fa fa-tasks fa-fw"></i> New Task
                                    <span class="pull-right text-muted small">4 minutes ago</span>
                                </div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="#">
                                <div>
                                    <i class="fa fa-upload fa-fw"></i> Server Rebooted
                                    <span class="pull-right text-muted small">4 minutes ago</span>
                                </div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a class="text-center" href="#">
                                <strong>See All Alerts</strong>
                                <i class="fa fa-angle-right"></i>
                            </a>
                        </li>
                    </ul>
                </li>
                <!--End of Notification Dropdown-->
                <!--User Dropdown-->
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle nav-config" data-toggle="dropdown" role="button" aria-expanded="false">
                        <i class="fa fa-user fa-fw"></i>  <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-feeds nav-config-menu" role="menu">
                    	<li role="separator" class="divider"></li>
                        <li><a href="{{ url('/animalgroup') }}">Animal Group</a></li>
                        <li role="separator" class="divider"></li>
                        <li><a href="{{ url('/farms') }}">Farms</a></li>
                        <li><a href="{{ url('/deceased') }}">Deceased</a></li>
                        <li><a href="{{ url('/treatment') }}">Treatment</a></li>
                        <li><a href="{{ url('/truck') }}">Trucks</a></li>
                        <li style="display:none"><a href="{{ url('/bins') }}">Bins</a></li>
                        <li><a href="{{ url('/feedtype') }}">Feeds Types</a></li>
                        <li><a href="{{ url('/medication') }}">Medications</a></li>
                        <li><a href="{{ url('/binsize') }}">Bin Sizes</a></li>
                        <li><a href="{{ url('/users') }}">User Accounts</a></li>
                        <li><a href="{{ url('/userstype') }}">User Types</a></li>
                        <li><a href="{{ url('/farmsprofile') }}">Farmers Profiles</a></li>
                        <li role="separator" class="divider" style="display:none"></li>
                        <li><a href="#" class="undone" style="display:none">Feed Consumption</a></li>
                        <li><a href="#" class="undone" style="display:none">Drivers Profiles</a></li>
                        <li><a href="#" class="undone" style="display:none">User Profiles</a></li>
                        <li role="separator" class="divider"></li>
                        <li><a href="{{ url('/auth/logout') }}">Logout</a></li>
                    </ul>
                </li>
                <!--End of User Dropdown-->
            </ul>

            <ul class="nav navbar navbar-right">
            	<li><a href="#"></a></li>
            </ul>
            <!--<ul class="nav navbar-nav navbar-right">
               <li>{!! link_to_action('FarmsController@show', $latest->name, [$latest->id]) !!}</li>
            </ul>-->
        </div>

    </div>

</nav>
@if(Request::url() != url()."/releasenotes")
<script type="text/javascript">
$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})
</script>
@endif

@endif
