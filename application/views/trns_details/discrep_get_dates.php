<html>
<body>
<form onsubmit = "return chkdt();" action = "discountreport" method = "Post">
<table border = 1 align = "center">
<?php echo validation_errors();
?>
<tr><td align = center colspan = 4>Range 31 days max</td></tr>
<tr><td align = "right"><label for = "frdate">From</label></td>
<td><input type = "date" required id = "frdate" name = "frdate" value = "<?php echo Date('Y-m-d')?>"></td>
<td  align = "right"><label for = "todate">To</label></td>
<td  align = "right"><input type = "date" required id = "todate" name  = "todate" value = "<?php echo Date('Y-m-d')?>"> </td></tr>
<tr><td colspan = 4 align = "center"><input type = "submit" name = "submit" value = "Submit"></td></tr>
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
