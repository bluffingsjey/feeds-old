<!-- Modal -->
<div class="modal fade" id="treatmentModal" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Add Treatment Pigs</h4>
      </div>
      <div class="modal-body form-horizontal">
        	<div class="form-group">

						<div class='row space'>
							<div class='col-md-offset-2 col-md-3'>
								<p class='text-primary'>Group</p>
							</div>
							<div class='col-md-5'>
								<select class='input-sm form-control' id="groups"></select>
							</div>
						</div>

						<div class='row space'>
							<div class='col-md-offset-2 col-md-3'>
								<p class='text-primary'>Farm</p>
							</div>
							<div class='col-md-5'>
								<select class='input-sm form-control' id="farms"></select>
							</div>
						</div>

						<div class='row space'>
							<div class='col-md-offset-2 col-md-3'>
								<p class='text-primary'>Bin</p>
							</div>
							<div class='col-md-5'>
								<select class='input-sm form-control' id="bins"></select>
							</div>
						</div>

						<div class="input-fields">

							<div class='row space'>
								<div class='col-md-offset-2 col-md-3'>
									<p class='text-primary'>Date</p>
								</div>
								<div class='col-md-5'>
									<input type="text" class='input-sm form-control datePicker' id="date" value="{{date("M d, Y")}}"/>
								</div>
							</div>

							<div class='row space'>
								<div class='col-md-offset-2 col-md-3'>
									<p class='text-primary'>Illness</p>
								</div>
								<div class='col-md-5'>
									<select class='input-sm form-control' id="illness">
										<option value="Foot and Mouth">Foot and Mouth</option>
										<option value="Rabbies">Rabbies</option>
										<option value="Regworth Poisoning">Regworth Poisoning</option>
										<option value="Sale Ulcer">Sale Ulcer</option>
										<option value="Tetanus">Tetanus</option>
										<option value="Traumatic Reticulities">Traumatic Reticulities</option>
									</select>
								</div>
							</div>

              <div class='row space'>
								<div class='col-md-offset-2 col-md-3'>
									<p class='text-primary'>Drug Used</p>
								</div>
								<div class='col-md-5'>
									<select class='input-sm form-control' id="drug_used">
										<option value="Antibiotics">Antibiotics</option>
										<option value="Antibacterial">Antibacterial</option>
										<option value="Vitamins">Vitamins</option>
										<option value="Sedatives">Sedatives</option>
										<option value="Parasecticides">Parasecticides</option>
										<option value="Vaccines">Vaccines</option>
                    <option value="Hormones">Hormones</option>
										<option value="Probiotics">Probiotics</option>
										<option value="Growth Promoters">Growth Promoters</option>
										<option value="Colostrum Supplements">Colostrum Supplements</option>
									</select>
								</div>
							</div>

							<div class='row space'>
								<div class='col-md-offset-2 col-md-3'>
									<p class='text-primary'>Amount</p>
								</div>
								<div class='col-md-5'>
									<select class='input-sm form-control' id="amount">
										<option value="1">1 Pig</option>
										<option value="2">2 Pigs</option>
										<option value="3">3 Pigs</option>
										<option value="4">4 Pigs</option>
										<option value="5">5 Pigs</option>
										<option value="6">6 Pigs</option>
										<option value="7">7 Pigs</option>
									</select>
								</div>
							</div>

							<div class='row space'>
								<div class='col-md-offset-2 col-md-3'>
									<p class='text-primary'>Notes</p>
								</div>
								<div class='col-md-5'>
									<textarea class='input-sm form-control' placeholder="Some notes for the treatment..." row="8" id="notes"></textarea>
								</div>
							</div>

						</div>

					</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary btn-save" disabled>Save changes</button>
      </div>
    </div>
  </div>
</div>
