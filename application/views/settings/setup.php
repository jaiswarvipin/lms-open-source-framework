<div class="row">
	<div class="col s12">
		<table class="bordered highlight  responsive-table">
	        <thead>	
	          <tr>
	              <th width='5%'>#</th>
	              <th>Setting Name</th>
	              <th>Why needed?</th>
	          </tr>
	        </thead>

	        <tbody>
				<?php 
					if(!empty($dataArr)){
						/* variable initialization */
						$strTable	= "";
						/* Iterating the loop */
						foreach($dataArr as $dataArrKey => $dataArrValue){
							/* Setting value */
							$strTable	.= "<tr><td>".$dataArrKey."</td><td>".$dataArrValue['label']."</td><td>".$dataArrValue['description']."</td></tr>";
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