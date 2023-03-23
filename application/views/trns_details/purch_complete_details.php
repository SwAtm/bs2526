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
	<tr><th>Party</th><th style = "width: 20%">Expenses</th><th style = "width: 20%">Remarks</th></tr>
	
	
		<?php
		echo "<tr><td><form method = POST action = ".site_url("trns_details/purch_complete_details").">";
		echo "<select name = party id = party required>";
		echo "<option value = '' >Select Party</option>";
		foreach ($party as $p):
			echo "<option value = $p[id]>$p[name] -- $p[city]</option>";
		endforeach;
		echo "</select>";
		echo "</td><td><input type = number name = expenses></td><td><input type = text name = remark></td></tr>";
		echo "<tr><td colspan = 2 align = center><input type = submit name = finalize value = Finalize></td>";
		//echo "<td colspan = 2 align = center><input type = submit name = cancel value = Cancel formvalidate = formnovalidate></td></tr></table>";
		?>
		
		<td colspan = 2 align = center><input type = submit name =  cancel id = cancel formnovalidate="formnovalidate" value = 'Cancel Bill'></td>
		<?php
		echo "</form>";
?>



	</body>
	</html>



