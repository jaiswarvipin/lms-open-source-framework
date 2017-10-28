<table class="responsive-table">
	<thead>
		<tr>
			<th width="100%">Top Employee</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>
				<table width="100%" class="bordered highlight responsive-table">
					<thead>
						<th>Employee Name</th>
						<th>Region</th>
						<th>Branch</th>
						<th>Open</th>
						<th>Closed</th>
						<th>%</th>
					</thead>
					<tbody>
						<?php
							/* Variable initialization */
							$intNumberOfCell 	= 1;
							$strHTML 			= '';
							
							/* Iterating for parent status */
							if(!empty($strResultArr)){
								/* Iterating the loop */
								foreach($strResultArr['data'] as $strResultArrKey => $strResultArrValue){
									$strRegionName	= isset($strResultArr['region'][getEncyptionValue($strResultArrValue['region_code'])])?$strResultArr['region'][getEncyptionValue($strResultArrValue['region_code'])]:'-';
									$strBranchName	= isset($strResultArr['branch'][getEncyptionValue($strResultArrValue['branch_code'])])?$strResultArr['branch'][getEncyptionValue($strResultArrValue['branch_code'])]:'-';
									$strHTML 	.= '<tr>';
									$strHTML 	.= '<td>'.$strResultArrValue['name'].'</td>';
									$strHTML 	.= '<td>'.$strRegionName.'</td>';
									$strHTML 	.= '<td>'.$strBranchName.'</td>';
									$strHTML 	.= '<td>'.$strResultArrValue['open'].'</td>';
									$strHTML 	.= '<td>'.$strResultArrValue['closed'].'</td>';
									$strHTML 	.= '<td>'.$strResultArrValue['value'].'</td>';
									$strHTML 	.= '</tr>';
								}
								/* set HTML */
								echo $strHTML;
							}else{
								echo getNoRecordFoundTemplate(6);
							}
						?>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>