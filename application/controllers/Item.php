<?php
class Item extends CI_Controller{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->helper('url');
		//$this->load->helper('form');
		//$this->load->library('form_validation');
		$this->load->library('table');
		//$this->load->helper('security');
		$this->load->library('grocery_CRUD');
		$this->output->enable_profiler(TRUE);
		$this->load->model('Inventory_model');
		$this->load->model('Trns_details_model');
		//$this->load->model('Grocery_crud_model');
		$this->load->model('Trnf_details_model');
		$this->load->model('Item_model');
		$this->load->library('session');
	}

	public function item()
	{
		$crud = new grocery_CRUD();
		$crud->set_table('item')
		     ->set_subject('Item')
			 ->columns('cat_id', 'code', 'title','party_id','gcat_id','gstrate','rcm')
			 ->display_as('cat_id','Category')
			 ->display_as('code','Item Code')
			 ->display_as('title','Title')
			 ->display_as('party_id','Party')
			 ->display_as('gcat_id','GST Category')
			 ->display_as('gstrate','GST Rate')
			 ->display_as('rcm','Attract RCM?')
			 ->field_type('rcm','dropdown',array('Y'=>'Yes','N'=>'No'))
			 ->set_language('english');
			//set relations:
			$crud->set_relation('cat_id','item_cat','name');
			$crud->set_relation('party_id','party','{name}--{city}');			
			$crud->set_relation('gcat_id','gst_cat','name');
			//set required fields while adding and editing
			$crud->set_rules('gstrate', 'GST Rate', 'callback_checkgst');
			$operation=$crud->getState();
			if( $operation == 'add' || $operation == 'insert' || $operation == 'insert_validation'):
				$crud->required_fields('cat_id','title','party_id','gcat_id','gstrate','rcm');
				$crud->callback_before_insert(array($this,'toupper'));
				//$crud->callback_before_insert(array($this,'checkgst'));
				$crud->set_rules('code', 'Item Code', 'required|callback_unique_code');
			elseif($operation == 'edit' || $operation == 'update' || $operation == 'update_validation'):
				$state_info=$crud->getStateInfo();
				if ($this->check_in_details($state_info->primary_key)):
					$crud->required_fields('title','gcat_id','gstrate','rcm');
					$crud->field_type('cat_id', 'readonly');
					$crud->field_type('code', 'readonly');
					$crud->field_type('party_id', 'readonly');
				else:
					$crud->required_fields('cat_id','title','party_id','gcat_id','gstrate','rcm');
					$crud->callback_before_update(array($this,'toupper'));
					$crud->set_rules('code', 'Item Code', 'required|callback_unique_code');
				endif;
			//$crud->callback_before_update(array($this,'checkgst'));
			endif;
            $crud->add_action('View Details',base_url('application/view_details.png'),'Item/get_stock');
            $crud->set_lang_string('delete_error_message','This data cannot be deleted, it is used');
            $crud->callback_before_delete(array($this,'delete_check'));
			
		
		$output = $crud->render();
		$output->extra="<table width = 100% bgcolor=pink><tr><td align = center><a href = ".site_url('item/get_stock_all').">All stock</a href></td></tr></table>";
		$this->_example_output($output);                
	}

	public function unique_code($code)
	{
	$id=$this->uri->segment(4);
	
	$title=$this->input->post('title');
	$sql=$this->db->select('*');
	$sql=$this->db->from('item');
	$sql=$this->db->group_start();
	$sql=$this->db->where('code',$code);
	$sql=$this->db->or_where('title',$title);
	$sql=$this->db->group_end();
	if (!empty($id) && is_numeric($id)):
	$sql=$this->db->where('id !=',$id);
	endif;
	$res=$this->db->get();
	if ($res and $res->num_rows()>0):
		$this->form_validation->set_message('unique_code','There is already an entry for same Code or name');
		return false;
    else:
		return true;
	endif;
	}


	public function toupper($post_array)
	{
	$post_array['code']=strtoupper($post_array['code']);
	$post_array['title']=strtoupper($post_array['title']);
	return $post_array;
	
	}

	public function checkgst($gstrate){
	$err=0;
	$gcat_id=$this->input->post('gcat_id');
	$gcat_name=$this->db->select('*')->where('id',$gcat_id)->get('gst_cat')->row()->name;
	if ('RATED'== strtoupper($gcat_name)):
		if (0==$gstrate):
		$err=1;
		endif;
	else:
		if (0!=$gstrate):
		$err=1;
		endif;
	endif;
	if ($err):	
	$this->form_validation->set_message('checkgst','Mismatch between GST Category and GST Rate');
		return false;
	else:
		return true;
	endif;
	}
	
	
	
	
	
	
	public function check_in_details($id)
	{
	
	$sql=$this->db->select('*');
	$sql=$this->db->from('trns_details');
	$sql=$this->db->where('item_id',$id);
	$res=$this->db->get();
	if ($res && $res->num_rows()>0):
	return true;
	else:
		$sql=$this->db->select('*');
		$sql=$this->db->from('trnf_details');
		$sql=$this->db->where('item_id',$id);
		$res=$this->db->get();
		if ($res && $res->num_rows()>0):
		return true;
		else:
			$sql=$this->db->select('*');
			$sql=$this->db->from('profo_details');
			$sql=$this->db->where('item_id',$id);
			$res=$this->db->get();
			if ($res && $res->num_rows()>0):
			return true;
			else:
				$sql=$this->db->select('*');
				$sql=$this->db->from('inventory');
				$sql=$this->db->where('item_id',$id);
				$res=$this->db->get();
				if ($res && $res->num_rows()>0):
				return true;
				else:
				return false;
				endif;
			endif;
		endif;
	endif;
	
	}

	public function delete_check($primary_key)
	{
	//return false;
	if ($this->check_in_details($primary_key)):
		return false;
	else:
		return true;
	endif;
	
	}


	function _example_output($output = null)
	{
		$this->load->view('templates/header');
		$this->load->view('templates/trans_template.php',$output);    
		$this->load->view('templates/footer');
	}    
	
	function get_stock($id){
		$stock = $this->Inventory_model->itemwise_locationwise_stock($id);
		for($i=0; $i<count($stock); $i++):
		$stock[$i]['rate']=$stock[$i]['myprice']*($stock[$i]['gstrate']+100)/100;
		endfor;
		$data['stock'] = $stock;
		$this->load->view('templates/header');
		$this->load->view('item/display_stock',$data);    
		$this->load->view('templates/footer');
	}
//rate=myprice*(100+grate)/100; 
	function get_stock_all(){
		
		$stock = $this->Inventory_model->locationwise_stock();
		for($i=0; $i<count($stock); $i++):
		$stock[$i]['rate']=$stock[$i]['myprice']*($stock[$i]['gstrate']+100)/100;
		endfor;
		$data['stock'] = $stock;
		$this->load->view('templates/header');
		$this->load->view('item/display_stock',$data);    
		$this->load->view('templates/footer');
	}



	function det_stck($id, $myprice){
		$id=$this->uri->segment('3');	
		$myprice=$this->uri->segment('4');	
		
		$item=$this->Item_model->get_details_with_partycode($id)['title'];
		$gstrate=$this->Item_model->get_details_with_partycode($id)['gstrate'];
		$opstock=$this->Inventory_model->get_opbal($id, $myprice)['opbal'];
		$trans=$this->Trns_details_model->get_trans($id, $myprice);
		$trnf_out = $this->Trnf_details_model->get_trnf_out($id, $myprice);
		$trnf_in = $this->Trnf_details_model->get_trnf_in($id, $myprice);
		if ($opstock==''):
		$opstock=0;
		endif;
		$show_stck=array();
		$show_stck[]=array('date'=>"0000-00-00",'document'=>"Opening Stock",'qty'=>'', 'balance'=>$opstock);
		foreach ($trans as $row):
			if ($row['tran_type_name']=='Purchase Return'||$row['tran_type_name']=='Sales'):
				$row['quantity']=0-$row['quantity'];
			endif;
		$show_stck[]=array('date'=>$row['date'], 'document'=>$row['payment_mode_name']." ".$row['tran_type_name']." ".$row['series']." ".$row['no'], 'qty'=>$row['quantity'], 'balance'=>0);
		endforeach;
		
		foreach ($trnf_out as $row):
		$row['quantity']=0-$row['quantity'];
		$show_stck[]=array('date'=>$row['date'], 'document'=>"Trnf to ".$row['name']." "."Trnf Id No: ".$row['trnf_summ_id'], 'qty'=>$row['quantity'], 'balance'=>0);
		endforeach;
		
		foreach ($trnf_in as $row):
		$show_stck[]=array('date'=>$row['date'], 'document'=>"Trnf from ".$row['name']." "."Trnf Id No: ".$row['trnf_summ_id'], 'qty'=>$row['quantity'], 'balance'=>0);
		endforeach;
		echo "<pre>";
		echo "</pre>";	
		array_multisort(array_column($show_stck, 'date'), SORT_ASC, $show_stck);
		$stck_bal=$opstock;
		foreach ($show_stck as $row=>$val):
			if ($val['qty']==''):
			$val['qty']=0;
			endif;
			$show_stck[$row]['balance']=$stck_bal+$val['qty'];
			$stck_bal=$show_stck[$row]['balance'];
		endforeach;
		$rate=$myprice*($gstrate+100)/100;
		$data['title']=$item;
		$data['id']=$id;
		$data['rate']=$rate;
		$data['show_stck']=$show_stck;
		$this->load->view('templates/header');
		$this->load->view('item/show_stck',$data);    
		$this->load->view('templates/footer');
		
	}
	

}
