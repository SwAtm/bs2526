<?php

$countstck = count($stock);
if (0 == $countstck):
	echo "This Item has no stock<br>";
else:
	$template = array(
        'table_open' => '<table border="1" cellpadding="2" cellspacing="1" class="mytable" align="center">'
);

$this->table->set_template($template);
	//$this->table->set_caption($stock[0]['title']);
	$this->table->set_heading('Item Id','Title','Rate','Closing Balance','');

	foreach ($stock as $st):
		$extra_col = "<a href =".site_url("item/det_stck/$st[id]/$st[myprice]").">View Details</a href>";
		$row_to_add = array($st['id'], $st['title'], number_format($st['rate'],2,'.',','), $st['clbal'], $extra_col);
		$this->table->add_row($row_to_add);
	endforeach;
	echo $this->table->generate();
endif;	
echo "<a href =".site_url('item/item').">Item List</a href>";

?>
