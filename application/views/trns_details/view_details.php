<!DOCTYPE html>
<html>
<head>
<style>
th {
	background-color: yellow;
	position: sticky; 
    top: 0px; 
    padding: 0px 0px; 
    height: 10px;
    font-size: small;
}
</style>
</head>
<body>
	<table border = 1 align = center><th>Item_id</th><th>Item Code</th><th>Title</th><th>Rate</th><th>Quantity</th><th>Discount</th><th>Cash Discount</th><th>Amount</th><th>HSN</th><th>GST Rate</th>
	<?php
	$amt = 0;
	foreach ($trns_details as $key => $v) {
		echo "<tr><td>$v[item_id]</td><td>$v[code]</td><td>$v[title]</td><td>".number_format($v['rate'],2)."</td><td>$v[quantity]</td><td>".number_format($v['discount'],2)."</td><td>".number_format($v['cash_disc'],2)."</td><td>".number_format($v['amount'],2)."</td><td>$v[hsn]</td><td>".number_format($v['gst_rate'],2)."</td></tr>";
		$amt+=$v['amount'];
	}
	echo "<tr><td colspan = 10 align = center>Amount + Expenses: ".number_format($amt+$expenses,2)."</td></tr>";
	echo "<tr><td colspan = 10 align = center><a href = ".site_url('trns_summary/summary').">Back to List</a href></td></tr>";
	echo "</table>";
?>

</body>
</html>	
