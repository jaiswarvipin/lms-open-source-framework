<div class="row">
	<div class="col s12"><?php echo $strSalesFunnel?></div>
</div>
<div class="row">
	<div class="col s12">
		<table class="bordered highlight responsive-table">
			<thead>
				<tr>
					<th width="100%">FOS Tracking</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><h1 style="color:gray; text-align:center;">FOS REAL TIME TRACKING</h1></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<div class="row">
	<div class="col s12"><?php echo $strRegionLeads?></div>
</div>
<div class="row">
	<div class="col s12"><?php echo $strBranchLeads?></div>
</div>
<div class="row">
	<div class="col s6"><?php echo $strNewLead?></div>
	<div class="col s6"><?php echo $strPendingTask?></div>
</div>

<div class="row">
	<div class="col s6"><?php echo $strRegionPerformance?></div>
	<div class="col s6"><?php echo $strBranchPerformance?></div>
</div>


<div class="row">
	<div class="col s6"><?php echo $strEmpPerformance?></div>
	<div class="col s6">
		<table class="bordered highlight responsive-table">
			<thead>
				<tr>
					<th width="100%">My Performance</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><h1 style="color:gray; text-align:center;">MY PERFORMANCE</h1></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>

<!-- Add /Edit Modal Structure -->
<div id="<?php echo $strDataAddEditPanel?>" class="modal modal-fixed-footer">
    <div class="modal-content">
		<h4><span class="spnActionText">Add New</span> <?php echo $moduleTitle?></h4>
		<?php echo $strAddPanel?>
	</div>
	<div class="modal-footer">
    	<a href="javascript:void(0);" class="modal-action modal-close waves-effect waves-green btn-flat">Cancel</a>
		<button class="btn waves-effect waves-light cmdSearchReset green lighten-2 hide" type="submit" name="cmdAddNewLeadsCancel" id="cmdAddNewLeadsClearFilter" formName="cmdAddNewLeadsClearFilter" >Clear Filter<i class="material-icons right">find_replace</i></button>
    	<button class="btn waves-effect waves-light cmdDMLAction" type="submit" name="cmdAddNewLeads" id="cmdAddNewLeads" formName="frmAddNewLead" >Submit<i class="material-icons right">send</i></button>
    </div>
</div>