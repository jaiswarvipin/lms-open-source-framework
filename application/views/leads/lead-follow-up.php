<div id="divlLeadFolloupDetails" class="modal modal-fixed-footer">
    <div class="modal-content">
		<h4><span class="spnActionTexta">Lead Follow-Up Details</span></h4>
		<form name="frmleadFolloupDetails" id="frmleadFolloupDetails" method="post" action="leadsoperation/leadsoperation/setlLeadFollowupDetails">
			<div class="row">
				<div class="col s6">
					<label for='cboParnetStatus'>Follow-up Date*</label>
					<input type="text" name="txtFollowUpDate" id="txtFollowUpDate" class="datepicker" />
				</div>
				<div class="col s6">
					<label for='cboParnetStatus'>Follow-up Time*</label>
					<input type="text" name="txtFollowUpTime" id="txtFollowUpTime" class="timepicker" />
				</div>
			</div>
			<div class="row">
				<div class="input-field col s12">
					<select name="cboStatusCode" id="cboStatusCode" data-set="status_code"><?php echo $strStatusCode?></select>
					<label for='cboParnetStatus'>Select Status*</label>
				</div>
			</div>
			<div class="row">
				<div class="input-field col s12">
					<select name="cboTaskTypeCode" id="cboTaskTypeCode" data-set="task_type_code"><?php echo $strTaskType?></select>
					<label for='cboParnetStatus'>Select Task Type*</label>
				</div>
			</div>
			<div class="row">
				<div class="input-field col s12">
					<textarea id="txtComments" name="txtComments" class="materialize-textarea" data-length="120"></textarea>
					<label for="textarea1">Follow-Up Comments</label>
				</div>
			</div>
			<input type="hidden" name="txtLeadCode" id="txtLeadCode" value= "" />
			<input type="hidden" name="txtLeadOwnerCode" id="txtLeadOwnerCode" value= "" />
		</form>
	</div>
	<div class="modal-footer">
    	<a href="javascript:void(0);" class="modal-action modal-close waves-effect waves-green btn-flat">Cancel</a>
		<button class="btn waves-effect waves-light cmdDMLAction" type="submit" name="cmdRecordFolloupDetails" id="cmdRecordFolloupDetails" formName="frmleadFolloupDetails" >Submit<i class="material-icons right">send</i></button>
    </div>
</div>