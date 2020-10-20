<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>H and H Farms</title>

        <style type="text/css">
    			.navbar-top-links .dropdown-messages, .navbar-top-links .dropdown-tasks, .navbar-top-links .dropdown-alerts {
    				width: 310px;
    				min-width: 0;
    			}
        </style>

        <link href="{{ asset('/css/bootstrap.min.css') }}?f={{date("YmdHis")}}" rel="stylesheet">
        <link href="{{ asset('/css/google-lato-fonts.css') }}" rel="stylesheet" type="text/css">
        <link href="{{ asset('/css/select2.min.css') }}" rel="stylesheet" />
        <link href="{{ asset('/css/jquery-ui-1.11.4.css') }}" rel="stylesheet" />
        <!--<link href="{{ asset('/css/spectrum.css') }}" rel="stylesheet" />-->
        <!--<link href="{{ asset('css/jquery-ui-timepicker-addon.css') }}" rel="stylesheet" />-->
        <!--<link href="{{ asset('css/dragula.css') }}" rel="stylesheet" />-->
        <link href="{{ asset('css/custom.css') }}?f={{date("YmdHis")}}" rel="stylesheet" />
        <link href="{{ asset('css/pnotify.custom.min.css') }}?f==date("YmdHis")" rel="stylesheet" />
        <link href="{{ asset('js/libs/morris.js-0.5.1/morris.css') }}" rel="stylesheet" />

        <!--Font Awesome-->
        <link href="{{ asset('vendor/bower_components/font-awesome/css/font-awesome.css') }}" rel="stylesheet" />

        @if (Auth::guest())
        <style type="text/css">
    			html {
    			  //background: url("{{ asset('images/bg.jpg') }}") no-repeat center center fixed;
    			  background: #FFFFFF;
    			  -webkit-background-size: cover;
    			  -moz-background-size: cover;
    			  -o-background-size: cover;
    			  background-size: cover;
    			}
    			body {
    				background-color: !important none;
    			}
    		</style>
        @endif

    <!-- livezilla.net code --><script type="text/javascript" id="8e6ac49cfc652f613945d0122864a0bc" src="http://j2feeds.carrierinsite.com/livezilla/script.php?id=8e6ac49cfc652f613945d0122864a0bc"></script><!-- http://www.livezilla.net -->

    <script type="text/javascript" src="{{ asset('/js/google-js-api.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/js/jquery-2.1.4.min.js') }}"></script>
    <script src="{{ asset('/js/app.js') }}"></script>
    <script type="text/javascript">jQuery.fn.bstooltip = jQuery.fn.tooltip;</script>
    <script src="{{ asset('js/jquery-ui-1.11.4.js')}}"></script>
    <script src="{{ asset('js/custom.js') }}?f=<?=date("YmdHis");?>"></script>
    @if(Request::url() != url()."/loading")
    <script type="text/javascript">var jQuery.fn.tooltip = _tooltip;</script>
    <script src="{{ asset('js/host.js') }}?f=<?=date("YmdHis");?>"></script>

    <script src="{{ asset('js/jquery.numeric.min.js') }}"></script>
    <script src="{{ asset('js/jquery-dateFormat-master/jquery-dateFormat.min.js') }}"></script>
    @endif

    @if (Auth::guest())
    <script src="{{ asset('js/tubular.js') }}"></script>
    <script type="text/javascript">
    $(document).ready(function(){
        $('.container-tube').tubular({videoId: 'zTVMLt600AE'});
    });
    </script>
    @endif



    </head>
    <body>
        @if(Request::url() != url()."/loading")
        <script src="{{ asset('js/pnotify.custom.min.js') }}"></script>
        @endif

        <script type="text/javascript">
			   var app_url = "{{url()}}";
		      $(document).ready(function(e) {
			            $('.farm-header-two').hide();
          });
        </script>


        @include('partials.nav')

        <div class="container">
        	@include('partials.leftpanel')
        	@include('flash::message')
        	@yield('content')
        </div>

        @yield('footer')

        @if(Request::url() != url()."/loading")
        <script src="{{ asset('js/select2.min.js') }}"></script>

        <script>
    			$('#tag_list').select2({
    				placeholder: 'Select a data',
    				//allowClear: true,
    				tags: true,
    				theme: "classic"
    			});

    			// select menu for tons selection
    			$('.tag_list').select2({
    				placeholder: 'Select a data',
    				allowClear: true,
    				theme: "classic"
    			});
    			// Remove default zero value in the tons selection
    			$('.tag_list').on("select2:open", function (e) {
    				var zero_element = $("strong.select2-results__group").remove();
    			});

    			$(".farms-list").select2();
    			$(".drivers-list").select2();
    			$(".assign-list").select2();
    			$(".roles-list").select2();

    		</script>
        @endif
        @if(Request::url() == url()."/farms" || Request::url() == url()."/farms/create" || Request::url() == url()."/livetrucks")
          <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC-m-mI5Zae0JsqBeHyKh5v-lMvgCdsYmk&libraries=places,visualization,geometry&callback=initialize"></script>
        @endif


    </body>
</html>
