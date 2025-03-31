<?php
echo "You are deleting following Receipt:";
echo $details['series'].' - '.$details['no']."<br>";
echo $details['name'].' - '.$details['city']."<br>";
echo $details['amount']."<br>";
echo "<a href =".site_url('Party_trans/delete/'.$details['id']).">Delete</a>";
?>
