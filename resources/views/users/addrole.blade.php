@extends('app')
@section('content')
<div class="col-md-10">
    <div class="panel panel-info">
        <div class="panel-heading">
			<h1 class="panel-title">Assign User Role 
            <span class="pull-right"><a href="/users" class="btn btn-xs btn-primary">Back to Users</a></span>
            </h1>
    	</div>
        <div class="panel-body">
         @include('errors.list')
        <!-- Form Model Binding-->
        {!! Form::model(['url' => 'users','class'=>'form-inline']) !!}
           <div class="form-group">
                {!! Form::label('roles', 'Roles: ',['class'=>'col-sm-1']) !!}
                <div class="col-sm-4">
                {!! Form::select('roles', $roles, null, ['class' => 'roles-list form-control']) !!}
                </div>
                {!! Form::hidden('userid', $user_id->id, null) !!}
            </div>
            <div class="form-group">
            	<div class="col-offset-3 col-sm-4">
                {!! Form::button("Assign Role", ['class' => 'btn-assignrole btn btn-sm btn-success form-control']) !!}
                </div>
            </div>
            
        {!! Form::close() !!} 
        </div>
     </div>   
</div>   
    
@stop