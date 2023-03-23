<?php
class Series_model extends CI_Model{
	public function __construct()
		{		
		$this->load->database();
	}

	public function get_series($location_name, $payment_mode_name, $tran_type_name){
	//called by trns_details/purch_edit_details, trns_details/purch_complete_details
	$sql=$this->db->select('id, series');
	$sql=$this->db->from('series');
	$sql=$this->db->where('location_name', $location_name);
	$sql=$this->db->where('payment_mode_name', $payment_mode_name);
	$sql=$this->db->where('tran_type_name', $tran_type_name);
	$sql=$this->db->get();
	if ($sql and $sql->num_rows()>0):
	return $sql->row_array();
else:
	return false;
endif;
	}


	public function get_payment_mode_name($series){
		//called by Trns_summary/summary
	$sql=$this->db->select('payment_mode_name');
	$sql=$this->db->from('series');
	$sql=$this->db->where('series',$series);
	$sql=$this->db->get();
	return $sql->row();
	}
	
	public function get_series_by_location()	{
		//called by trns_details/sales_complete_details
	$sql=$this->db->select('*');
	$sql=$this->db->from('series');
	$sql=$this->db->where('location_name',$this->session->loc_name);
	$sql=$this->db->where('tran_type_name','Sales');
	$sql=$this->db->get();
	if ($sql and $sql->num_rows()>0):
	return $sql->result_array();
else:
	return false;
endif;
	
	}

	public function get_series_details($id){
		//called by trns_details/sales_complete_details, trns_summary/summary_edit
	$sql=$this->db->select('*');
	$sql=$this->db->from('series');
	$sql=$this->db->where('id',$id);
	$sql=$this->db->get();
	return $sql->row_array();	
	}

	public function get_all_series_by_location()	{
		//called by trns_summary/summary, reports/tran_report
	$sql=$this->db->select('*');
	$sql=$this->db->from('series');
	$sql=$this->db->where('location_name',$this->session->loc_name);
	$sql=$this->db->get();
	return $sql->result_array();

}

	public function get_details_from_series($series){
	$sql=$this->db->select('*');
	$sql=$this->db->from('series');
	$sql=$this->db->where('series', $series);
	$sql=$this->db->get();
	return $sql->row_array();
	}
	
	public function get_all_sales_series(){
	//called by reports/gstreports
	$sql=$this->db->select('series');
	$sql=$this->db->from('series');
	$sql=$this->db->where('tran_type_name', 'Sales');
	$sql=$this->db->get();
	return $sql->result_array();
	}

}
