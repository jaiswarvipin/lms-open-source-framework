<table class="responsive-table">
	<thead>
		<tr>
			<th width="100%">Top Performing <?php echo $strLabel?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>
				<table width="100%" class="bordered highlight responsive-table">
					<thead>
						<th>Name</th>
						<th>Open Lead</th>
						<th>Close Lead</th>
						<th>%</th>
					</thead>
					<tbody>
						<?php
							/* Variable initialization */
							$strHTML	= '';
							if((isset($strResultArr['data'])) && (!empty($strResultArr['data']))){
								/* Iterating the loop */
								foreach($strResultArr['data'] as $strResultArrKey => $strResultValueArr){
									$strHTML	.= '<tr>';
									$strHTML	.= '<td>'.$strResultValueArr['name'].'</td>';
									$strHTML	.= '<td>'.$strResultValueArr['open'].'</td>';
									$strHTML	.= '<td>'.$strResultValueArr['close'].'</td>';
									$strHTML	.= '<td>'.$strResultValueArr['value'].'</td>';
									$strHTML	.= '</tr>';
								}
								echo $strHTML;
							}else{
								echo getNoRecordFoundTemplate(4);
							}
						?>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>