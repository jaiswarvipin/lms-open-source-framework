<table class="responsive-table">
	<thead>
		<tr>
			<th width="100%">Leads By <?php echo $strLabel?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>
				<table width="100%" class="bordered highlight responsive-table">
				<?php
					/* Variable initialization */
					$intNumberOfCell = 1;
					/* Iterating for parent status */
					if(isset($strResultArr['status']) && (!empty($strResultArr['status']))){
						echo '<tr><td>'.$strLabel.'(s)</td>';
						foreach($strResultArr['status'] as $strParentStatusKey => $strStatusArr){
							/* Setting colspan */
							$intNumberofColSpan	= (!empty($strStatusArr['child']))?count($strStatusArr['child']):1;
							/* Displaying the Parent Status Details */
							echo '<td colspan="'.$intNumberofColSpan.'">'.$strStatusArr['name'].'</td>';
						}
						echo '</tr>';
						
						echo '<tr><td>&nbsp;</td>';
						/* Iterating for parent status */
						foreach($strResultArr['status'] as $strParentStatusKey => $strStatusArr){
							/* Checking for child array */
							if(!empty($strStatusArr['child'])){
								/* Iterating for child status */
								foreach($strStatusArr['child'] as $strStatusArrKey => $strStatusArrValue){
									/* Cell incrementing */
									$intNumberOfCell++;
									/* Display child status */
									echo '<td>'.$strStatusArrValue.'</td>';
								}
							}else{
								/* Cell incrementing */
								$intNumberOfCell++;
								echo '<td>&nbsp;</td>';
							}
						}
						echo '</tr>';
					}
					
					if(isset($strResultArr[strtolower($strLabel)]) && (!empty($strResultArr[strtolower($strLabel)]))){
						/* iterating the loop */
						foreach($strResultArr[strtolower($strLabel)] as $strResultArrKey => $strResultArrValue){
							/* Setting the Region Name */
							echo '<tr><td>'.$strResultArrValue.'</td>';
							
							/* Iterating for parent status */
							foreach($strResultArr['status'] as $strParentStatusKey => $strStatusArr){
								/* Checking for child array */
								if(!empty($strStatusArr['child'])){
									/* Iterating for child status */
									foreach($strStatusArr['child'] as $strStatusArrKey => $strStatusArrValue){
										/* Checking is lead found for row */
										if(isset($strResultArr['data'][$strResultArrKey][$strStatusArrKey])){
											/* Display result */
											echo '<td>'.$strResultArr['data'][$strResultArrKey][$strStatusArrKey].'</td>';
										}else{
											/* Display No result */
											echo '<td>-</td>';
										}
									}
								}else{
									/* Display No result */
									echo '<td>-</td>';
								}
							}
						
							
							/* Closing the row */
							echo '</tr>';
						}

					}else{
						echo getNoRecordFoundTemplate($intNumberOfCell);
					}
				?>
				</table>
			</td>
		</tr>
	</tbody>
</table>