<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" lan="en">
</head>
<body>
<div style="margin:0 auto; width:50%;">
<p>
<?php
echo validation_errors();
echo form_open('Welcome/verify');?>
<label for="user">Select User</label>
<Select name = 'user'>
<tr><td>
<?php
foreach ($location as $location1):
echo "<option value=".$location1['id'].">".$location1['name']."</option>";
endforeach;?>
</Select>
<input type = "submit" value = "Submit">
</form>
</p>
</div>

</body>
</html>
