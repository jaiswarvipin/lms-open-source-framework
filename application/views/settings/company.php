<div class="row">
	<div class="col s12">
		<table class="bordered highlight  responsive-table">
	        <thead>	
	          <tr>
	              <th width='5%'>#</th>
	              <th>Name</th>
	              <th width='7%'>Active</th>
	              <th width='7%'>Action</th>
	          </tr>
	        </thead>

	        <tbody>
	        	<?php if(!empty($dataSet)){
					$intCoounter	= $intPageNumber;
					foreach($dataSet as $dataSetKey => $dataSetValue){?>
						<tr>
		          			<td><?php echo $intCoounter?></td>
		            		<td><?php echo $dataSetValue['name']?></td>
		            		<td><?php echo (($dataSetValue['is_active'] == 1)?'Yes':'No')?></td>
		            		<td>
		            			<a href="javascript:void(0);" onclick="openEditModel('deleteModel','<?php echo getEncyptionValue($dataSetValue['id'])?>',0);" class="waves-effect waves-circle waves-light btn-floating secondary-content red"><i class="material-icons">delete</i></a>&nbsp;
		            			<a href="javascript:void(0);" onclick="openEditModel('<?php echo $strDataAddEditPanel?>','<?php echo getEncyptionValue($dataSetValue['id'])?>',1);" class="waves-effect waves-circle waves-light btn-floating secondary-content"><i class="material-icons">edit</i></a>
		            		</td>
		          		</tr>
						<?php $intCoounter++;?>
	        	<?php }
	        		}else{
	        			echo getNoRecordFoundTemplate(4);
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
     	 <form class="col s12" method="post" action="<?php echo SITE_URL?>settings/company/setCompanyDetails" name="<?php echo $moduleForm?>" id="<?php echo $moduleForm?>">
            <div class='row'>
              <div class='col s12'>
              </div>
            </div>

            <div class='row'>
              <div class='input-field col s12'>
                <input class='validate' type='text' name='txtCompanyName' id='txtCompanyName' data-set="name" />
                <label for='txtCompanyName'>Enter Company Name *</label>
              </div>
            </div>
			
			<div class='row no-search'>
              <label>Is Active*</label>
              <div class='input-field col s12'>
                	<input class="with-gap" name="rdoisActive" value="1" type="radio" id="rdoisActiveYes"  data-set="is_active" />
    				<label for="rdoisActiveYes">Yes</label>
    				<input class="with-gap" name="rdoisActive" value="0" type="radio" id="rdoisActiveNO" checked data-set="is_active" />
    				<label for="rdoisActiveNO">No</label>
              </div>
            </div>
			
			<input type="hidden" name="txtCompanyCode" id="txtCompanyCode" value="" data-set="id" />
			<input type="hidden" name="txtSearch" id="txtSearch" value="" data-set="" />
          </form>
    </div>
    <div class="modal-footer">
    	<a href="javascript:void(0);" class="modal-action modal-close waves-effect waves-green btn-flat">Cancel</a>
		<button class="btn waves-effect waves-light cmdSearchReset green lighten-2 hide" type="submit" name="cmdSearchReset" id="cmdSearchReset" formName="<?php echo $moduleForm?>" >Clear Filter<i class="material-icons right">find_replace</i></button>
    	<button class="btn waves-effect waves-light cmdStatusManagment" type="submit" name="cmdStatusManagment" id="cmdStatusManagment" formName="<?php echo $moduleForm?>" >Submit<i class="material-icons right">send</i></button>
    </div>
</div>




<!-- Field Mapping Modal Structure -->
<div id="divFieldMapping" class="modal modal-fixed-footer">
    <div class="modal-content">
		<h4><span class="spnActionTexta">Mapping Fields</h4>
     	 <form class="col s12" method="post" action="<?php echo SITE_URL?>settings/modules/setModuesFieldsDetails" name="<?php echo $moduleForm?>fieldMapping" id="<?php echo $moduleForm?>fieldMapping">
            <div class='row'>
              <div class='col s12'>
              </div>
            </div>

            <div class='row'>
              <div class='input-field col s9'>
                <select name="cboLeadAttributeCode" id="cboLeadAttributeCode" data-set="id"><?php echo $strleadAttrArr?></select>
                <label for='txtModuleName'>Select Lead Attributes *</label>
              </div>
			  <div class='input-field col s3'>
                <button class="btn waves-effect waves-light cmdLeadAttributeAdding" type="button" name="cmdLeadAttributeAdding" id="cmdLeadAttributeAdding" formName="<?php echo $moduleForm?>fieldMapping" >Add Field<i class="material-icons right">send</i></button>
              </div>
            </div>
			<div class='row'>
              <div class='input-field col s12'>
				<table border="0" id="tblLeadAttribute">
					<tr>
						<th>Field Name</th>
						<th width="5%">Action</th>
					</tr>
				</table>
			  </div>
			</div>
			<input type="hidden" name="txtModuleFieldCode" id="txtModuleFieldCode" value="" data-set="module_code" />
			<input type="hidden" name="txtSearch" id="txtSearch" value="" data-set="" />
          </form>
    </div>
    <div class="modal-footer">
    	<a href="javascript:void(0);" class="modal-action modal-close waves-effect waves-green btn-flat">Cancel</a>
		<button class="btn waves-effect waves-light cmdDMLAction" type="submit" name="cmdModuleFieldArr" id="cmdModuleFieldArr" formName="<?php echo $moduleForm?>fieldMapping" >Submit<i class="material-icons right">send</i></button>
    </div>
</div>