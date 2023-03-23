<?php
class Trnf_summary_model extends CI_Model{
	public function __construct()
		{		
		$this->load->database();
	}

	public function get_details_by_id($pk){
	//called by trnf_summary/summary
	$sql=$this->db->select('*');
	$sql=$this->db->from('trnf_summary');
	$sql=$this->db->where('id',$pk);
	$sql=$this->db->get();
	return $sql->row_array();	
	}

	public function add($data){
	//called by trnf_details/send_complete	
	if($this->db->insert('trnf_summary',$data)):
		return true;
	else:
		return false;
	endif;

	}


	public function get_max_id(){
	//called by trnf_details/send_complete	
	$sql=$this->db->select_max('id');
	$sql=$this->db->from('trnf_summary');
	$sql=$this->db->get();
	return $sql->row_array();
	}


	public function trnf_summary_per_id($id){
		//called by Trnf_summary/view_details
		$this->db->select('ts.id, ts.date, lfr.name as from, lto.name as to');
		$this->db->from('trnf_summary ts');
		$this->db->join('locations lfr', 'ts.from_id = lfr.id');
		$this->db->join('locations lto', 'ts.to_id = lto.id');
		$this->db->where('ts.id',$id);
		$sql = $this->db->get();
		return $sql->row_array();
	}

}
