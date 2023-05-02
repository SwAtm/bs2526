<?php
class Location_model extends CI_Model{
	public function __construct()
		{		
		$this->load->database();
	}

	public function getall(){
	//called by Welcome/index, trns_details/discountreport
	$sql=$this->db->select('*');
	$sql=$this->db->from('locations');
	$sql=$this->db->get();
	return $sql->result_array();
	}
	
	public function getdetails($id){
	//called by Welcome/start, verify
	$sql=$this->db->select('*');
	$sql=$this->db->from('locations');
	$sql=$this->db->where('id',$id);
	$sql=$this->db->get();
	return $sql->row_array();
	}

	public function get_list_except_loggedin($id){
	//called by trnf_details/send_complete	
	$sql=$this->db->select('*');
	$sql=$this->db->from('locations');
	$sql=$this->db->where('id!=',$id);
	$sql=$this->db->get();
	return $sql->result_array();
	}



}
?>


