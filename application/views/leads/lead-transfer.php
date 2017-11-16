<div id="divlLeadTransfer" class="modal modal-fixed-footer">
    <div class="modal-content">
		<h4><span class="spnActionTexta">Lead Transfer</span></h4>
		<form name="frmLeadTransfer" id="frmLeadTransfer" method="post" action="leadsoperation/leadsoperation/setNewLeadOwner">
			<div class="row">
				<div class="col s6">
					<label for='cboTransferRegionCode'>Select Region</label>
					<select name="cboTransferRegionCode" id="cboTransferRegionCode" data-set="region_code" check-dependency="getBranchListByRegionCodeAct" dependency-element="cboTransferBranchCode"><?php echo $strRegionArr?></select>
				</div>
				<div class="col s6">
					<label for='cboTransferBranchCode'>Select Branch</label>
					<select name="cboTransferBranchCode" id="cboTransferBranchCode" data-set="branch_code" onChange="showRelatedRecord(this,'cboUSerCode');"><?php echo $strBranchArr?></select>
				</div>
			</div>
			<div class="row">
				<div class="input-field col s12">
					<select name="cboUSerCode" id="cboUSerCode" data-set="user_code"></select>
					<label for='cboUSerCode'>Select User</label>
				</div>
			</div>
			<input type="hidden" name="txtLeadCode" id="txtLeadCode" value= "" />
		</form>
	</div>
	<div class="modal-footer">
    	<a href="javascript:void(0);" class="modal-action modal-close waves-effect waves-green btn-flat">Cancel</a>
		<button class="btn waves-effect waves-light cmdDMLAction" type="submit" name="cmdRecordTransferDetails" id="cmdRecordTransferDetails" formName="frmLeadTransfer" >Submit<i class="material-icons right">send</i></button>
    </div>
</div>