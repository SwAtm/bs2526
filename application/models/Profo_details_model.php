<?php
class Profo_details_model extends CI_Model{
	public function __construct()
		{		
		$this->load->database();
	}

	public function confirm_zero_entry($field, $value){
		//called by trns_details/check_editable
		$sql = $this->db->where($field,$value);
		$sql = $this->db->from('profo_details');
		$sql = $this->db->count_all_results();
		if ($sql>0):
			return false;
		else:
			return true;
		endif;


	}



}
