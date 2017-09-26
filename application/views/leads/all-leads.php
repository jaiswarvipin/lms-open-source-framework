<div class="row">
	<div class="col s12">
		<table class="bordered highlight responsive-table">
	        <thead>
				<tr>
					<th><input type="checkbox" id="chkBoxSelectAllLeads" /><label for="test5"></label></th>
					<?php if(!empty($strColumnsArr)){?>
						<?php foreach($strColumnsArr as $strColumnsArrKey => $strColumnsArrValue){?>
							<th><?php echo $strColumnsArrValue['label']?></th>
						<?php }?>
					<?php }?>
	              <th>Action</th>
	          </tr>
	        </thead>
	        <tbody id="tblLeadContaierBody"">
				<?php if((!empty($dataSet)) && (!empty($strColumnsArr))){?>
					<?php foreach($dataSet as $dataSetKey => $dataSetValue){?>
						<tr>
							<td><input type="checkbox" name="chkLeadCode[]" value="<?php echo getEncyptionValue($dataSetValue['lead_code']).DELIMITER.getEncyptionValue($dataSetValue['lead_owner_code'])?>" /><label for="test5"></label></td>
							<?php foreach($strColumnsArr as $strColumnsArrKey => $strColumnsArrValue){?>
								<?php if(isset($strColumnsArrValue['is_date'])){?>
									<td><?php echo getDateFormat($dataSetValue[$strColumnsArrValue['column']])?></td>
								<?php }else{?>
									<td><?php echo $dataSetValue[$strColumnsArrValue['column']]?></td>
								<?php }?>
							<?php }?>
							<td><a href="javascript:void(0);" onclick="openEditModel('divlLeadFolloupDetails','<?php echo getEncyptionValue($dataSetValue['lead_code']).DELIMITER.getEncyptionValue($dataSetValue['lead_owner_code'])?>',4);" class="waves-effect waves-circle waves-light btn-floating secondary-content"><i class="material-icons">edit</i></a></td>
						</tr>
					<?php }?>
				<?php }?>
	        </tbody>
	      </table>
		  <?php echo $pagination;?>
	</div>
</div>

<!-- Add /Edit Modal Structure -->
<div id="<?php echo $strDataAddEditPanel?>" class="modal modal-fixed-footer">
    <div class="modal-content">
		<h4><span class="spnActionText">Add New</span> <?php echo $moduleTitle?></h4>
		<?php echo $strAddPanel?>
		<?php echo $strColumnSearchPanel?>
	</div>
	<div class="modal-footer">
    	<a href="javascript:void(0);" class="modal-action modal-close waves-effect waves-green btn-flat">Cancel</a>
		<button class="btn waves-effect waves-light cmdSearchReset green lighten-2 hide" type="submit" name="cmdAddNewLeadsCancel" id="cmdAddNewLeadsClearFilter" formName="cmdAddNewLeadsClearFilter" >Clear Filter<i class="material-icons right">find_replace</i></button>
    	<button class="btn waves-effect waves-light cmdDMLAction" type="submit" name="cmdAddNewLeads" id="cmdAddNewLeads" formName="frmAddNewLead" >Submit<i class="material-icons right">send</i></button>
    </div>
</div>
<?php echo $strLeadFollowuppanel?>