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
	
	public function get_max_no($series){
	//called by party_trans/getnumber
	$sql=$this->db->select_max('no');
	$sql=$this->db->from('party_trans');
	$sql=$this->db->where('series',$series);
	$sql=$this->db->get();
	$row=$sql->row();
	if(isset($row)):
	return $row->no;
	else:
	return false;
	endif;
	}	
	
	public function get_details_by_id($id){
	//called by party_trans/printvr, party_trans/delete_confirm
	$sql=$this->db->select('party_trans.*, party.name, party.add1, party.add2, party.city, party.pin, party.state, party.i_e');
	$sql=$this->db->from('party_trans');
	$sql=$this->db->join('party', 'party.id=party_trans.party_id');
	$sql=$this->db->where('party_trans.id',$id);
	$sql=$this->db->get();
	return $sql->row_array();
	}

	public function add($data){
	//called by party_trans/add_ob
	if($this->db->insert('party_trans', $data)):
		return true;
	else:
		return false;
	endif;
	}
		
	public function delete($id){
	//called by party_trans/delete
	$sql=$this->db->set('amount',0);
	$sql=$this->db->set('remark','Cancelled');
	$sql=$this->db->where('id',$id);
	$sql=$this->db->update('party_trans');
	if($sql):
	return true;
	else:
	return false;
	endif;
	
	}	
}
