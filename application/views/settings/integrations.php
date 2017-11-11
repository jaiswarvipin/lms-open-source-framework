<div class="row">
	<div class="col s12">
		<?php
			$strIntegrationParameters = '';
		?>
		<table class="bordered highlight  responsive-table">
			<tr>
				<td width="20%">API BASE URL: </td>
				<td><pre><?php echo SITE_URL?><pre></td>
			</tr>
			<tr>
				<td>Parameters Description</td>
				<td>
					<table width="100%" class="bordered responsive-table">
						<tr>
							<th>API key</th>
							<th>Name</th>
							<th>Default Value</th>
							<th>Value List</th>
							<th>Mandatory</th>
							<th>Acceptable Data Type</th>
						</tr>
						<?php 
							/* Variable initialization */
							$strParameters		= '';
							$strLeadSourceList	= array();
							$intCompanyCode		= 0;
							/* If dataset is not empty then do not needful */
							if(!empty($dataSet)){
								/* Iterating the loop */
								foreach($dataSet['lead_attri'] as $dataSetKey => $dataSetValue){
									if($strParameters == ''){
										$strParameters	= '?'.$dataSetValue['attri_slug_key'].'={value}';
									}else{
										$strParameters	.= '&'.$dataSetValue['attri_slug_key'].'={value}';
									}
									/* Setting company Code */
									$intCompanyCode	= $dataSetValue['company_code'];
									echo '
											<tr>
												<td>'.$dataSetValue['attri_slug_key'].'</td>
												<td>'.$dataSetValue['attri_slug_name'].'</td>
												<td>'.$dataSetValue['attri_default_value'].'</td>
												<td>'.jsonReturn($dataSetValue['attri_value_list']).'</td>
												<td>'.(($dataSetValue['is_mandatory'] == 0)?'No':'Yes').'</td>
												<td>'.$dataSetValue['attri_validation'].'</td>
											</tr>
										';
								}
								
								/* Checking for lead source */
								if((isset($dataSet['lead_source'])) && (!empty($dataSet['lead_source']))){
									/* Iterating the loop */
									foreach($dataSet['lead_source'] as $dataSetKey => $dataSetValue){
										/* Setting Lead source Value */
										$strLeadSourceList[]	= $dataSetValue['description'];
									}
								}
								
								echo '
											<tr>
												<td>lead_source_code</td>
												<td>Lead source details</td>
												<td>Website</td>
												<td>'.jsonReturn(array_values($strLeadSourceList)).'</td>
												<td>Yes</td>
												<td>String</td>
											</tr>
										';
							}
						?>
					</table>
				</td>
			</tr>
			<tr>
				<td width="15%">Integration URL: </td>
				<td><pre><?php echo SITE_URL.$strParameters.'&lead_source_code={value}&company_code='.getEncyptionValue($intCompanyCode)?><pre></td>
			</tr>
			<tr>
				<td width="15%">Instruction: </td>
				<td>
					<ul>
						<li><b>Acceptable Data Type:</b></li>
						<li>
							<ul style="margin-left: 10px;">
								<li><b>String:</b> Alphanumeric set of characters are acceptable. [e.q. A-Z, 0-9]</li>
								<li><b>Numeric:</b> Only numbers (integer and decimal) are acceptable. [e.q. 1, 100, 12.93, 12.984, 973]</li>
								<li><b>Date:</b> Only numbers (integer) are acceptable in YYYYMMDD format. [e.q. 20171031 == 2017/10/31, 2017101 == 2017/10/01]</li>
								<li><b>Time:</b> Only numbers (integer) are acceptable in HHIISS format. [e.q. 123454 == Hours-12, Min-34 and Sec.-54, 230017 == Hours-23, Min-00 and Sec.-17 ]</li>
								<li><b>Email:</b> only value email is acceptable. [e.q. jon@domain.com, user.name@domainname.io]</li>
							</ul>
						</li>
						<li><b>Value List:</b> If any API Key is having value(s) in the list, then only suggested value are only acceptable. These value are case sensitive. Kindly contact to system to add new value in the system, in case needed.</li>
						<li><b>API Key:</b>This remain unchanged. Its should be same it is, because these are mapping fields to system</li>
						<li><ul style="margin-left: 10px;"><li><b>company_code:</b>Do not modify it. It's Key for handshaking between 2 system(s).</li></ul></li>
					</ul>
				</td>
			</tr>
	    </table>
	</div>
</div>