{!! Form::open(['method' => 'POST','url' => 'farms/storebinsone']) !!}
    {!! Form::hidden('bins_number',$bins_number_orig) !!}
    {!! Form::hidden('farm_id',$farm_id) !!}
    <div class="form-horizontal">
        <div class="row">
        
              <!-- Nav tabs -->
              <ul class="nav nav-pills" role="tablist">
				    <li role="presentation" class="active"><a href="#1" style="border-bottom-left-radius: 0px; border-bottom-right-radius: 0px;" aria-controls="home" role="tab" data-toggle="tab">1 - 1</a></li>
              </ul>
            
              <!-- Tab panes -->
              <div class="tab-content">
				@include('farms.binsForm.1')
              </div>
            
            <div class="row">
                <div class="col-xs-2 pull-right" style="margin-right:20px">
                    <div class="form-group">
                        {!! Form::submit('Save', ['class' => 'btn btn-xs btn-success form-control']) !!}
                    </div>
                </div>
            </div>
            
        </div>
        
    </div>
    
    
{!! Form::close() !!}