<?php
echo "<table width = 100% border = 1>";
echo "<tr><td>Party_id</td><td>Party</td><td>Opn Bal</td><td>Sales</td><td>Purchase</td><td>Receipts</td><td>Payments</td><td>Balance</td></tr>";
foreach ($ledger as $led):
$sales=$led['salepr']+$led['salesexp'];
$purchase=$led['purchsr']+$led['purchexp'];
$balance=$led['obl']+$sales-$led['rpt']-$purchase+$led['pmt'];
echo "<tr><td>$led[id]</td><td>$led[name]-$led[city]</td><td>$led[obl]</td><td>$sales</td><td>$purchase</td><td>$led[rpt]</td><td>$led[pmt]</td><td><a href =".site_url('Party_trans/ind_ledger/'.$led['id']).">".round($balance,2)."</a></td></tr>";
endforeach;
//print_r($ledger);
echo "</table>";
?>
