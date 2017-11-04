<div class="row">
	<div class="col s12">
		<table class="bordered highlight responsive-table">
			<thead>
				<tr>
					<th width="100%">Status v/s Date<span class="right f20"><?php echo $strDataArr['strFromDate']?> - <?php echo $strDataArr['strToDate']?></span></th>
			  </tr>
			</thead>
			<tbody>
				<tr>
					<table border="0" width="100%">
						<tr>
							<td>
								<div id="divParentStatusVSDateContainer"></div>
								<script language="JavaScript">var strParentStatusVSDateJSON = <?php echo $strParentStatusJSON?>;</script>
							</td>
						</tr>
					</table>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<div class="row">
	<div class="col s6">
		<table class="bordered highlight responsive-table">
			<thead>
				<tr>
					<th width="100%">Status v/s Date</th>
			  </tr>
			</thead>
			<tbody>
				<tr>
					<table border="0" width="100%">
						<tr>
							<td><H1 style="color:gray; text-align:center;">Lead By Source and Date</H1></td>
						</tr>
					</table>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="col s6">
		<table class="bordered highlight responsive-table">
			<thead>
				<tr>
					<th width="100%">Source v/s Date</th>
			  </tr>
			</thead>
			<tbody>
				<tr>
					<table border="0" width="100%">
						<tr>
							<td><H1 style="color:gray; text-align:center;">Lead By Source and Date</H1></td>
						</tr>
					</table>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<div class="row">
	<div class="col s12">
		<table class="bordered highlight responsive-table">
			<thead>
				<tr>
					<th width="100%">Result</th>
			  </tr>
			</thead>
			<tbody>
				<tr>
					<table border="0" width="100%">
						<thead>
							<?php $intColumnCount = 0;?>
							<?php if(!empty($strColumnsArr)){?>
								<?php unset($strColumnsArr['date_range']);?>
								<?php foreach($strColumnsArr as $strColumnsArrKey => $strColumnsArrValue){?>
									<th><?php echo $strColumnsArrValue['label']?></th>
									<?php $intColumnCount++;?>
								<?php }?>
							<?php }?>
						</thead>
						<tbody>
							<?php 
								/* Checking is data set is empty or not */
								if((isset($strDataArr['data'])) && (!empty($strDataArr['data']))){
									/* Iterating the loop */
									foreach($strDataArr['data'] as $strDataArrKey => $strDataArrValue){
										echo '<tr>';
										
										/* Iterating the column loop */
										foreach($strColumnsArr as $strColumnsArrKey => $strColumnsArrValue){
											
											/* Setting value of respactive columns */
											$strValue	= isset($strDataArrValue[$strColumnsArrValue['column']])?$strDataArrValue[$strColumnsArrValue['column']]:'-';
											/* checking is data type column */
											if(isset($strColumnsArrValue['is_date'])){
												/* formatting the value */
												$strValue	= getDateFormat($strValue);
											}
											echo '<td>'.$strValue.'</td>';
										}
										echo '</tr>';
									}
									
								}else {
									echo getNoRecordFoundTemplate($intColumnCount);
								}
							?>
							<tr>
							</tr>
						</tbody>
					</table>
					<?php echo $pagination;?>
				</tr>
			</tbody>
		</table>
	</div>
</div>

<!-- Add /Edit Modal Structure -->
<div id="<?php echo $strDataAddEditPanel?>" class="modal modal-fixed-footer">
    <div class="modal-content">
		<h4><span class="spnActionText">Add New</span> <?php echo $moduleTitle?></h4>
		<?php echo $strColumnSearchPanel?>
	</div>
	<div class="modal-footer">
    	<a href="javascript:void(0);" class="modal-action modal-close waves-effect waves-green btn-flat">Cancel</a>
		<button class="btn waves-effect waves-light cmdSearchReset green lighten-2 hide" type="submit" name="cmdAddNewLeadsCancel" id="cmdSearchLeadLeadsClearFilter" formName="<?php echo $moduleForm?>" >Clear Filter<i class="material-icons right">find_replace</i></button>
    	<button class="btn waves-effect waves-light cmdDMLAction" type="submit" name="cmdLeasSearchPanel" id="cmdLeasSearchPanel" formName="<?php echo $moduleForm?>" >Submit<i class="material-icons right">send</i></button>
    </div>
</div>