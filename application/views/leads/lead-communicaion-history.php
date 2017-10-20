<div id="divLeadCommunicationHistoryDetails">
	<div class="row">
		<div class="col s12">
			<table class="bordered highlight responsive-table">
				<thead>
					<tr>
						<th width="20%">Date</th>
						<th>Lead Owner Name</th>
						<th>Task Type</th>
						<th>Status</th>
						<th width="40%">Comments</th>
				  </tr>
				</thead>
				<tbody>
					<?php if(!empty($strCommunicaionHistoryArr)){?>
						<?php foreach($strCommunicaionHistoryArr as $strCommunicaionHistoryArrKey => $strCommunicaionHistoryArrValue){?>
							<tr>
								<td><?php echo getDateFormat($strCommunicaionHistoryArrValue['record_date'])?></td>
								<td><?php echo $strCommunicaionHistoryArrValue['lead_owner_code']?></td>
								<td><?php echo $strCommunicaionHistoryArrValue['status_code']?></td>
								<td><?php echo $strCommunicaionHistoryArrValue['status_code']?></td>
								<td><?php echo $strCommunicaionHistoryArrValue['comments']?></td>
							</tr>
						<?php }?>
					<?php }else{
						echo getNoRecordFoundTemplate(5);
					}?>
				</tbody>
			</table>
		</div>
	</div>
</div>