{!! Form::open(['method' => 'POST','url' => 'farms/storebinstwo']) !!}
    {!! Form::hidden('bins_number',$bins_number) !!}
    {!! Form::hidden('farm_id',$farm_id) !!}
    {!! Form::hidden('selected_farm_bins', $selected_farm_bins) !!}
    <div class="form-horizontal">
        <div class="row">
        
              <!-- Nav tabs -->
              
            
              <!-- Tab panes -->
              <div class="tab-content">
				@include('farms.binsForm.2')
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