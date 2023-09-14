<script>
	window.onload = function(){
	let amt = document.querySelector('#amt');
	amt.focus();
}

</script>
<style>
.tb {

width: 100%;
border: 1px solid black;	
}
.tb tr, .tb th, .tb td {
border: 1px solid black;	
}
</style>

<table class = "tb">

	<tr><th>Total</th><th style="width: 15%">Settle for</th><th style="width: 25%">Party</th><th style="width: 15%">Select Transaction Type</th><th style="width: 15%">Expenses</th><th style="width: 15%">Remark</th></tr><tr>

<?php
echo "<form method = POST action = ".site_url("trns_details/ec_complete").">";

?>
<td><input type = number size = 15 name = amount value = <?php echo $amount ?> readonly ></td>
<td><input type = number size = 15 name = amt value = <?php echo round($amount,0) ?> id = amt max = <?php $amount?>></td>
<td>
<?php
echo "<select name = party id = party required>";
foreach ($party as $p):
	echo "<option value = $p[id]>$p[name] -- $p[city]</option>";
	if ($p[id]==122):
		echo "<option value = $p[id] selected>$p[name] -- $p[city]</option>";
	endif;
endforeach;
echo "</select>";
echo "</td><td><select name = series id = series required>";
foreach ($series as $s):
	echo "<option value = $s[id]>$s[payment_mode_name] -- $s[tran_type_name]</option>";
endforeach;
echo "</select>";
?>
</td>
<td><input type = number size = 15 name = expenses></td>
<td><input type = text size = 15 name = remark></td></tr><tr>

<td align = center colspan="3"><input type = submit name =  submit id = submit  value = 'Submit'></td>
<td align = center colspan="3"><input type = submit name =  cancel id = cancel  value = 'Cancel Bill'></td></tr>
<?php

echo "</form>";
echo "</table>";


?>

