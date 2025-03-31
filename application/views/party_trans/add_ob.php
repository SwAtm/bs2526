<?php
echo validation_errors();
echo "Form to Add Opening Balance"."<Br>";

echo "<table border=1>";
echo "<tr><td>Party</td><td>Amount</td><td>Dr/Cr</td><td></td></tr>";
echo form_open('Party_trans/add_ob');
echo "<tr><td>";
echo form_dropdown('party_id',$pty);
echo "</td><td>";
echo form_input('amount','');
echo "</td><td>";
echo form_label('Debit','Debit');
echo form_radio(array('name'=>'drcr', 'value'=>'Debit', 'id'=>'Debit'));
echo form_label('Credit','Credit');
echo form_radio(array('name'=>'drcr', 'value'=>'Credit', 'id'=>'Credit'));
echo "</td><td>";
echo form_submit('submit','Submit');
echo "</td></tr>";
echo form_close();
echo "</table>";
//print_r($pty);
?>
