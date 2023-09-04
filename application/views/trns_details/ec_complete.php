<script>
	window.onload = function(){
	let amt = document.querySelector('#amt');
	title.focus();
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

	<tr><th>Total</th><th style="width: 25%">Settle for</th><th style="width: 25%"></th></tr><tr>

<?php
echo "<form method = POST action = ".site_url("trns_details/ec_complete").">";

?>
<td><input type = number size = 15 name = amount value = <?php echo $amount ?> readonly ></td>
<td><input type = number size = 15 name = amt value = <?php echo $amount ?> id = amt max = <?php $amount?>></td>
<td align = center colspan="2"><input type = submit name =  submit id = submit  value = 'Submit'></td>
<td align = center colspan="2"><input type = submit name =  cancel id = cancel  value = 'Cancel Bill'></td></tr>
<?php

echo "</form>";
echo "</table>";


?>

