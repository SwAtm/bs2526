<script>
	window.onload = function(){
	let title = document.querySelector('#title');
	title.focus();
	let ratehandle = document.querySelector('#rate');
	let hsnhandle = document.querySelector('#hsn');
	//let gstratehandle = document.querySelector('#gstrate');
	let cbhandle = document.querySelector("#cb");
	let btnhandle = document.querySelector('#complete');
	function fillvalues(){
		cbhandle.innerHTML = '';
		//let	details = title.options[title.selectedIndex].value;
		let	details = title.value;
		if ('' === details){
			btnhandle.disabled = false;
		} else {
			details1 = JSON.parse(details);
			ratehandle.value = details1.rate;
			hsnhandle.value = details1.hsn;
			//gstratehandle.value = details1.grate;
			cbhandle.innerHTML = details1.title+'-'+details1.rate+' '+'Closig Balance: '+ details1.clbal + ' HSN: ' + details1.hsn + ' GST Rate: ' + details1.gstrate + '%';
			console.log(details1);
		
			btnhandle.disabled = true;
			
		}
}
	title.addEventListener('change',fillvalues);
	

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
	<tr><th>Title</th><th style="width: 11%">Quantity</th><th style="width: 5%"></th></tr><tr>

<?php
$items = array();
foreach ($invent as $key) {
	$items[] = array ('title' => $key['title']. "-".$key['rate'], 'value' => json_encode($key));
}
echo "<form method = POST action = ".site_url("trnf_details/send/").">";
?>
<tr><td><input list = "xyz" name = "item" id = "title" size = 45 autocomplete="off" required>
<datalist id = "xyz">
<?php
foreach ($items as $key) {
	echo "<option value = '$key[value]'>$key[title]</option>";
}
echo "</td>";
/*


echo "<td><Select name = item required id = title>";
echo "<option value=''>Title</option>";
foreach ($items as $key) {
	echo "<option value = '$key[value]'>$key[title]</option>";
}
echo "</select>";
echo "</td>";
* */
?>
<td><input type = number size = 15 name = quantity value = 1 min="1"></td>
<input type = hidden id = hsn>
<!--<input type = hidden id = gstrate>-->
<input type = hidden id = rate>
<td><input type = submit name = add value = Add></td></tr>
<tr><td id = cb></td>
<td align = center colspan="4"><input type = submit name =  complete id = complete formnovalidate="formnovalidate" value = 'Transfer Over'></td></tr>
<?php
echo "</form>";
echo "</table>";

?>

