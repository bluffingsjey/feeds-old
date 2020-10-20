@extends('app')


@section('content')


<div class="col-md-9">
 
    <div>

      <!-- Nav tabs -->
      <ul class="nav nav-tabs" role="tablist">
        <li role="presentation"><a href="/driverstracking" role="tab">Drivers Delivery Time Tracking</a></li>
        <li role="presentation" class="active"><a href="/livestocktracking" role="tab" style="color: #31708f; background-color: #d9edf7; font-weight:bolder;">Livestock Tracking</a></li>
      </ul>
    
      <!-- Tab panes -->
      <div class="tab-content">
        <div role="tabpanel" class="tab-pane "></div>
        <div role="tabpanel" class="tab-pane active">
        
        </div>
      </div>
    
    </div>
 
</div>

@stop