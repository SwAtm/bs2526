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
<tr align=left><th>Item Id</th><th>Title</th><th>Rate</th><th>Cl Bal</th><th></th></tr>
</th></tr>
</thead>
<?php	
//echo "<a href =".site_url('item/item').">Item List</a href>";
	foreach ($stock as $st):
	$extra_col = "<a href =".site_url("item/det_stck/$st[id]/$st[myprice]").">View Details</a href>";
	echo "<tr><td>$st[id]</td><td>$st[title]</td><td>".number_format($st['rate'],2,'.',',')."</td><td>$st[clbal]</td><td>$extra_col</td></tr>";
	endforeach;
?>
<tfoot>
<tr align=center><th colspan=5><?php echo "<a href =".site_url('item/item').">Item List</a href>";?></th></tr>
</tfoot>

<?php
echo "</table>";
?>
</html>
