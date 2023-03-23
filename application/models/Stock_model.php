<?php
class Stock_model extends CI_Model{
	public function __construct()
		{		
		$this->load->database();
	}

	public function add($data){
	//called by stock/add
	if($this->db->insert('stock',$data)):
		return true;
	else:
		return false;
	endif;
		
	}

	public function liststock(){
	//called by stock/list
	$sql=$this->db->select('*');
	$sql=$this->db->from('stock');
	$sql=$this->db->get();
	return $sql->result_array();
	}
	}
	
	public function printinventory(){
	//called by stock/printinventory, stock/viewinventory
	$sql = $this->db->select('item.code, item.title, item.gstrate, inventory.myprice, inventory.clbal, sum(stock.stock) as stock, inventory.id' );
	$sql = $this->db->from('stock');
	$sql = $this->db->join('inventory', 'inventory.id = stock.inventory_id');
	$sql = $this->db->join('item', 'item.id = inventory.item_id');
	$sql = $this->db->where('stock.location_id=',$this->session->loc_id);
	$sql = $this->db->group_by('inventory.id');
	$sql = $this->db->get();
	return $sql->result_array();	
	
	}	


}
