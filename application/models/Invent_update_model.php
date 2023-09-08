<?php
class Invent_update_model extends CI_Model{
	public function __construct()
		{		
		$this->load->database();
	}

	public function gettrans(){
	$sql="select trns_details.id, trns_details.item_id, trns_details.myprice, trns_details.inventory_id, inventory.location_id as invlocid, locations.id as trnlocid, series.tran_type_name, trns_details.quantity from
	trns_details join trns_summary on trns_details.trns_summary_id=trns_summary.id 
	join series on trns_summary.series=series.series
	join inventory on trns_details.inventory_id=inventory.id
	join locations on series.location_name=locations.name
	where locations.id!=inventory.location_id";
	$res=$this->db->query($sql);
	return $res->result_array();
	}
	
	public function gettrnf(){
	$sql="select trnf_details.id, trnf_details.item_id, trnf_details.myprice, trnf_details.quantity, trnf_summary.from_id as trnlocid, inventory.location_id as invlocid, trnf_details.inventory_id from 
	trnf_details join trnf_summary on trnf_details.trnf_summ_id=trnf_summary.id
	join inventory on trnf_details.inventory_id=inventory.id
	where inventory.location_id!=trnf_summary.from_id";
	$res=$this->db->query($sql);
	return $res->result_array();
	
	}
	
	public function select_inv($item_id, $myprice, $locid){
	$this->db->select('id, clbal');
	$this->db->from('inventory');
	$this->db->where('item_id',$item_id);
	$this->db->where('myprice', $myprice);
	$this->db->where('location_id',$locid);
	$this->db->order_by('id ASC');
	$sql=$this->db->get();
	return $sql->result_array();
}
	
	public function update_wrong_inventory($inventory_id, $quantity){
	$this->db->set('out_qty', 'out_qty-'.$quantity,false);
	$this->db->set('clbal','opbal+in_qty-out_qty',false);
	$this->db->where('id',$inventory_id);
	$this->db->update('inventory');
	}
	
	
	public function update_right_inventory($inventory_id, $quantity){
	$this->db->set('out_qty', 'out_qty+'.$quantity,false);
	$this->db->set('clbal','opbal+in_qty-out_qty',false);
	$this->db->where('id',$inventory_id);
	$this->db->update('inventory');
	}

	public function update_tns_trn($filename, $id, $rightinventory){
	$this->db->set('inventory_id',$rightinventory);
	$this->db->where('id',$id);
	$this->db->update($filename);
	}
	
	
	
	


}
?>
