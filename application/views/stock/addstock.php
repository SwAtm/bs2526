<script>
	window.onload = function(){
	let title = document.querySelector('#title');
	title.focus();
	//let ratehandle = document.querySelector('#rate');
	//let hsnhandle = document.querySelector('#hsn');
	//let gstratehandle = document.querySelector('#gstrate');
	let stockhandle = document.querySelector('#stock');
	let cbhandle = document.querySelector("#cb");
	let btnhandle = document.querySelector('#complete');
	function fillvalues(){
		cbhandle.innerHTML = '';
		stockhandle.innerHTML = '';
		let	details = title.options[title.selectedIndex].value;
		if ('' === details){
			btnhandle.disabled = false;
		} else {
			details1 = JSON.parse(details);
			//ratehandle.value = details1.rate;
			//hsnhandle.value = details1.hsn;
			//gstratehandle.value = details1.grate;
			cbhandle.innerHTML ='Closig Balance: '+ details1.clbal;
			stockhandle.innerHTML = details1.stock;	
			console.log(details1);
			if (parseInt(details1.stock,10)<parseInt(details1.clbal,10)){
			stockhandle.style.color='blue';
			console.log(typeof details1.clbal);
			}else{
				stockhandle.style.color='red';
			}
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

	<tr><th>Title</th><th>Entered Till Now</th><th style="width: 11%">Quantity</th><th style="width: 5%">Remark</th></tr><tr>

<?php
$items = array();
foreach ($invent as $key) {
	$items[] = array ('title' => $key['title']. "-".$key['rate'], 'value' => json_encode($key));
}

	echo "<form method = POST action = ".site_url("stock/add").">";

echo "<td><Select name = item required id = title>";
echo "<option value=''>Title</option>";
foreach ($items as $key) {
	echo "<option value = '$key[value]'>$key[title]</option>";
}
echo "</select>";
echo "</td>";
?>

<td id = stock></td>
<td><input type = number size = 15 name = quantity required value = 1 min="1"></td>
<td><input type = text size = 30 name = remark></td>
<td><input type = submit name = add value = Add></td>
</tr>
<tr><td id = cb></td>
<td align = center colspan="2"><input type = submit name =  complete id = complete formnovalidate="formnovalidate" value = 'Over for Now'></td>

<?php

echo "</form>";
echo "</table>";

?>

