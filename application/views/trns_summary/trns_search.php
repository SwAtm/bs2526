<?php
echo "<Table border=1 width=100%>";
echo "<form method=POST action=".site_url("trns_summary/trns_search").">";
echo "<tr><td colspan=2 align=center>Please enter Transaction Number or Select Party</td></tr>";
echo "<tr><td>Transaction No: </td><td><input type=numeric name=trno ></td></tr>";
echo "<tr><td colspan=2 align=center>OR</td></tr>";
echo "<tr><td>Party</td><td><select name=party>";
echo "<option value = ''>Select Party</option>";
foreach ($party as $p):
echo "<option value = $p[id]>$p[name] -- $p[city]</option>";
endforeach;
echo "</select></td></tr>";
echo "<tr><td colspan=2 align=center><input type=submit name=submit value=Submit></td></tr>";
echo "</form>";
echo "</Table>";

?>
