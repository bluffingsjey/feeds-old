@extends('app')

@section('content')
<div class="container container-tube">
	<div class="row">
    	<div class="col-md-4 col-md-offset-4 logo-holder">
        	<img src="{{ asset("images/logo.png") }}" class="center-block login-logo" />
        </div>
    </div>
	<div class="row">
		<div class="col-md-4 col-md-offset-4 login-canvas">
            @if (count($errors) > 0)
                <div class="alert alert-danger alert-login">
                    <strong>Whoops!</strong> There were some problems with your input.<br>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
          	<div class="alert alert-danger alert-login-restrict" style="display:none">
               <strong>Whoops!</strong> There were some problems with your input.<br>
            </div>
            <form class="form-horizontal" id="form-login" role="form" method="POST" action="{{ url('/auth/login') }}">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <div class="form-group">
                    <div class="col-md-12">
                        <input type="text" class="form-control input-login username" name="username" autocomplete="off" value="{{ old('username') }}" placeholder="Username">
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-12">
                        <input type="password" class="form-control input-login password" name="password" placeholder="Password">
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-12">
                        <button type="button" class="btn btn-primary btn-block btn-login">Login</button>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-12">
                        <div class="checkbox white-font">
                            <label style="width:130px;">
                                <input type="checkbox" name="remember"> Remember Me
                            </label>
														<br/>
                            <a style="margin-left:7px;" class="btn btn-link dev" target="_parent" href="/forgotpw" style="font-size: 12px;">Change Password?</a>
                        </div>
                    </div>
                </div>


            </form>
		</div>
	</div>

     <div class="row">
    	<div class="col-md-4 col-md-offset-4" style="font-size: 11px; margin-top: 17px; opacity:0.8;">
        		<p class="white-font text-center italic">Powered by <a href="#" class="dev">EmergeGroupLLC</a></p>
        </div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function(e) {
    $(".btn-login").click(function(){

		var data = {
				'username'	: $(".username").val(),
				'password'	: $(".password").val()
			}

		var error = '<strong>Whoops!</strong> You are not allowed to access the admin panel.<br>';
		var success = '<div class="alert alert-success alert-login-restrict text-center"><strong>Logging In!</strong> Please wait...<br></div>';

		$.ajax({
			url	:	app_url+'/loginchecker',
			data: data,
			type: "POST",
			success: function(r){
				//success
				if(r == 0){
					$("#form-login").submit();
					$(".login-canvas").html("");
					$(".login-canvas").append(success);
				// fail
				}else{
					$(".alert-login-restrict").html("");
					$(".alert-login-restrict").show(function(){
						$(this).append(error);
					});

				}
			}
		})

	})
});
</script>

@endsection
