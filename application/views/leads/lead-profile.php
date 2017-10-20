<div id="divLeadProfileDetails" class="modal modal-fixed-footer">
    <div class="modal-content">
		<h4><span class="spnActionTexta">Lead Profile</span></h4>
		<form name="frmLeadProfileDetails" id="frmLeadProfileDetails" method="post" action="leadsoperation/leadsoperation/getLeadProfileDetails">
			<input type="hidden" name="txtLeadCode" id="txtLeadCode" value= "" />
			<input type="hidden" name="txtLeadOwnerCode" id="txtLeadOwnerCode" value= "" />
		</form>
		<div class="row">
			<div class="col s12">
				<ul class="tabs">
					<li class="tab col s3"><a class="active" href="#test1">Details</a></li>
					<li class="tab col s3"><a href="#test2">UTM Details</a></li>
					<li class="tab col s3"><a href="#divCommuncationHistoryContrainer">History</a></li>
				</ul>
			</div>
			<div id="test1" class="col s12"><?php echo $strLeadAttHTML?></div>
			<div id="test2" class="col s12">UTM Details</div>
			<div id="divCommuncationHistoryContrainer" class="col s12"></div>
		</div>
	</div>
	<div class="modal-footer">
    	<a href="javascript:void(0);" class="modal-action modal-close waves-effect waves-green btn-flat">Cancel</a>
		<button class="btn waves-effect waves-light cmdDMLAction" type="submit" name="cmdRecordProfileDetails" id="cmdRecordProfileDetails" formName="frmLeadProfileDetails">Submit<i class="material-icons right">send</i></button>
    </div>
</div>