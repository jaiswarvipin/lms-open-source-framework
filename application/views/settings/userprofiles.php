<div class="row">
	<div class="col s12">
		<table class="bordered highlight  responsive-table">
	        <thead>	
	          <tr>
	              <th width='5%'>#</th>
	              <th>Name</th>
	              <th>Email</th>
				  <th>Role</th>
				  <th>Status</th>
				  <th width='7%'>Action</th>
	          </tr>
	        </thead>

	        <tbody>
	        	<?php if(!empty($dataSet)){
					$intCoounter	= $intPageNumber;
	        		foreach($dataSet as $dataSetKey => $dataSetValue){?>
						<tr>
		          			<td><?php echo $intCoounter?></td>
		            		<td><?php echo $dataSetValue['user_name']?></td>
							<td><?php echo $dataSetValue['user_email']?></td>
							<td><?php echo $dataSetValue['role_name']?></td>
							<td><?php echo $dataSetValue['is_active']?></td>
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
	      <?php echo $pagination; ?>
	</div>
</div>


<!-- Add /Edit Modal Structure -->
<div id="<?php echo $strDataAddEditPanel?>" class="modal modal-fixed-footer">
    <div class="modal-content">
		<h4><span class="spnActionText">Add New</span> <?php echo $moduleTitle?></h4>
     	 <form class="col s12" method="post" action="<?php echo SITE_URL?>settings/userprofiles/setUserProfile" name="<?php echo $moduleForm?>" id="<?php echo $moduleForm?>">			
            <div class='row'>
              <div class='col s12'>
              </div>
            </div>

            <div class='row'>
              <div class='input-field col s4'>
                <input class='validate' type='text' name='txtUserName' id='txtUserName' data-set="user_name" />
                <label for='txtUserName'>Enter User Name *</label>
              </div>
			  <div class='input-field col s4'>
                <input class='validate' type='text' name='txtEmail' id='txtEmail' data-set="user_email" />
                <label for='txtEmail'>Enter Email ID *</label>
              </div>
			  <div class='input-field col s4'>
                <input class='validate' type='text' name='txtPassword' id='txtPassword' data-set="" />
                <label for='txtPassword'>Password *</label>
              </div>
            </div>
			
			<div class='row'>
              <div class='input-field col s4'>
                <select name="cboRoleCode" id="cboRoleCode" data-set="role_code"><?php echo $strCustomRoleArr?></select>
                <label for='cboRoleCode'>Select User Role*</label>
              </div>
			  <div class='input-field col s4'>
                <select name="cboUserSystemRole" id="cboUserSystemRole" data-set="system_role_code"><?php echo $strSystemRoleArr?></select>
                <label for='cboUserSystemRole'>Select System Role*</label>
              </div>
			  <div class='input-field col s4'>
                <select name="cboUserStatus" id="cboUserStatus" data-set="is_active"><?php echo $strUserStatsArr?></select>
                <label for='cboUserStatus'>Select Status *</label>
              </div>
            </div>
			
			<div class='row no-search'>
              <hr />
            </div>
			
			<div class='row no-search'>
              <div class='input-field col s4'>
                <select name="cboZone" id="cboZone" data-set="zone" onChange="getDependencyData(this,'cboRegion','<?php echo getEncyptionValue('1')?>')" multiple><?php echo $strZoneArr?></select>
                <label for='cboZone'>Select Zone*</label>
              </div>
			  <div class='input-field col s4'>
                <select name="cboRegion" id="cboRegion" data-set="region" onChange="getDependencyData(this,'cboCity','<?php echo getEncyptionValue('2')?>')" multiple><?php //echo $strStatusCategories?></select>
                <label for='cboRegion'>Select Region*</label>
              </div>
			  <div class='input-field col s4'>
                <select name="cboCity" id="cboCity" data-set="city"  onChange="getDependencyData(this,'cboArea','<?php echo getEncyptionValue('3')?>')" multiple><?php //echo $strStatusCategories?></select>
                <label for='cboCity'>Select City *</label>
              </div>
            </div>
			
			<div class='row no-search'>
              <div class='input-field col s4'>
                <select name="cboArea" id="cboArea" data-set="area" onChange="getDependencyData(this,'cboBranchCode','<?php echo getEncyptionValue('4')?>')" multiple><?php //echo $strStatusCategories?></select>
                <label for='cboArea'>Select Area *</label>
              </div>
			  <div class='input-field col s4'>
                <select name="cboBranchCode" id="cboBranchCode" data-set="branch" onChange="getDependencyData(this,'','<?php echo getEncyptionValue('5')?>')" multiple><?php //echo $strStatusCategories?></select>
                <label for='cboBranchCode'>Select Branch Code *</label>
              </div>
			  <div class='input-field col s4'>
                <select name="cboReportingManager" id="cboReportingManager" data-set="cboDepartement"><?php //echo $strStatusCategories?></select>
                <label for='cboReportingManager'>Select Reporting Manager *</label>
              </div>
            </div>
			
			<input type="hidden" name="txtUserCode" id="txtUserCode" value="" data-set="id" />
			<input type="hidden" name="txtSearch" id="txtSearch" value="" data-set="" />
          </form>
    </div>
    <div class="modal-footer">
    	<a href="javascript:void(0);" class="modal-action modal-close waves-effect waves-green btn-flat">Cancel</a>
		<button class="btn waves-effect waves-light cmdSearchReset green lighten-2 hide" type="submit" name="cmdUserProfileSearchReset" id="cmdUserProfileSearchReset" formName="<?php echo $moduleForm?>" >Clear Filter<i class="material-icons right">find_replace</i></button>
    	<button class="btn waves-effect waves-light cmdDMLAction" type="submit" name="cmdUserProfileManagement" id="cmdUserProfileManagement" formName="<?php echo $moduleForm?>" >Submit<i class="material-icons right">send</i></button>
    </div>
</div>