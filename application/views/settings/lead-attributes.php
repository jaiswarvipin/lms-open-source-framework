<div class="row">
	<div class="col s12">
		<table class="bordered highlight  responsive-table">
	        <thead>	
	          <tr>
	              <th width='5%'>#</th>
	              <th>Attribute Name</th>
	              <th>Element Type</th>
	              <th>Mandatory</th>
	              <th width='7%'>Action</th>
	          </tr>
	        </thead>

	        <tbody>
	        	<?php if(!empty($dataSet)){
					$intCoounter	= $intPageNumber;
	        		foreach($dataSet as $dataSetKey => $dataSetValue){?>
						<tr>
		          			<td><?php echo $intCoounter?></td>
		            		<td><?php echo $dataSetValue['attri_slug_name']?></td>
		            		<td><?php echo ucfirst($dataSetValue['attri_data_type'])?></td>
		            		<td><?php echo getYesNo($dataSetValue['is_mandatory'])?></td>
		            		<td>
		            			<a href="javascript:void(0);" onclick="openEditModel('deleteModel','<?php echo getEncyptionValue($dataSetValue['id'])?>',0);" class="waves-effect waves-circle waves-light btn-floating secondary-content red"><i class="material-icons">delete</i></a>&nbsp;
		            			<a href="javascript:void(0);" onclick="openEditModel('<?php echo $strDataAddEditPanel?>','<?php echo getEncyptionValue($dataSetValue['id'])?>',1);" class="waves-effect waves-circle waves-light btn-floating secondary-content"><i class="material-icons">edit</i></a>
		            		</td>
		          		</tr>
						<?php $intCoounter++;?>
	        	<?php }
	        		}else{
	        			echo getNoRecordFoundTemplate(5);
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
     	 <form class="col s12" method="post" action="<?php echo SITE_URL?>settings/leadattributes/setLeadAttributDetails" name="<?php echo $moduleForm?>" id="<?php echo $moduleForm?>">
            <div class='row'>
              <div class='col s12'>
              </div>
            </div>

            <div class='row'>
              <div class='input-field col s12'>
                <input class='validate' type='text' name='txtAttrubuteName' id='txtAttrubuteName' data-set="attri_slug_name" />
                <label for='txtAttrubuteName'>Enter Attribute Description *</label>
              </div>
            </div>

            <div class='row no-search'>
              <div class='input-field col s12'>
                <select name="cboAttributeType" id="cboAttributeType" data-set="attri_data_type" onChange="displayHideElement(this,'<?php echo getEncyptionValue('select')?>','divLeadAttributesContaier');"><?php echo $strElementsArr?></select>
                <label for='cboAttributeType'>Select Attribute Element Type*</label>
              </div>
            </div>
			
			<div class='row hide divLeadAttributesContaier'>
              <div class='input-field col s12'>
				<table border="0" width="100%" class="divLeadAttributesPanel"></table>
				<button class="btn waves-effect waves-light green lighten-2" type="button" name="cmdAddLeadAttributeOptions" id="cmdAddLeadAttributeOptions" onclick="addFormElement('text','txtLeadAttributesName[]','divLeadAttributesPanel','');">Add Options<i class="material-icons right">add</i></button>
              </div>
            </div>
			
			<div class='row no-search'>
              <div class='input-field col s12'>
                <select name="cboValidation" id="cboValidation" data-set="attri_validation"><?php echo $strValidationArr?></select>
                <label for='cboValidation'>Select Attribute Element Validation</label>
              </div>
            </div>

            <div class='row no-search'>
              <label>Is Mandatory*</label>
              <div class='input-field col s12'>
                	<input class="with-gap" name="rdoisMandatory" value="1" type="radio" id="rdoisMandatoryYes"  data-set="is_default" />
    				<label for="rdoisMandatoryYes">Yes</label>
    				<input class="with-gap" name="rdoisMandatory" value="0" type="radio" id="rdoisMandatory" checked data-set="is_default" />
    				<label for="rdoisMandatory">No</label>
              </div>
            </div>
			
			<input type="hidden" name="txtAttributeCode" id="txtAttributeCode" value="" data-set="id" />
			<input type="hidden" name="txtSearch" id="txtSearch" value="" data-set="" />
          </form>
    </div>
    <div class="modal-footer">
    	<a href="javascript:void(0);" class="modal-action modal-close waves-effect waves-green btn-flat">Cancel</a>
		<button class="btn waves-effect waves-light cmdSearchReset green lighten-2 hide" type="submit" name="cmdSearchReset" id="cmdSearchReset" formName="<?php echo $moduleForm?>" >Clear Filter<i class="material-icons right">find_replace</i></button>
    	<button class="btn waves-effect waves-light cmdStatusManagment" type="submit" name="cmdStatusManagment" id="cmdStatusManagment" formName="<?php echo $moduleForm?>" >Submit<i class="material-icons right">send</i></button>
    </div>
</div>