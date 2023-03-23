<html>
<style>
.header {
  width: 100%;
 
  //color: #ffffff;
}
.content {
  width: 100%;
  padding-top: 10px;
}
.sticky {
  position: fixed;
  top: 0;
  width: 100%;
}
</style>
<div class="header" id="myHeader">
<table style="width:100%; border-collapse:collapse" border=1></style>
<?php
echo "<tr><td width=10% bgcolor=white>Inv Id</td><td width=10% bgcolor=white>Code</td><td width=10% bgcolor=white>Rate</td><td width=30% bgcolor=white>Title</td><td width=10% bgcolor=white>Cl Bal</td><td width=10% bgcolor=white>Stock</td><td width=10% bgcolor=white>Diff Qty</td><td width=10% bgcolor=white>Diff Amt</td></tr></table>";
?>
</div>
<div class="content">
<table style="width:100% ; border-collapse: collapse" border=1></style>
<?php 
$tdiffamt=0;
foreach ($inventory as $row):
$diffqty = $row['clbal']-$row['stock'];
$diffamt = ($row['clbal']-$row['stock'])*$row['rate'];
echo "<tr><td width=10% bgcolor=white>$row[id]</td><td width=10% bgcolor=white>$row[code]</td><td width=10% bgcolor=white>$row[rate]</td><td width=30% bgcolor=white>$row[title]</td><td width=10% bgcolor=white>$row[clbal]</td><td width=10% bgcolor=white>$row[stock]</td><td width=10% bgcolor=white>$diffqty</td><td width=10% bgcolor=white>$diffamt</td></tr>";
$tdiffamt+=$diffamt;
endforeach;
//echo "<tr><td colspan=8 align=center><a href=".site_url('Item/item').">Go to Item List</a></td></tr>";
echo "<tr><td colspan = 8>Total Difference: $tdiffamt</td></tr>";
echo "</table>";

?>
</div>



<script>
window.onscroll = function() {myFunction()};

var header = document.getElementById("myHeader");
var sticky = header.offsetTop;

function myFunction() {
  if (window.pageYOffset > sticky) {
    header.classList.add("sticky");
  } else {
    header.classList.remove("sticky");
  }
}
</script>

</html>
