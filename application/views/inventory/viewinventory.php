<html>
<style>
table thead {
position: sticky;
top: 0;
background: pink;
}
table tfoot {
position: sticky;
bottom: 0;
background: pink;
}
</style>
<table style="width:100%; border-collapse:collapse" border=1></style>
<thead>
<tr align=left><th>Item Id</th><th>Title</th><th>Rate</th><th>Cl Bal</th><th>Stock</th></th><th>Clbal-Stock</th></tr>
</thead>
<?php	
//echo "<a href =".site_url('item/item').">Item List</a href>";
	foreach ($invent as $st):
	//$extra_col = "<a href =".site_url("item/det_stck/$st[id]/$st[myprice]").">View Details</a href>";
	echo "<tr><td>$st[item_id]</td><td>$st[title]</td><td>".number_format($st['rate'],2,'.',',')."</td><td>$st[clbal]</td><td>$st[stock]</td><td>".number_format(($st['clbal']-$st['stock']),0)."</td></tr>";
	endforeach;
?>
<tfoot>
<tr align=center><th colspan=6><?php echo "<a href =".site_url('welcome/home').">Go Home</a href>";?></th></tr>
</tfoot>

<?php
echo "</table>";
?>
</html>
