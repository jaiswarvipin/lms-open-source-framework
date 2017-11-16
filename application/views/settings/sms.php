<div class="row">
	<div class="col s12">
		<table class="bordered highlight  responsive-table">
	        <thead>	
	          <tr>
	              <th width='5%'>#</th>
	              <th>Subject</th>
	              <th>From</th>
	              <th>Type</th>
	              <th width='20%'>Action</th>
	          </tr>
	        </thead>

	        <tbody>
	        	<?php if(!empty($dataSet)){
					$intCoounter	= $intPageNumber;
					foreach($dataSet as $dataSetKey => $dataSetValue){?>
						<tr>
		          			<td><?php echo $intCoounter?></td>
		            		<td><?php echo $dataSetValue['sms_subject']?></td>
		            		<td><?php echo $dataSetValue['sms_from']?></td>
		            		<td><?php echo $dataSetValue['sms_type']?></td>
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
     	 <form class="col s12" method="post" action="<?php echo SITE_URL?>settings/sms/setSMSDetails" name="<?php echo $moduleForm?>" id="<?php echo $moduleForm?>">
            <div class='row'>
              <div class='col s12'>
              </div>
            </div>

            <div class='row'>
              <div class='input-field col s12'>
                <input class='validate' type='text' name='txtSMSSubject' id='txtSMSSubject' data-set="sms_subject" />
                <label for='txtSMSSubject'>Enter SMS Subject *</label>
              </div>
            </div>
			
			<div class='row'>
              <div class='input-field col s12'>
                <input class='validate' type='text' name='txtSmsFrom' id='txtSmsFrom' data-set="sms_from" />
                <label for='txtSmsFrom'>Enter SMS From *</label>
              </div>
            </div>
			
			<div class='row'>
              <div class='input-field col s12'>
                <input class='validate' type='text' name='txtSMSType' id='txtSMSType' data-set="sms_type" />
                <label for='txtSMSType'>Enter SMS Type *</label>
              </div>
            </div>
			
			<div class='row no-search'>
              <div class='input-field col s12'>
                <textarea class='materialize-textarea' name='txtSMSBody' id='txtSMSBody' data-set="sms_body"></textarea>
                <label for='txtSMSBody'>SMS Content</label>
              </div>
            </div>
			
			<input type="hidden" name="txtSMSCode" id="txtSMSCode" value="" data-set="id" />
			<input type="hidden" name="txtSearch" id="txtSearch" value="" data-set="" />
          </form>
    </div>
    <div class="modal-footer">
    	<a href="javascript:void(0);" class="modal-action modal-close waves-effect waves-green btn-flat">Cancel</a>
		<button class="btn waves-effect waves-light cmdSearchReset green lighten-2 hide" type="submit" name="cmdSearchReset" id="cmdSearchReset" formName="<?php echo $moduleForm?>" >Clear Filter<i class="material-icons right">find_replace</i></button>
    	<button class="btn waves-effect waves-light cmdStatusManagment" type="submit" name="cmdStatusManagment" id="cmdStatusManagment" formName="<?php echo $moduleForm?>" >Submit<i class="material-icons right">send</i></button>
    </div>
</div>