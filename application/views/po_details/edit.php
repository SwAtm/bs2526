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
<?php
//print_r($id);
echo "<br>";
//print_r($party);
echo "<br>";
echo "<form method = POST action = ".site_url('po_details/edit').">";
echo "<table align = center border=1>";
echo "<tr><td colspan = 5 align = center>Purchase Order No.".$id." to ".$party['name']." - ".$party['city']."</td></tr>";

echo "<th>Item id</th><th>Name</th><th>Rate</th><th>CL Bal</th><th>Order</th>";
$i=0;

foreach ($items as $item):
$rate=number_format($item['myprice']+($item['myprice']*$item['gstrate']/100),2,".",",");
$added=0;
foreach ($addeditems as $aitem):
  if ($aitem['item_id']==$item['id'] and $rate==$aitem['rate']):
 echo "<tr><td>$item[id]</td><td>$item[title]</td><td>$rate</td><td>$item[clbal]</td><td><input type = number name = podet[$i][quantity] value = $aitem[quantity]></td></tr>";
 $added=1;
 endif;
 
endforeach;
if (0==$added):
 echo "<tr><td>$item[id]</td><td>$item[title]</td><td>$rate</td><td>$item[clbal]</td><td><input type = number name = podet[$i][quantity]></td></tr>";
 endif;
//echo "<tr><td>$item[id]</td><td>$item[title]</td><td>$rate</td><td>$item[clbal]</td><td><input type = number name = podet[$i][quantity]></td></tr>";
echo "<input type = hidden name = podet[$i][item_id] value = $item[id]>";
echo "<input type = hidden name = podet[$i][rate] value = $rate>";
$i++;
endforeach;
echo "<input type = hidden name = id value =$id>";
?>
<tfoot>
<?php
echo "<tr><td colspan = 5 align = center><input type = submit name = submit value = Submit></td></tr></table>";
echo "</form>";
?>
</tfoot>
</html>
