<div class="row">
	<div class="col s12">
		<table class="bordered highlight  responsive-table">
	        <thead>	
	          <tr>
	              <th width='5%'>#</th>
	              <th>Role</th>
	              <th width='7%'>Action</th>
	          </tr>
	        </thead>

	        <tbody>
	        	<?php if(!empty($dataSet)){
					$intCoounter	= $intPageNumber;
					foreach($dataSet as $dataSetKey => $dataSetValue){?>
						<tr>
		          			<td><?php echo $intCoounter?></td>
		            		<td><?php echo $dataSetValue['description']?></td>
		            		<td>
		            			<a href="javascript:void(0);" onclick="openEditModel('<?php echo $strDataAddEditPanel?>','<?php echo getEncyptionValue($dataSetValue['id'])?>',1);" class="waves-effect waves-circle waves-light btn-floating secondary-content"><i class="material-icons">edit</i></a>
		            		</td>
		          		</tr>
						<?php $intCoounter++;?>
	        	<?php }
	        		}else{
	        			echo getNoRecordFoundTemplate(3);
	        		}
				?>
	        </tbody>
	      </table>
	      <?php echo $pagination; ?>
	</div>
</div>


<!-- Add /Edit Modal Structure -->
<div id="<?php echo $strDataAddEditPanel?>" class="modal modal-fixed-footer">
    <div class="modal-content">
		<h4><span class="spnActionText">Add New</span> <?php echo $moduleTitle?></h4>
     	 <form class="col s12" method="post" action="<?php echo SITE_URL?>settings/modulesaccess/setModuesAccessDetails" name="<?php echo $moduleForm?>" id="<?php echo $moduleForm?>">
            <div class='row'>
              <div class='col s12'>
              </div>
            </div>

			<?php if(!empty($strModuleArr)){?>
				<div class='row'>
					<div class='input-field col s12'>
						<table border="0">
							<tr>
								<th width="25%">Module Name</th>
								<th>All</th>
							</tr>
							<?php foreach($strModuleArr as $strModuleArrKey => $strModuleArrValue){
									if(isset($strModuleArrValue['description'])){?>
										<tr>
											<td><?php echo str_replace('[divider]','',$strModuleArrValue['description'])?></td>
											<td><input class='validate' type='checkbox' name='txtModulename[]' id='txtModulename[]' value="<?php echo getEncyptionValue($strModuleArrValue['id'])?>" data-set="module_code" />&nbsp;<label>&nbsp;</label></td>
										</tr>
								<?php }?>
								<?php if(isset($strModuleArrValue['child'])){?>
									<?php foreach($strModuleArrValue['child'] as $strModuleArrValueKey => $strModuleArrValueDetails){?>
										<tr>
											<td>&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;<?php echo str_replace('[divider]','',$strModuleArrValueDetails['description'])?></td>
											<td><input class='validate' type='checkbox' name='txtModulename[]' id='txtModulename[]' value="<?php echo getEncyptionValue($strModuleArrValueDetails['id'])?>" data-set="module_code" />&nbsp;<label>&nbsp;</label></td>
										</tr>
									<?php }?>
								<?php }?>
							<?php }?>
						</table>
					</div>
				</div>
			<?php }?>
            
			<input type="hidden" name="txtRoleCode" id="txtRoleCode" value="" data-set="role_code" />
			<input type="hidden" name="txtSearch" id="txtSearch" value="" data-set="" />
          </form>
    </div>
    <div class="modal-footer">
    	<a href="javascript:void(0);" class="modal-action modal-close waves-effect waves-green btn-flat">Cancel</a>
		<button class="btn waves-effect waves-light cmdSearchReset green lighten-2 hide" type="submit" name="cmdSearchReset" id="cmdSearchReset" formName="<?php echo $moduleForm?>" >Clear Filter<i class="material-icons right">find_replace</i></button>
    	<button class="btn waves-effect waves-light cmdStatusManagment" type="submit" name="cmdStatusManagment" id="cmdStatusManagment" formName="<?php echo $moduleForm?>" >Submit<i class="material-icons right">send</i></button>
    </div>
</div>