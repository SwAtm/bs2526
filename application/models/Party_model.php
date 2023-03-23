<?php
class Party_model extends CI_Model{
	public function __construct()
		{		
		$this->load->database();
	}

	public function getall(){
	//called by trns_details/purch_complete_details, trns_details/sales_complete_details, trns_summary/summary_edit, trns_summary/trns_search
	$sql=$this->db->select('*');
	$sql=$this->db->from('party');
	$sql=$this->db->get();
	return $sql->result_array();
	}

	public function get_details($id){
	//trns_details/sales_complete_details, trns_summary/summary_edit, trns_details/purch_complete_details, reports/print_bill, party_trans/ind_ledger
	$sql=$this->db->select('*');
	$sql=$this->db->from('party');
	$sql=$this->db->where('id', $id);
	$sql=$this->db->get();
	return $sql->row_array();	

	}

	public function getall_unregd(){
	//called by trns_summary/summary_edit	
	$sql=$this->db->select('*');
	$sql=$this->db->from('party');
	$sql=$this->db->where('status !=','REGD');
	$sql=$this->db->get();
	return $sql->result_array();	
	}

	
	



/*
 *public function getall_in_array($party){
		//called by trnf_details/send_complete

	$sql=$this->db->select('*');
	$sql=$this->db->from('party');
	$sql=$this->db->where_in('id', $party);
	$sql=$this->db->get();
	return $sql->result_array();	
	}

public function get_details_by_code($code){
		//called by trnf_details/receive
	$this->db->select('*');
	//$this->db->from('party')
	$this->db->where('code',$code);
	$result = $this->db->count_all_results('party');
	if ($result>0):
		return true;
	else:
		return false;
	endif;
	

	}
	*	public function add($arr){
		//called by trnf_details/receive
		$this->db->insert('party',$arr);
	}
	* 
	* public function get_id_from_code($code){
		//called by trnf_details/receive
		$this->db->select('id');
		$this->db->where('code',$code);
		$result = $this->db->get('party');
		return $result->row()->id;

	}
*/
}
