<html>
<style>
.header {
  width: 100%;
 
  color: #ffffff;
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
echo "<tr><td align=center bgcolor=white>".$id." ".$title." ".$rate."</td></tr></table>";
?>

<table style="width:100%; border-collapse:collapse" border=1></style>
<?php
echo "<tr><td width=10% bgcolor=white>Date</td><td width=70% bgcolor=white>Details</td><td width=10% bgcolor=white>Quantity</td><td width=10% bgcolor=white>Balance</td></tr></table>";
?>
</div>
<div class="content">
<table style="width:100% ; border-collapse: collapse" border=1></style>
<?php 
foreach ($show_stck as $row):
echo "<tr><td width=10%>".$row['date']."</td><td width=70%>".$row['document']."</td><td width=10%>".$row['qty']."</td><td width=10%>".$row['balance']."</td></tr>";
endforeach;
echo "<tr><td colspan=8 align=center><a href=".site_url('Item/item').">Go to Item List</a></td></tr>";
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
