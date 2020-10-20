
	<div role="tabpanel" class="tab-pane fade in active" id="1">
			<div class="panel panel-default">
			<div class="panel-body" style="padding:0px; padding-top:15px;">
			@for ($i = 1; $i <= $bins_number_orig; $i++)
				<div class="col-md-6">
					<div class="bins-box">
						<h4 style="font-weight: bolder; font-style: normal; margin-left:10px; margin-top:0px; margin-bottom:0px; color: #000;">Bin # {{ $i }}</h4><hr style="margin-bottom:5px; margin-top:0px; border-top: 1px solid #ACABAB;"/>
						<div class="form-group bins-box-form-group">
							<label for="input_bin_name_{{$i}}" class="col-sm-4 control-label">Bin Name/Alias</label>
							<div class="col-sm-8">
							<input type="text" name="bin_name_{{$i}}" class="form-control compartments input-sm" autocomplete="off" placeholder="Enter name or alias of the bin" required>
							</div>
						</div>
						<div class="form-group bins-box-form-group" style="display:none">
							<label for="input_number_of_pigs_{{$i}}" class="col-sm-4 control-label">Pigs</label>
							<div class="col-sm-8">
							<input type="number" name="number_of_pigs_{{$i}}" class="form-control compartments input-sm numeric" autocomplete="off" placeholder="Enter Number of Pigs" value="0">
							</div>
						</div>
						<div class="form-group bins-box-form-group">
							 <label for="input_bin_types_{{$i}}" class="col-sm-4 control-label">Bin Size</label>
							 <div class="col-sm-8">
							 {!! Form::select("bin_size_$i", $bin_sizes, null, ['class' => 'form-control input-sm']) !!}
							 </div>
						</div>
						<div class="form-group bins-box-form-group">
							<label for="input_bins_color_{{$i}}" class="col-sm-4 control-label">Bin Color</label>
							<div class="col-sm-8">
							<input type="hidden" name="bins_color_{{$i}}" value="{{'#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT)}}" />
							<input type="text" class="form-control" autocomplete="off" style="background-color:{{'#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT)}}" disabled required>
							</div>
						</div>
					</div>
				</div>
			@endfor
			</div>
			</div>
		</div>
