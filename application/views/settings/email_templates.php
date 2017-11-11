<div class="row">
	<div class="col s12">
		<table class="bordered highlight  responsive-table">
	        <thead>	
	          <tr>
	              <th width='5%'>#</th>
	              <th>Subject</th>
	              <th>From Name</th>
	              <th>From Email</th>
	              <th>Active</th>
	              <th width='20%'>Action</th>
	          </tr>
	        </thead>

	        <tbody>
	        	<?php if(!empty($dataSet)){
					$intCoounter	= $intPageNumber;
					foreach($dataSet as $dataSetKey => $dataSetValue){?>
						<tr>
		          			<td><?php echo $intCoounter?></td>
		            		<td><?php echo $dataSetValue['email_subject']?></td>
		            		<td><?php echo $dataSetValue['from_name']?></td>
		            		<td><?php echo $dataSetValue['from_email']?></td>
		            		<td><?php echo (($dataSetValue['is_active'] == 1)?'Yes':'No');?></td>
		            		<td>
		            			<a href="javascript:void(0);" onclick="openEditModel('deleteModel','<?php echo getEncyptionValue($dataSetValue['id'])?>',0);" class="waves-effect waves-circle waves-light btn-floating secondary-content red"><i class="material-icons">delete</i></a>&nbsp;
		            			<a href="javascript:void(0);" onclick="openEditModel('<?php echo $strDataAddEditPanel?>','<?php echo getEncyptionValue($dataSetValue['id'])?>',1);" class="waves-effect waves-circle waves-light btn-floating secondary-content"><i class="material-icons">edit</i></a>
		            		</td>
		          		</tr>
						<?php $intCoounter++;?>
	        	<?php }
	        		}else{
	        			echo getNoRecordFoundTemplate(6);
	        		}
				?>
	        </tbody>
	      </table>
	</div>
</div>


<!-- Add /Edit Modal Structure -->
<div id="<?php echo $strDataAddEditPanel?>" class="modal modal-fixed-footer">
    <div class="modal-content">
		<h4><span class="spnActionText">Add New</span> <?php echo $moduleTitle?></h4>
     	 <form class="col s12" method="post" action="<?php echo SITE_URL?>settings/emailtemplate/setEmailTemplateDetails" name="<?php echo $moduleForm?>" id="<?php echo $moduleForm?>">
            <div class='row'>
              <div class='col s12'>
              </div>
            </div>

            <div class='row'>
              <div class='input-field col s12'>
                <input class='validate' type='text' name='txtEmailSubject' id='txtEmailSubject' data-set="email_subject" />
                <label for='txtEmailSubject'>Email Subject *</label>
              </div>
            </div>
			
			<div class='row'>
              <div class='input-field col s12'>
                <input class='validate' type='text' name='txtEmailFromName' id='txtEmailFromName' data-set="from_name" />
                <label for='txtEmailFromName'>From Name *</label>
              </div>
            </div>
			
			<div class='row'>
              <div class='input-field col s12'>
                <input class='validate' type='text' name='txtEmailFromEmail' id='txtEmailFromEmail' data-set="from_email" />
                <label for='txtEmailFromName'>From Email *</label>
              </div>
            </div>
			
			<div class='row no-search'>
              <div class='input-field col s12'>
                <textarea class='materialize-textarea' name='txtEmailBody' id='txtEmailBody' data-set="email_body"></textarea>
                <label for='txtEmailFromName'>Email Content</label>
              </div>
            </div>
			
			<div class='row no-search'>
              <div class='input-field col s12'>
                <input class='validate' type='text' name='txtBlackListEmailAddress' id='txtBlackListEmailAddress' data-set="black_list_emails" />
                <label for='txtBlackListEmailAddress'>Black List Emails(s) [Separated by comma]</label>
              </div>
            </div>
			
			<div class='row no-search'>
              <label>Is Active Email Template*</label>
              <div class='input-field col s12'>
                	<input class="with-gap" name="rdoisDefault" value="1" type="radio" id="isDefaultYes"  data-set="is_active" />
    				<label for="isDefault">Yes</label>
    				<input class="with-gap" name="rdoisDefault" value="0" type="radio" id="isDefaultNo" checked data-set="is_active" />
    				<label for="isDefault">No</label>
              </div>
            </div>
			
			<input type="hidden" name="txtEmailTemplateCode" id="txtEmailTemplateCode" value="" data-set="id" />
			<input type="hidden" name="eMaIlCoDe" id="eMaIlCoDe" value="<?php echo ($strEmailPCode)?>" />
			<input type="hidden" name="txtSearch" id="txtSearch" value="" data-set="" />
          </form>
    </div>
    <div class="modal-footer">
    	<a href="javascript:void(0);" class="modal-action modal-close waves-effect waves-green btn-flat">Cancel</a>
		<button class="btn waves-effect waves-light cmdSearchReset green lighten-2 hide" type="submit" name="cmdSearchReset" id="cmdSearchReset" formName="<?php echo $moduleForm?>" >Clear Filter<i class="material-icons right">find_replace</i></button>
    	<button class="btn waves-effect waves-light cmdStatusManagment" type="submit" name="cmdStatusManagment" id="cmdStatusManagment" formName="<?php echo $moduleForm?>" >Submit<i class="material-icons right">send</i></button>
    </div>
</div>