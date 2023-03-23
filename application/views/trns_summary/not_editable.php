<?php
echo "Sorry, This bill cannot be edited<br>";
echo "It could be due to any one or more of the following reasons:<br>";
echo "<ul>";
echo "<li>It is a Cancelled Bill</li>";
echo "<li>It is a Cash bill of an earlier date</li>";
echo "<li>It is a Non-Cash bill of earlier month</li></ul>";

echo "<a href = ".site_url('Trns_summary/summary').">Back to List</a href>";
?>
