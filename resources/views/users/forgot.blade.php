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
            <form class="form-horizontal" id="form-login" role="form" method="GET" action="/resetpw">

                <div class="form-group">
                    <div class="col-md-12">
                        <input type="text" class="form-control input-login username" name="username" autocomplete="off" placeholder="Username" required>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-12">
                        <input type="password" class="form-control input-login password" autocomplete="off" name="password" placeholder="New Password" required>
                    </div>
                </div>

								<div class="form-group">
                    <div class="col-md-12">
                        <input type="password" class="form-control input-login password_confirmation" autocomplete="off" name="password_confirmation" placeholder="Repeat New Password" required>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary btn-block btn-login">SUBMIT</button>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-12">
                        <div class="checkbox white-font">
                            <a class="btn btn-link dev" target="_parent" href="/auth/login" style="font-size: 12px;">Go to Login Page?</a>
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

@endsection
