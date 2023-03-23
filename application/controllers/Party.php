<?php
class Party extends CI_Controller{
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

	public function party()
	{
		$crud = new grocery_CRUD();
		$crud->set_table('party')
		     ->set_subject('Party')
			 ->columns('code', 'name', 'city', 'i_e', 'state', 'gstno')
			 ->display_as('code','Party Code')
			->display_as('name','Party Name')
			->display_as('city','City')
			->display_as('i_e','Branch')
			->display_as('state_io','State-In/Out')
			->display_as('state','State')
			->display_as('gstno','GST No')
			->display_as('status', 'Status')
			->unique_fields(array('code'))
			->field_type('i_e','dropdown',array('I'=>'Inter Branch', 'E'=>'External'))
			->field_type('state_io','dropdown',array('I'=>'Inside State','O'=>'Outside State'))
			->field_type('status', 'dropdown', array('REGD'=>'Registered', 'UNRD'=>'Un Registered', 'COMP'=>'Composition Dealer'))
			->set_lang_string('delete_error_message', 'This data cannot be deleted, because there is still a constrain data, please delete that constrain data first.');
			$crud->callback_before_delete(array($this,'delete_check'));
			$crud->set_rules('gstno', 'GST No', 'callback_checkgst');

			
			$operation=$crud->getState();
			if( $operation == 'add' || $operation == 'insert' || $operation == 'insert_validation'):
				$crud->required_fields('name','code', 'city', 'i_e', 'state_io','state', 'status');
				$crud->callback_before_insert(array($this,'toupper'));
			
			elseif($operation == 'edit' || $operation == 'update' || $operation == 'update_validation'):
				if ($this->check_in_use($crud->getStateInfo()->primary_key)):
					$crud->required_fields('city');
					$crud->callback_before_update(array($this,'toupper'));
			
					$crud->field_type('i_e', 'readonly');
					$crud->field_type('code', 'readonly');
					$crud->field_type('name', 'readonly');
					$crud->field_type('state', 'readonly');
					$crud->field_type('state_io', 'readonly');
			
				else:
					$crud->required_fields('name', 'code', 'city', 'i_e', 'state', 'status', 'state_io');
					$crud->callback_before_update(array($this,'toupper'));
				endif;
			
			
			endif;
            
            
		$output = $crud->render();
		$this->_example_output($output);                
	}

	
	public function toupper($post_array)
	{
	foreach ($post_array as $k=>$v):
	$post_array[$k]=strtoupper($v);
	endforeach;
	return $post_array;
	
	
	}
	
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
	
	
	function _example_output($output = null)
	{
		$this->load->view('templates/header');
		$this->load->view('our_template.php',$output);    
		$this->load->view('templates/footer');
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


}
