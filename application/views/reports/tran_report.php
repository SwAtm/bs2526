<html>
<body>
<form onsubmit = "return chkdt();" action = "tran_report" method = "Post">
<table border = 1 align = "center">
<?php echo validation_errors();
?>
<tr><td align = center colspan = 13>Range 31 days max</td></tr>
<tr><td colspan = 4 align = "right"><label for = "frdate">From</label></td>
<td><input type = "date" required id = "frdate" name = "frdate" value = "<?php echo Date('Y-m-d')?>"></td>
<td colspan = 4 align = "right"><label for = "todate">To</label></td>
<td colspan = 4 align = "right"><input type = "date" required id = "todate" name  = "todate" value = "<?php echo Date('Y-m-d')?>"> </td></tr>
<tr><td>Report Type</td>
<td><label for = "date">Datewise</label></td>
<td><input type = "radio" id = "date" name = "rtype" value = "date" required <?php echo (!isset($_POST['rtype']) or 'date'==$_POST['rtype']) ? "checked" : '' ?>></td>
<td><label for = "bill">Billwise</label></td>
<td><input type = "radio" id = "bill" name = "rtype" value = "bill" required <?php echo (isset($_POST['rtype']) and 'bill'==$_POST['rtype']) ? "checked" : '' ?>></td>
</tr>
<tr><td>Transaction Type</td>
<?php

foreach ($series as $s):
$checked = (('Cash' == $s['payment_mode_name'] or 'UPI' == $s['payment_mode_name']) and 'Sales' == $s['tran_type_name']) ? "checked" : "";
//echo $checked;
echo "<td><label for = ".$s['series'].">".$s['payment_mode_name']." - ".$s['tran_type_name']."</label></td>";
echo "<td><input type = checkbox id = ".$s['series']." name = ttype[] value = ".$s['series']." ".$checked."></td>";
endforeach;
echo "<tr>";
?>
<tr><td colspan = 13 align = "center"><input type = "submit" name = "submit" value = "Submit"></td></tr>
</table>
</form>
<script>
var frdatehandle = document.querySelector('#frdate');
var todatehandle = document.querySelector('#todate');

function chkdt(){
console.log(frdatehandle);
var frdate = new Date(frdatehandle.value).getTime();
var todate = new Date(todatehandle.value).getTime();
var diff = todate - frdate;
console.log(diff);
//console.log(todate);
if ((frdate > todate ) || (diff>(31*24*60*60*1000))){
	alert ("To date is earlier than From Date/Diff is more than 31 days");
return false;
	} else {
return true;
}

}
//todatehandle.addEventListener('change',chkdt);

</script>

</body></html>
<?php






?>
