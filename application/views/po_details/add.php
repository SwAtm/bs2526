<html>
<style>
th {
	background-color: yellow;
	position: sticky; 
    top: 0px; 
    padding: 0px 0px; 
    height: 10px;
    font-size: small;
}

tfoot {
	background-color: yellow;
	position: sticky; 
    bottom: 0px;
    height: 25px;

}

</style>



<div>
<?php
//print_r($id);
//echo "<br>";
//print_r($items);
echo "<br>";

echo "<form method = POST action = ".site_url('po_details/add').">";
echo "<table align = center border=1 width = 100%>";
echo "<tr><td colspan = 5 align = center>Purchase Order to ".$party['name']." - ".$party['city']."</td></tr>";
echo "<th>Item id</th><th>Name</th><th>Rate</th><th>CL Bal</th><th>Order</th>";
$i=0;
foreach ($items as $item):
$rate=$item['myprice']+($item['myprice']*$item['gstrate']/100);
if(0==$rate):
$rate='';
endif;
echo "<tr><td>$item[id]</td><td>$item[title]</td><td>$rate</td><td>$item[clbal]</td><td><input type = number name = podet[$i][quantity]></td></tr>";
echo "<input type = hidden name = podet[$i][item_id] value = $item[id]>";
echo "<input type = hidden name = podet[$i][rate] value = $rate>";
$i++;
endforeach;
echo "<input type = hidden name = id value =$id>";
?>
<tfoot>
<?php
echo "<tr><td colspan = 5 align = center><input type = submit name = submit value = Submit></td></tr></table>";
//echo "<tr><td align = right><input type = submit name = submit value = Submit></td></tr></table>";
echo "</form>";
?>
</tfoot>
</div>
</html>
