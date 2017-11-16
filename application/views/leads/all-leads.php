<div class="row">
	<div class="col s12">
		<table class="bordered highlight responsive-table">
	        <thead>
				<tr>
					<th><input type="checkbox" name="chkBoxSelectAllLeads" id="chkBoxSelectAllLeads" /><label for="chkBoxSelectAllLeads"></label></th>
					<?php if(!empty($strColumnsArr)){?>
						<?php foreach($strColumnsArr as $strColumnsArrKey => $strColumnsArrValue){?>
							<th><?php echo $strColumnsArrValue['label']?></th>
						<?php }?>
					<?php }?>
	              <th>Action</th>
	          </tr>
	        </thead>
	        <tbody id="tblLeadContaierBody">
				<?php if((!empty($dataSet)) && (!empty($strColumnsArr))){?>
					<?php foreach($dataSet as $dataSetKey => $dataSetValue){?>
						<?php $strRowClass = (isset($dataSetValue['task_notifi'])?' '.$dataSetValue['task_notifi']:'');?>
						<tr<?php echo $strRowClass;?>>
							<?php if((isset($dataSetValue['is_open'])) && ($dataSetValue['is_open'] == 1)){?>
								<td><input type="checkbox" id="chk<?php echo getEncyptionValue($dataSetValue['lead_code'])?>" name="chkLeadCode[]" value="<?php echo getEncyptionValue($dataSetValue['lead_code']).DELIMITER.getEncyptionValue($dataSetValue['lead_owner_code'])?>" /><label for="chk<?php echo getEncyptionValue($dataSetValue['lead_code'])?>"></label></td>
							<?php }else{?>
								<td><input type="checkbox" name="chkLeadCode[]" disabled="disabled" /><label for="chkLeadCode[]"></label></td>
							<?php }?>
							<?php foreach($strColumnsArr as $strColumnsArrKey => $strColumnsArrValue){?>
								<?php if(isset($strColumnsArrValue['is_date'])){?>
									<td><?php echo getDateFormat($dataSetValue[$strColumnsArrValue['column']])?></td>
								<?php }else{?>
									<td><?php echo $dataSetValue[$strColumnsArrValue['column']]?></td>
								<?php }?>
							<?php }?>
							<td>
								<?php if((isset($dataSetValue['is_open'])) && ($dataSetValue['is_open'] == 1)){?>
									<?php if($strDataAddEditPanel != 'taskModules'){?>
										<a href="javascript:void(0);" onclick="openEditModel('divLeadProfileDetails','<?php echo getEncyptionValue($dataSetValue['lead_code']).DELIMITER.''?>',4);" class="waves-effect waves-circle waves-light btn-floating secondary-content"><i class="material-icons">edit</a>
									<?php }?>
									<a href="javascript:void(0);" onclick="openEditModel('divlLeadFolloupDetails','<?php echo getEncyptionValue($dataSetValue['lead_code']).DELIMITER.getEncyptionValue($dataSetValue['lead_owner_code'])?>',4);" class="waves-effect waves-circle waves-light btn-floating secondary-content"><i class="material-icons">feedback</i></a>
								<?php }else{?>
									<?php if($strDataAddEditPanel != 'taskModules'){?>
										<a href="javascript:void(0);" class="disabled waves-effect waves-circle waves-light btn-floating secondary-content"><i class="material-icons">edit</a>
									<?php }?>
									<a href="javascript:void(0);" class="disabled waves-effect waves-circle waves-light btn-floating secondary-content"><i class="material-icons">add</i></a>
								<?php }?>
							</td>
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
<?php echo $strLeadTransferPanel?>
<?php echo $strLeadProfile?>