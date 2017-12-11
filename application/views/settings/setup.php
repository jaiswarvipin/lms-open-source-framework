<div class="row">
	<div class="col s12">
		<table class="bordered highlight  responsive-table">
	        <thead>	
	          <tr>
	              <th width='5%'>#</th>
	              <th>Setting Name</th>
	              <th>Why needed?</th>
	              <th>Status</th>
	          </tr>
	        </thead>

	        <tbody>
				<?php 
					if(!empty($dataArr)){
						
						/* checking is message index set - for no ADMIN user */
						if(isset($dataArr['message'])){
							/* Display message */
							echo '<tr><td colspan="4">'.$dataArr['message']."</td></tr>";
						}else{
							
							/* variable initialization */
							$strTable	= "";
							/* Iterating the loop */
							foreach($dataArr as $dataArrKey => $dataArrValue){
								/* Status */
								$strStatuString = ((isset($dataArrValue['status']) && ($dataArrValue['status']))?'check':'cancel');
								
								/* Setting value */
								$strTable	.= "<tr><td>".$dataArrKey."</td><td>".$dataArrValue['label']."</td><td>".$dataArrValue['description']."</td><td><i class='material-icons small'>".$strStatuString."</i></td></tr>";
							}
						}
					}else{
						$strTable	= '<tr><td colspan="3">Environment is up-to-date</td></tr>';
					}
					
					echo $strTable;
				?>
	        </tbody>
	      </table>
	</div>
</div>