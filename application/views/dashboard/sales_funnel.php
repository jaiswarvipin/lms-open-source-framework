<table class="bordered highlight responsive-table">
	<thead>
		<tr>
			<th width="100%">Sales Funnel</th>
	  </tr>
	</thead>
	<tbody>
		<tr>
			<?php 
				if(empty($strResultArr)){
						echo getNoRecordFoundTemplate(1);
				}else{
					/* Variable initialization */
					$strHTMLArr	= array(OPEN_CLOSURE_STATUS_CODE=>array('count'=>0,'older'=>0,'new'=>0,'html'=>''),NEGATIVE_CLOSURE_STATUS_CODE=>array('count'=>0,'html'=>''),POSITIVE_CLOSURE_STATUS_CODE=>array('count'=>0,'html'=>''));
					
					
					/* checking for open status list */
					if((isset($strResultArr['statusArr'])) && (!empty($strResultArr['statusArr']))){
						/* Iterating the status loop */
						foreach($strResultArr['statusArr'] as $strArrayParentKey => $strStatusArr){
							/* based on the key setting the value */ 
							switch($strArrayParentKey){
								case OPEN_CLOSURE_STATUS_CODE:
									/* Checking for open lead status count */
									if(isset($strResultArr['data'][OPEN_CLOSURE_STATUS_CODE])){
									/* Iterating the loop */
										foreach($strResultArr['data'][OPEN_CLOSURE_STATUS_CODE] as $strResultArrKey => $strResultArrValue){
											/* Setting open lead count */
											$strHTMLArr[OPEN_CLOSURE_STATUS_CODE]['count']	+= $strResultArrValue['leadCount'];
											
											/* if lead creational date is same as yesterday then do needful */ 
											if((int)$strResultArr['intDate'] == (int)$strResultArrValue['lead_date']){
												$strHTMLArr[OPEN_CLOSURE_STATUS_CODE]['new']	+= $strResultArrValue['leadCount'];
											}else{
												/* Setting the froworded date count */
												$strHTMLArr[OPEN_CLOSURE_STATUS_CODE]['older']	+= $strResultArrValue['leadCount'];
											}
										}
									}
									break;
								case NEGATIVE_CLOSURE_STATUS_CODE:
								case POSITIVE_CLOSURE_STATUS_CODE:
									/* Checking for negative lead status count */
									if(isset($strResultArr['data'][$strArrayParentKey])){
									/* Iterating the loop */
										foreach($strResultArr['data'][$strArrayParentKey] as $strResultArrKey => $strResultArrValue){
											/* Setting negative lead count */
											$strHTMLArr[$strArrayParentKey]['count']	+= $strResultArrValue['leadCount'];
										}
									}
									break;
							}
							
							/* Setting the HTML */
							if(!empty($strStatusArr)){
								/* Iterating the loop */
								foreach($strStatusArr as $strStatusArrKey => $strStatusArrValue){
									/* Checking is requested sun status is set in result set */
									if(isset($strResultArr['data'][$strArrayParentKey][$strStatusArrKey])){
										/* Setting HTML */
										$strHTMLArr[$strArrayParentKey]['html']	.= '<tr><td>'.$strStatusArrValue.'</td><td>'.$strResultArr['data'][$strArrayParentKey][$strStatusArrKey]['leadCount'].'</td></tr>';
									}else{
										/* Setting HTML */
										$strHTMLArr[$strArrayParentKey]['html']	.= '<tr><td>-</td></tr>';
									}
								}
							}
						}
					}
					echo '<table border="0" width="100%">
							<tr>
								<td width="25%">
									<table border="0">
										<tr><td>Open Lead forworded '.$strHTMLArr[OPEN_CLOSURE_STATUS_CODE]['older'].'</td></tr>
										<tr><td>Open Lead : '.$strHTMLArr[OPEN_CLOSURE_STATUS_CODE]['new'].'</td></tr>
									</table>
								</td>
								<td width="25%">
									<table border="0">
										<tr><td>Total Open Leads: '.$strHTMLArr[OPEN_CLOSURE_STATUS_CODE]['count'].'</td></tr>
										<tr>
											<td>
												<table width="100%">'.$strHTMLArr[OPEN_CLOSURE_STATUS_CODE]['html'].'</table>
											</td>
										</tr>
									</table>
								</td>
								<td width="25%">
									<table border="0">
										<tr><td>Total Nagative Closed Leads: '.$strHTMLArr[NEGATIVE_CLOSURE_STATUS_CODE]['count'].'</td></tr>
										<tr>
											<td>
												<table width="100%">'.$strHTMLArr[NEGATIVE_CLOSURE_STATUS_CODE]['html'].'</table>
											</td>
										</tr>
									</table>
								</td>
								<td width="25%">
									<table border="0">
										<tr><td>Total Positive Closed Leads: '.$strHTMLArr[POSITIVE_CLOSURE_STATUS_CODE]['count'].'</td></tr>
										<tr>
											<td>
												<table width="100%">'.$strHTMLArr[POSITIVE_CLOSURE_STATUS_CODE]['html'].'</table>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>';
				}
			?>
		</tr>
	</tbody>
</table>