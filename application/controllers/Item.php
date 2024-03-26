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
		$this->load->helper('pdf_helper');
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
					$crud->callback_before_update(array($this,'toupper'));
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
	$post_array['code']=strtoupper(preg_replace('/[^a-zA-Z0-9_ -]/s', '', $post_array['code']));
	$post_array['title']=strtoupper(preg_replace('/[^a-zA-Z0-9_ -]/s', '', $post_array['title']));
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
		function printstock(){
		$stock=$this->Inventory_model->locationwise_stock();
		for($i=0; $i<count($stock); $i++):
		$stock[$i]['rate']=$stock[$i]['myprice']*($stock[$i]['gstrate']+100)/100;
		endfor;	
		$data['stock']=$stock;
		$this->load->view('item/printstock', $data);
				
		}
		
		function recordstock(){
		//first run
		if (!isset($_POST)||empty($_POST)):			
			//if ($this->form_validation->run()==false):
				if (!$inventory = $this->Inventory_model->get_list_per_loc()):
					//nothing in the inventory	
					echo $this->load->view('templates/header','',true);
					die("Sorry, Inventory is empty<br> <a href = ".site_url('welcome/home').">Go Home</a href>&nbsp&nbsp&nbsp<a href = ".site_url('welcome/home').">Or Go to List</a href>");
								
				endif;
				foreach ($inventory as $k=>$v):
					//$inventory[$k]['rate']=number_format($v['myprice']*(100+$v['gstrate'])/100,2,'.',',') ;
					$inventory[$k]['rate']=$v['myprice']*(100+$v['gstrate'])/100;
				endforeach;
				$this->session->invent = $inventory;
				$data['invent']=$inventory;
				$data['details']=$this->session->stock_details=array();
				$this->load->view('templates/header');
				$this->load->view('item/recordstock',$data);
				//$this->load->view('templates/footer');
		//to cancel
		elseif(isset($_POST['cancel'])):
			unset($_SESSION['invent']);
			unset($_SESSION['stock_details']);
			redirect('welcome/home');
		//submitted to add and is invalid:
		elseif(isset($_POST['add']) and (!is_object(json_decode($_POST['item'])) or ''==$_POST['quantity'] or empty($_POST['quantity']))):
		
			/*
			//if no stock is recorded till now:
			if(!$this->session->stock_details||empty($this->session->stock_details)):
				unset ($_POST);
				unset($_SESSION['invent']);
				redirect(site_url('Item/recordstock'));
			//since some stock is recorded, we need to use inventory from session
			else:
				$data['details'] = $this->session->stock_details;
				$data['invent'] = $this->session->invent;	
				$this->load->view('templates/header');
				$this->load->view('Item/recordstock',$data);
				$this->load->view('templates/footer');	
			endif;	
			*/
			//matters not whether any stock is recorded or not. If not stock is recorded, session->stock_detials will be an empty array or it will have data
			$data['details'] = $this->session->stock_details;
			$data['invent'] = $this->session->invent;	
			$this->load->view('templates/header');
			$this->load->view('Item/recordstock',$data);
			$this->load->view('templates/footer');	
		
		//submitted to add and is valid
		elseif(isset($_POST['add']) and is_object(json_decode($_POST['item'])) and ''!=$_POST['quantity'] and !empty($_POST['quantity'])):
		
		
			$item = json_decode($_POST['item']);
			//choose appropriate inventory_id
			//if negative quantity is added, array reverse is not necessary
			if($_POST['quantity']>0):
			$selectedinv=array_reverse($this->Inventory_model->select_inv($item->item_id, $item->myprice));
			else:
			$selectedinv=($this->Inventory_model->select_inv($item->item_id, $item->myprice));
			endif;
				$rowcount=1;
				$qtyadded1=$qtyadded=$_POST['quantity'];
				foreach ($selectedinv as $inv):
					
					if($_POST['quantity']>0):
						$diff=$inv['clbal']-$inv['stock'];
						//negative diff is possible only at last row.
						//at last row OR //not at last row but diff is > qtyadded
						if($rowcount==count($selectedinv) OR ($qtyadded<=$diff)):
							$details=array('inv_id'=>$inv['id'], 'quantity'=>$qtyadded);
							$qtyadded=0;
						
						//not at last row and exising diff < quantitytoadd. Need to add to this row and move on to next.
						else:
							if($inv['clbal']<=0):
								$rowcount++;
								continue;
							endif;
							$details=array('inv_id'=>$inv['id'], 'quantity'=>$diff);
							$qtyadded-=$diff;
							$rowcount++;
						endif;
					else:
						$stock=$inv['stock'];
						//at last row OR //not at last row but existing stock+quantityadded>=0
						if($rowcount==count($selectedinv) OR ($stock+$qtyadded>=0)):
							$details=array('inv_id'=>$inv['id'], 'quantity'=>$qtyadded);
							$qtyadded=0;
						
						//not at last row and exising stock+quantityadded<0. Need to add to this row and move on to next.
						else:
							if($inv['clbal']<=0):
								$rowcount++;
								continue;
							endif;
							$details=array('inv_id'=>$inv['id'], 'quantity'=>0-$stock);
							$qtyadded+=$stock;
							$rowcount++;
						endif;
					endif;
				//build an array to add
				$det[]=$details;		

				//if all sales is factored in, exit the foreach loop
					if (0==$qtyadded):
					break;
					endif;			
				endforeach;				
				//add to session
					// first transaction - session is empty
					if (!isset($this->session->stock_details)||empty($this->session->stock_details)):
					$this->session->stock_details=$det;
					else:
					//add to session
					$det1=$this->session->stock_details;
					foreach ($det as $d):
					$det1[]=$d;
					endforeach;
					$this->session->stock_details=$det1;
					endif;
			//need to add the last entry to stock in inventory
			$inventory = $this->session->invent;
			foreach ($inventory as $key => $value):
				if ($value['item_id'] == $item->item_id and $value['myprice'] == $item->myprice):
				$inventory[$key]['stock']+=$qtyadded1;
				//print_r($inventory[$key]['clbal']);
				endif;
			endforeach;
			
			//put chgd inventory in session
			$this->session->invent = $inventory;
			
			$data['invent']=$this->session->invent;
			$data['details']=$this->session->stock_details;
			$this->load->view('templates/header');
			$this->load->view('item/recordstock',$data);
			$this->load->view('templates/footer');	
			
		//sumbitted to complete
		else:	
			$details=$this->session->stock_details;
			foreach ($details as $det):
			$this->Inventory_model->update_stock($det['inv_id'], $det['quantity']);
			endforeach;
		unset($_SESSION['inventory']);
		unset($_SESSION['stock_details']);
		redirect('welcome/home');
		endif;
		
		}
	
		
}
