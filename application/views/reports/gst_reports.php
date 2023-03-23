<html>
<body>
<form onsubmit = "return chkdt();" action = "gstreports" method = "Post">
<table border = 1 align = "center">
<?php echo validation_errors();
?>
<tr>
<td colspan = 4 align = "right"><label for = "frdate">From</label></td>
<td><input type = "date" required id = "frdate" name = "frdate" value = "<?php echo Date('Y-m-d')?>"></td>
<td colspan = 4 align = "right"><label for = "todate">To</label></td>
<td colspan = 4 align = "right"><input type = "date" required id = "todate" name  = "todate" value = "<?php echo Date('Y-m-d')?>"> </td></tr>
<tr><td colspan = 13 align = "center"><input type = "submit" name = "submit" value = "Submit"></td></tr>


<script>
var frdatehandle = document.querySelector('#frdate');
var todatehandle = document.querySelector('#todate');

function chkdt(){
//console.log(frdatehandle);
var frdate = new Date(frdatehandle.value);
var todate = new Date(todatehandle.value);
//console.log(frdate.getTime());
if (frdate.getTime()>todate.getTime()){
	alert ("To date is earlier than From Date");
return false;
	} else {
return true;
}
}
//todatehandle.addEventListener('change',chkdt);

</script>

</body></html>


