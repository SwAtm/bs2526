<?php
class Po_summary extends CI_Controller{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->helper('url');
		//$this->load->helper('form');
		//$this->load->library('form_validation');
		//$this->load->library('table');
		//$this->output->enable_profiler(TRUE);
		$this->load->library('grocery_CRUD');
		$this->load->library('session');
	}

	public function summary()
	{
		$crud = new grocery_CRUD();
		$crud->set_table('po_summary')
		     ->set_subject('PO')
			 ->set_theme('datatables')
			 ->columns('id', 'date', 'party_id', 'remark')
			 ->display_as('id','PO Number')
			->display_as('date','Date')
			->display_as('party_id','Party Name')
			->display_as('remark','Remark')
			->set_relation('party_id','party','{name}--{city}')
			->add_action('Add Details','','po_details/add')
			->add_action('Print', '', 'po_details/print1')
			->required_fields('party_id','date')
			->callback_before_insert(array($this, 'callback_remark'));
			
		$output = $crud->render();
		$this->_example_output($output);                
	}
	
	
	function _example_output($output = null)
	{
		$this->load->view('templates/header');
		$this->load->view('our_template.php',$output);    
		$this->load->view('templates/footer');
	}    
	
	
	public function callback_remark($post)
	{
	
	$post['remark']=ucfirst($post['remark']);
	return $post;
	
	
	}
	/*
	public function check_in_use($id)
	{
	$sql=$this->db->select('*');
	$sql=$this->db->from('item');
	$sql=$this->db->where('party_id',$id);
	$res=$this->db->get();
	if ($res && $res->num_rows()>0):
		return true;
	else:
		$sql=$this->db->select('*');
		$sql=$this->db->from('trns_summary');
		$sql=$this->db->where('party_id',$id);
		$res=$this->db->get();
			if ($res && $res->num_rows()>0):
			return true;
			else:
				$sql=$this->db->select('*');
				$sql=$this->db->from('profo_summary');
				$sql=$this->db->where('party_id',$id);
				$res=$this->db->get();
				if ($res && $res->num_rows()>0):
				return true;
				else:
				return false;
				endif;
			endif;
	endif;
	}
	
	public function delete_check($primary_key)
	{
	if ($this->check_in_use($primary_key)):
	return false;
	else:
	return true;
	endif;
	
	}
	
	
	


	public function checkgst($gstno){
	$err=0;
	$gstno=$this->input->post('gstno');
	$status=$this->input->post('status');
	//$gcat_name=$this->db->select('*')->where('id',$gcat_id)->get('gst_cat')->row()->name;
	if ('UNRD'== strtoupper($status)):
		if (''!=$gstno):
		$err=1;
		endif;
	else:
		if (''==$gstno):
		$err=1;
		endif;
	endif;
	if ($err):	
	$this->form_validation->set_message('checkgst','Mismatch between GST Number and GST Status');
		return false;
	else:
		return true;
	endif;
	}

*/
}
?>
