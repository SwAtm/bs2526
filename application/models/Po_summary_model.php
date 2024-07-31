<?php
class Po_summary_model extends CI_Model{
	public function __construct()
		{		
		$this->load->database();
	}

	public function get_details($id){
	//called by Po_details/add
	$sql=$this->db->select('*');
	$sql=$this->db->from('po_details');
	$sql=$this->db->where('po_summary_id',$id);
	$sql=$this->db->get();
	if ($sql->num_rows()>0):
	return $sql->result_array();
	else:
	return false;
	endif;
	}

	public function get_party($id){
		//called by po_details/add, po_details/print, po_details/edit
		$this->db->select('party.*, po_summary.date');
		$this->db->from('party');
		$this->db->join('po_summary','party.id =  po_summary.party_id');
		$this->db->where('po_summary.id',$id);
		$sql = $this->db->get();
		return $sql->row_array();
	}


	public function get_items($partyid){
		//called by po_details/add, po_details/edit
		
		/*
		$this->db->select('item.*, inventory.myprice, sum(inventory.clbal) as clbal');
		$this->db->from('item');
		$this->db->join('inventory', 'item.id = inventory.item_id');
		$this->db->where('item.party_id',$partyid);
		$this->db->group_by('inventory.item_id, inventory.myprice');
		$sql = $this->db->get();
		return $sql->result_array();*/
		$sql="select item.*, inv.myprice, inv.clbal from
		item left join
		(select inventory.myprice, inventory.item_id, sum(inventory.clbal) as clbal from inventory group by item_id, myprice) as inv
		on item.id=inv.item_id
		where item.party_id=?";
		return $this->db->query($sql, array($partyid))->result_array();
		}







	public function add($arr){
		//called by po_details/add
		if ($this->db->insert_batch('po_details',$arr)):
		return true;
		else:
		return false;
		endif;
	}

	public function get_details_print($id){
	//called by po_details/print
	$this->db->select('po_details.rate, po_details.quantity, item.title');
	$this->db->from('po_details');
	$this->db->join('item', 'item.id = po_details.item_id');
	$this->db->where('po_details.po_summary_id',$id);
	$sql=$this->db->get();
	return $sql->result_array();
	
	}
	public function get_items_edit($id){
	$this->db->select('*');
	$this->db->from('po_details');
	$this->db->where('po_details.po_summary_id',$id);
	$sql=$this->db->get();
	return $sql->result_array();
	}

	public function delete($id){
	if(	$this->db->delete('po_details', array('po_summary_id'=>$id))):
	return true;
	else:
	return false;
	endif;
	
	}
}
