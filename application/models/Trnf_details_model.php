<?php
class Trnf_details_model extends CI_Model{
	public function __construct()
		{		
		$this->load->database();
	}

	public function confirm_zero_entry($field, $value){
		//called by trns_details/check_editable
		$sql = $this->db->where($field,$value);
		$sql = $this->db->from('trnf_details');
		$sql = $this->db->count_all_results();
		if ($sql>0):
			return false;
		else:
			return true;
		endif;
	}

	public function add($data){
	//called by trnf_details/send_complete	
	if($this->db->insert('trnf_details',$data)):
		return true;
	else:
		return false;
	endif;

	}

	public function trnf_details_per_id($pk){
		//called by Trnf_summary/view_details
	$sql = $this->db->select('trnf_details.quantity, trnf_details.item_id, item.title, trnf_details.rate ');
	$sql = $this->db->from('trnf_details');
	$sql = $this->db->join('item','item.id = trnf_details.item_id');
	$sql = $this->db->where('trnf_details.trnf_summ_id',$pk);
	$sql = $this->db->get();
	return $sql->result_array();

	}

	public function get_trnf_out($id, $myprice){
	//called by Item/det_stck
	$sql = $this->db->select('trnf_details.trnf_summ_id, trnf_details.quantity, locations.name, trnf_summary.date');
	$sql = $this->db->from('trnf_details');
	$sql = $this->db->join('trnf_summary', 'trnf_details.trnf_summ_id = trnf_summary.id');
	$sql = $this->db->join('locations', 'trnf_summary.to_id = locations.id');
	$sql = $this->db->where('trnf_summary.from_id', $this->session->loc_id);
	$sql = $this->db->where('trnf_details.item_id', $id);
	$sql = $this->db->where('trnf_details.myprice', $myprice);
	$sql = $this->db->get();
	return $sql->result_array();
	}
	
	public function get_trnf_in($id, $myprice){
	//called by Item/det_stck
	$sql = $this->db->select('trnf_details.trnf_summ_id, trnf_details.quantity, locations.name, trnf_summary.date');
	$sql = $this->db->from('trnf_details');
	$sql = $this->db->join('trnf_summary', 'trnf_details.trnf_summ_id = trnf_summary.id');
	$sql = $this->db->join('locations', 'trnf_summary.from_id = locations.id');
	$sql = $this->db->where('trnf_summary.to_id', $this->session->loc_id);
	$sql = $this->db->where('trnf_details.item_id', $id);
	$sql = $this->db->where('trnf_details.myprice', $myprice);
	$sql = $this->db->get();
	return $sql->result_array();
	}

}
