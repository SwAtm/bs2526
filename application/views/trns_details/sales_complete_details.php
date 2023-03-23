<!Doctype HTML>
	<!DOCTYPE html>
	<html>
	<head>
		<title></title>
<script>
	window.onload = function() {
	let partyhandle = document.querySelector('#party');
	partyhandle.focus();
}
</script>
<style>
	#tb{
		width: 100%;
		border: 1px solid black;	
	}
	#tb tr, #tb td{
		border: 1px solid black;
	}
</style>

	</head>
	<body>
<table id = tb >
	<caption>Please complete the transaction</caption>
	<tr><th>Party</th><th style = "width: 20%">Transaction Type</th><th style = "width: 20%">Expenses</th><th style = "width: 20%">Remarks</th></tr>
	
	
		<?php
		echo "<tr><td><form method = POST action = ".site_url("trns_details/sales_complete_details").">";
		echo "<select name = party id = party required>";
		echo "<option value = '' >Select Party</option>";
		foreach ($party as $p):
			echo "<option value = $p[id]>$p[name] -- $p[city]</option>";
			if ($p[id]==122):
			echo "<option value = $p[id] selected>$p[name] -- $p[city]</option>";
			endif;
		endforeach;
		echo "</select>";
		echo "</td><td><select name = series id = series required>";
		echo "<option value = ''>Select Transaction Type</option>";
		foreach ($series as $s):
			echo "<option value = $s[id]>$s[payment_mode_name] -- $s[tran_type_name]</option>";
		endforeach;
		echo "</select>";
		echo "</td><td><input type = number name = expenses></td><td><input type = text name = remark></td></tr>";
		echo "<tr><td colspan = 2 align = center><input type = submit name = finalize value = Finalize></td>";
		?>
		<td align = center colspan="2"><input type = submit name =  cancel id = cancel formnovalidate="formnovalidate" value = 'Cancel Bill'></td></tr></table>
<?php
		echo "</form>";
?>



	</body>
	</html>



