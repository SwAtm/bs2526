<?php
class Trns_summary_model extends CI_Model{
	public function __construct()
		{		
		$this->load->database();
	}

	public function get_max_no($sr){
	//called by trns_details/sales_complete_details, trns_details/purch_complete_details
	$sql=$this->db->select_max('no');
	$sql=$this->db->from('trns_summary');
	$sql=$this->db->where('series',$sr);
	$sql=$this->db->get();
	return $sql->row_array();
	}

	public function add($data){
	//called by trns_details/sales_complete_details, trns_details/purch_complete_details
	if($this->db->insert('trns_summary',$data)):
		return true;
	else:
		return false;
	endif;
		
	}

	public function get_max_id(){
	//called by trns_details/sales_complete_details, trns_details/purch_complete_details
	$sql=$this->db->select_max('id');
	$sql=$this->db->from('trns_summary');
	$sql=$this->db->get();
	return $sql->row_array();	
	}

	public function get_details_by_id($pk){
		//called by trns_summary/summary_edit, trns_details/check_editable, reports/print_bill, trns_summary/view_details
	$sql=$this->db->select('trns_summary.*, series.tran_type_name, series.payment_mode_name');
	$sql=$this->db->from('trns_summary');
	$sql=$this->db->join('series','trns_summary.series = series.series');
	$sql=$this->db->where('trns_summary.id',$pk);
	$sql=$this->db->get();
	return $sql->row_array();	
	}

	public function update($data, $id){
	//called by trns_summary/summary_edit
	$sql=$this->db->where('id',$id);
	
	if($sql=$this->db->update('trns_summary',$data)):
		return true;
	else:
		return false;
	endif;
	}

	public function delete($id){
	//called by trns_details/edit_purchase_add, trns_details/edit_sales_add
	$data=array('expenses'=>0, 'remark'=>'Cancelled');
	$sql=$this->db->where('id',$id);
	$sql=$this->db->update('trns_summary',$data);
	
	}


	


}
