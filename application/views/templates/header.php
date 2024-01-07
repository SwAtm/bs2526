<!DOCTYPE html>
<html>
<head>

<style type="text/css">
input:focus, select:focus, checkbox:focus {
      outline:3px solid magenta;
}
</style>
<style>
div {
	 margin: auto;
	 text-align: center;
	}
</style>
<script type = "text/javascript" >
   function preventBack(){window.history.forward();}
    setTimeout("preventBack()", 0);
    window.onunload=function(){null};
</script>
</head>
<body>
<?php
echo "<table width=100%  bgcolor=lightblue cellpadding=5>";
echo "<tr><td align=middle><b>".$this->session->cname." ".$this->session->ccity."</b></td></tr>";
echo "<tr><td align = middle id = time>";
echo "</td></tr>";
//echo "<tr><td align=middle>".date('l jS \of F Y h:i:s A')."</td></tr>";
echo "<tr><td>";

//echo "<tr><td align=middle>".date('l jS \of F Y h:i:s A')."</td></tr>";
if (isset($this->session->loc_id)||!empty($this->session->loc_id)):
//if ((!null == LOC_NAME)||!empty(LOC_NAME)):
//echo "";
echo "<tr><td align = right>Logged in as ".$this->session->loc_name." <a href = ".site_url('Welcome/logout').">Log Out</a></td></tr></table>";
else:

echo "Please Log in";
//redirect(site_url('welcome/home'));
redirect('welcome/logout');
echo "</table>";
endif;
?>
<script type="text/javascript">
function Timer() {
   var dt=new Date();
   document.getElementById('time').innerHTML=dt.getDate()+ '-' + (dt.toLocaleString('default', {month: 'long'}))+ '-'+dt.getFullYear()+'||'+dt.getHours()+":"+dt.getMinutes()+":"+dt.getSeconds();
   setTimeout("Timer()",1000);
}
Timer();
</script>
<?php
/*
if (null!==$this->session->logged AND $this->session->logged=='admin'):
		echo "Logged in as ".$this->session->logged."<a href=".site_url('login/logout')."> Log Out</a>";
else:
//echo "Logged in as Guest. Log in as <a href=".site_url('login/index')."> Admin</a>";
echo '';
endif;
*/
?>



</body>
</html>
