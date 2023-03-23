<script>
window.onload = function(){
var pr = document.querySelector('#party');
pr.focus();	}
</script>
<style>
#tbb {
table-layout: fixed;
width: 100%;
border: 1px solid black;	
}
th {
	background-color: yellow;
	position: sticky; 
    top: 0px; 
    padding: 0px 0px; 
    height: 10px;
    font-size: small;
}
</style>

<table id = tbb border = 1>
<caption>Please delete necessary records. Only deletable records have a checkbox. If you do not wish to delete or there is nothing to delete, just update</caption>
<th style="width: 35%">Title</th><th style="width: 10%">Rate</th><th style="width: 10%" >Quantity</th><th style="width: 10%" >Discount</th><th style="width: 10%">Cash Disc</th><th style="width: 10%">HSN</th><th style="width: 10%">GST Rate</th><th style="width: 5%">Delet</th>
<?php
echo "<form method = POST action = ".site_url("trns_details/edit_delet").">";
echo "<tr>";
//If its a purchase, rows with delet=1 should have checkbox. Others should have only quantity field editable.
//If its other than purchase, all rows will have checkbox. No field is editable.
$i = 0;
foreach ($details as $key => $value) {
echo "<td><input name = det[$i][title] required id = title value = '$value[title]' readonly>";

echo "</td>";

echo "<td style='width:10%'><input type = number size = 13 maxlength = 11 name = det[$i][rate] required step = 0.01 value = $value[rate] readonly></td>";

if ("Purchase" == $tran_type and 0 == $value['delet']): 
echo "<td style=width:10%><input type = number size = 13 name =det[$i][quantity] required value = $value[quantity]></td>";
else:
echo "<td style=width:10%><input type = number size = 13 name =det[$i][quantity] required value = $value[quantity] readonly></td>";
endif;

echo "<td style=width:10%><input type = number size = 13 name = det[$i][discount] step = 0.01 placeholder = 0.00 value = $value[discount] readonly></td>";
echo "<td style=width:10%><input type = number size = 13 name = det[$i][cash_disc] step = 0.01 placeholder = 0.00 value = $value[cash_disc] readonly></td>";
echo "<td style=width:10%><input type = number size = 13 maxlength = 14 name = det[$i][hsn] required value = $value[hsn] readonly></td>";
echo "<td style=width:10%><input type = number size = 13 name = det[$i][gst_rate] required step = 0.01 placeholder = 0.00 value = $value[gst_rate] readonly></td>";

if (("Purchase" == $tran_type and 1 == $value['delet']) or "Purchase" != $tran_type):
echo "<td style=width:5%><input type = checkbox id = delete name = det[$i][delete] value = 1></td></tr>";
else:
echo "<td></td></td></tr>";
endif;

echo "<input type = hidden id = item_id name = det[$i][item_id] value = $value[item_id]>";
echo "<input type = hidden id = code name = det[$i][code] value = $value[code]>";
echo "<input type = hidden id = id name = det[$i][id] value = $value[id]>";
echo "<input type = hidden id = inventory_id name = det[$i][inventory_id] value = $value[inventory_id]>";
$i++;
}
echo "<tr>";

echo "<td style='width: 45%' colspan = 3 align = center><input type = submit name =  update value = 'Update'></td>";
echo "<td style='width: 45%' colspan = 2 align = center><input type = submit name =  cancel value = 'Cancel'></td></tr></table>";

echo "</form>";

?>
