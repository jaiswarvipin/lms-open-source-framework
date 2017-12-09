<table class="bordered highlight responsive-table">
	<thead>
		<tr>
			<th width="100%">Pending Task Count</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="f100 centre"><?php echo (isset($strResultArr[0]['newLeadCount'])?$strResultArr[0]['newLeadCount']:getNoRecordFoundTemplate(1));?></h1></td>
		</tr>
	</tbody>
</table>