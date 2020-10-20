{!! Form::open(['method' => 'POST','url' => 'farms/storebins']) !!}
    {!! Form::hidden('bins_number',$bins_number) !!}
    {!! Form::hidden('farm_id',$farm_id) !!}
    {!! Form::hidden('selected_farm_bins', $selected_farm_bins) !!}
    <div class="form-horizontal">
        <div class="row">
        
              <!-- Nav tabs -->
              <ul class="nav nav-pills" role="tablist">
                 @for ($i = $selected_farm_bins; $i < $bins_number; $i++)
                    @if($i%4 == $selected_farm_bins)
                        <li role="presentation" class="{{($i == 1 ? 'active' : '')}}"><a href="#{{$i}}" style="border-bottom-left-radius: 0px; border-bottom-right-radius: 0px;" aria-controls="home" role="tab" data-toggle="tab">{{$i}} 
                    @endif
                    @if($i%4 == $bins_number)
                        - {{$i}}</a></li>    
                    @endif
                    @if($i == $bins_number)
                        </a></li>    
                    @endif
                 @endfor
              </ul>
            
              <!-- Tab panes -->
              <div class="tab-content">
                @for ($i = $selected_farm_bins; $i < $bins_number; $i++)
                    @if($i%4 == $selected_farm_bins)
                        <div role="tabpanel" class="tab-pane fade{{($i == 1 ? ' in active' : '')}}" id="{{$i}}">
                            <div class="panel panel-default">
                            <div class="panel-body" style="padding:0px; padding-top:15px;">
                    @endif
                                <div class="col-md-6">
                                    <div class="bins-box">
                                        <h4 style="font-weight: bolder; font-style: normal; margin-left:10px; margin-top:0px; margin-bottom:0px; color: #000;">Bin # {{ $i+1 }}</h4><hr style="margin-bottom:5px; margin-top:0px; border-top: 1px solid #ACABAB;"/>
                                        <div class="form-group bins-box-form-group">
                                            <label for="input_bin_name_{{$i+1}}" class="col-sm-4 control-label">Bin Name/Alias</label>
                                            <div class="col-sm-8">
                                            <input type="text" name="bin_name_{{$i+1}}" class="form-control compartments input-sm" autocomplete="off" placeholder="Enter name or alias of the bin" required>
                                            </div>
                                        </div>
                                        <div class="form-group bins-box-form-group">
                                            <label for="input_number_of_pigs_{{$i+1}}" class="col-sm-4 control-label">Pigs</label>
                                            <div class="col-sm-8">
                                            <input type="number" name="number_of_pigs_{{$i+1}}" class="form-control compartments input-sm numeric" autocomplete="off" placeholder="Enter Number of Pigs" required>
                                            </div>
                                        </div>
                                        <div class="form-group bins-box-form-group">
                                             <label for="input_bin_types_{{$i+1}}" class="col-sm-4 control-label">Bin Size</label>
                                             <div class="col-sm-8">
                                             {!! Form::select("bin_size_$i", $bin_sizes, null, ['class' => 'form-control input-sm']) !!}	
                                             </div>
                                        </div>
                                        <div class="form-group bins-box-form-group">
                                            <label for="input_bins_color_{{$i+1}}" class="col-sm-4 control-label">Bin Color</label>
                                            <div class="col-sm-8">
                                            <input type="hidden" name="bins_color_{{$i+1}}" value="{{'#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT)}}" />
                                            <input type="text" class="form-control" autocomplete="off" style="background-color:{{'#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT)}}" disabled required>
                                            </div>
                                        </div>
                                    </div>    
                                </div>
                    @if($i%4 == $bins_number)
                            </div>
                            </div>
                        </div>
                    @endif
                    @if($i == $bins_number)
                            </div>
                            </div>
                        </div>
                    @endif
                 @endfor
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