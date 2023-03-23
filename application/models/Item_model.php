<?php
class Item_model extends CI_Model{
	public function __construct()
		{		
		$this->load->database();
	}

	public function getall(){
	//called by Trns_details/purch_add_details, Trns_details/edit_purchase_add
	$sql=$this->db->select('*');
	$sql=$this->db->from('item');
	$sql = $this->db->order_by('title ASC');
	$sql=$this->db->get();
	return $sql->result_array();
	}

	public function get_details_with_partycode($id){
		//called by trnf_details/send_complete, item/det_stck
		$this->db->select('item.*, party.code as pcode');
		$this->db->from('item');
		$this->db->join('party','party.id =  item.party_id');
		$this->db->where('item.id',$id);
		$sql = $this->db->get();
		return $sql->row_array();
	}


	public function check_if_exists($code){
		//called by trnf_details/receive
		$this->db->select('*');
		$this->db->where('code',$code);
		$result = $this->db->count_all_results('item');
		if ($result>0):
			return true;
		else:
			return false;
		endif;

	}

	public function add($arr){
		//called by trnf_details/receive
		$this->db->insert('item',$arr);
	}


}
