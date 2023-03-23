<style>
	#content:{
		margin: 0 auto;
		text-align: center;

	}
</style>
<div id ='content'>
<script type="text/javascript">
	window.onload = function(){
	let toid = document.querySelector('#to_id');
	toid.focus();
}
</script>
<?php


echo form_open('trnf_details/send_complete');
echo form_label('Please select location to send <br><br>','to_id');
echo form_dropdown('to_id',$loc,'',array('id'=>'to_id'));
echo form_submit('submit','Submit');
echo form_close();
//print_r($loc);


?>
</div>