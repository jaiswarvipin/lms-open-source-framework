<div class="row">
	<div class="col s12">
		<table class="bordered highlight  responsive-table">
	        <thead>	
	          <tr>
	              <th width='5%'>#</th>
	              <th>Description</th>
				  <th width='10%'>Action</th>
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
								<ul>
									<li><a href="javascript:void(0);" onclick="openEditModel('deleteModel','<?php echo getEncyptionValue($dataSetValue['id'])?>',0);" class="waves-effect waves-circle waves-light btn-floating secondary-content red"><i class="material-icons">delete</i></a></li>
									<li><a href="javascript:void(0);" onclick="openEditModel('<?php echo $strDataAddEditPanel?>','<?php echo getEncyptionValue($dataSetValue['id'])?>',1);" class="waves-effect waves-circle waves-light btn-floating secondary-content"><i class="material-icons">edit</i></a></li>
									<li class="active"><a href="javascript:void(0);" onClick="setNavigation('<?php echo SITE_URL?>settings/locations/<?php echo strtolower($strChildLabel)?>','<?php echo 'parent_code=>'.getEncyptionValue($dataSetValue['id'])?>')"><?php echo $strChildLabel?></a></li>
								</ul>
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
     	 <form class="col s12" method="post" action="<?php echo SITE_URL?>settings/locations/setLocaions" name="<?php echo $moduleForm?>" id="<?php echo $moduleForm?>">			
            <div class='row'>
              <div class='col s12'>
              </div>
            </div>

            <div class='row'>
              <div class='input-field col s12'>
                <input class='validate' type='text' name='txtLocationDescription' id='txtLocationDescription' data-set="description" />
                <label for='txtLocationDescription'>Enter Location Description *</label>
              </div>
            </div>
			<input type="text" class="hide maintain" name="txtParentLocationCode" id="txtParentLocationCode" value="<?php echo $strParentCode?>" data-set="parent_code"/>
			<input type="text" class="hide" name="txtParentLocationType" id="txtParentLocationType" value="<?php echo $intLocationTypeCode?>" data-set="location_type" />
			<input type="hidden" name="txtLocationCode" id="txtLocationCode" value="" data-set="id" />
			<input type="hidden" name="txtSearch" id="txtSearch" value="" data-set="" />
          </form>
    </div>
    <div class="modal-footer">
    	<a href="javascript:void(0);" class="modal-action modal-close waves-effect waves-green btn-flat">Cancel</a>
		<button class="btn waves-effect waves-light cmdSearchReset green lighten-2 hide" type="submit" name="cmdleadSourceSearchReset" id="cmdleadSourceSearchReset" formName="<?php echo $moduleForm?>" >Clear Filter<i class="material-icons right">find_replace</i></button>
    	<button class="btn waves-effect waves-light cmdDMLAction" type="submit" name="cmdleadSourceManagement" id="cmdleadSourceManagement" formName="<?php echo $moduleForm?>" >Submit<i class="material-icons right">send</i></button>
    </div>
</div>