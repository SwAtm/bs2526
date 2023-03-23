<?php
class Party_trans_model extends CI_Model{
	public function __construct()
		{		
		$this->load->database();
	}
	
	public function get_details_by_party($id){
	//called by party_trans/ind_ledger
	$sql=$this->db->select('*');
	$sql=$this->db->from('party_trans');
	$sql=$this->db->where('party_id',$id);
	$sql=$this->db->get();
	return $sql->result_array();
	
	
	}
	
	
}
