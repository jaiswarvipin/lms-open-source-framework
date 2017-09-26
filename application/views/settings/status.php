<div class="row">
	<div class="col s12">
		<table class="bordered highlight  responsive-table">
	        <thead>	
	          <tr>
	              <th width='5%'>#</th>
	              <th>Parent & Child Status</th>
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
		            			<a href="javascript:void(0);" onclick="openEditModel('deleteModel','<?php echo $dataSetValue['id']?>',0);" class="waves-effect waves-circle waves-light btn-floating secondary-content red"><i class="material-icons">delete</i></a>&nbsp;
		            			<a href="javascript:void(0);" onclick="openEditModel('<?php echo $strDataAddEditPanel?>','<?php echo $dataSetValue['id']?>',1);" class="waves-effect waves-circle waves-light btn-floating secondary-content"><i class="material-icons">edit</i></a>
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
     	 <form class="col s12" method="post" action="<?php echo SITE_URL?>settings/status/setStatus" name="<?php echo $moduleForm?>" id="<?php echo $moduleForm?>">			
            <div class='row'>
              <div class='col s12'>
              </div>
            </div>

            <div class='row'>
              <div class='input-field col s12'>
                <input class='validate' type='text' name='txtStatusName' id='txtStatusName' data-set="description" />
                <label for='txtStatusName'>Enter Status Description *</label>
              </div>
            </div>

            <div class='row'>
              <div class='input-field col s12'>
                <select name="cboParnetStatus" id="cboParnetStatus" data-set="parent_id"><?php echo $strStatusCategories?></select>
                <label for='cboParnetStatus'>Select Parent Status*</label>
              </div>
            </div>

            <div class='row'>
              <label>Set Default Status*</label>
              <div class='input-field col s12'>
                	<input class="with-gap" name="rdoisDefault" value="1" type="radio" id="isDefaultYes"  data-set="is_default" />
    				<label for="isDefault">Yes</label>
    				<input class="with-gap" name="rdoisDefault" value="0" type="radio" id="isDefaultNo" checked data-set="is_default" />
    				<label for="isDefault">No</label>
              </div>
            </div>
			<input type="hidden" name="txtStatusCode" id="txtStatusCode" value="" data-set="id" />
			<input type="hidden" name="txtSearch" id="txtSearch" value="" data-set="" />
          </form>
    </div>
    <div class="modal-footer">
    	<a href="javascript:void(0);" class="modal-action modal-close waves-effect waves-green btn-flat">Cancel</a>
		<button class="btn waves-effect waves-light cmdSearchReset green lighten-2 hide" type="submit" name="cmdSearchReset" id="cmdSearchReset" formName="<?php echo $moduleForm?>" >Clear Filter<i class="material-icons right">find_replace</i></button>
    	<button class="btn waves-effect waves-light cmdStatusManagment" type="submit" name="cmdStatusManagment" id="cmdStatusManagment" formName="<?php echo $moduleForm?>" >Submit<i class="material-icons right">send</i></button>
    </div>
</div>