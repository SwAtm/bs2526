<!DOCTYPE html>
<html>
<head>
	<title></title>
<style type="text/css">
	#tb{
width: 100%;
border: 1px solid black;
	}
	#tb tr, #tb td, #tb th{
		border: 1px solid black;
	}
</style>
</head>
<body>
<table id = "tb">
	<tr><th>Transactino No:</th><th>Date</th><th>Party</th><th>Expenses</th><th>Remark</th></tr>
<?php echo "<tr><td>$series: $no</td><td>$date</td>";
echo "<form method = POST action =".site_url("trns_summary/summary_edit/$pk").">";
if ($partychange == 'No'):
echo "<td>$party_name</td>";
echo "<input type = hidden name = party value = $party_id>";
else:
echo "<td><select name = party id = party required>";
echo "<option value = ''>Select Party</option>";
foreach ($party as $k):
echo "<option value = $k[id]";
if ($k['id'] == $party_id):
	echo "selected = selected";
endif;
echo">$k[name]-$k[city]-$k[gstno]</option>";
endforeach;
endif;
echo "</td><td><input type = number name = expenses value =".number_format($expenses,2)."></td>";
echo "<td><input type = text name = remark size = 25 maxlength = 50 value ='$remark'></td></tr>";
echo "<input type = hidden name = id value = $pk>";
echo "<input type = hidden name = series_id value = $series_id>";
echo "<input type = hidden name = no value = $no>";
echo "<input type = hidden name = date value = $date>";
echo "<input type = hidden name = series value = $series>";
echo "<tr><td colspan= 5 align = center><input type = submit name = submit value = Submit></td></tr>";

echo "</form>";
?>	
</table>
<script>
	window.onload = function(){
	let party = document.querySelector('#party');
	party.focus();
}
</script>
</body>
</html>












