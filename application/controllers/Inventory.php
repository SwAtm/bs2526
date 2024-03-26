<?php
class Inventory extends CI_Controller{
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
		$this->load->library('session');
		$this->load->helper('pdf_helper');
	}


		function printstock(){
		$stock=$this->Inventory_model->locationwise_stock();
		for($i=0; $i<count($stock); $i++):
		$stock[$i]['rate']=$stock[$i]['myprice']*($stock[$i]['gstrate']+100)/100;
		endfor;	
		$data['stock']=$stock;
		$this->load->view('inventory/printstock', $data);
				
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
				$stock=$this->session->stock=array();
				$this->load->view('templates/header');
				$this->load->view('inventory/recordstock',$data);
				//$this->load->view('templates/footer');
		//to cancel
		elseif(isset($_POST['cancel'])):
			unset($_SESSION['invent']);
			unset($_SESSION['stock_details']);
			unset($_SESSION['stock']);
			redirect('welcome/home');
		//submitted to add and is invalid:
		elseif(isset($_POST['add']) and (!is_object(json_decode($_POST['item'])) or ''==$_POST['quantity'] or empty($_POST['quantity']) or  0>(json_decode($_POST['item'])->stock+$_POST['quantity']))):
		
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
			//matters not whether any stock is recorded or not. If no stock is recorded, session->stock_detials will be an empty array or it will have data
			$data['details'] = $this->session->stock_details;
			$data['invent'] = $this->session->invent;	
			$this->load->view('templates/header');
			$data['error']="NOT recorded. Invalid data sent OR Negative stock entered is more negative than existing stock";
			$this->load->view('templates/error_template',$data);	
			$this->load->view('inventory/recordstock',$data);
			$this->load->view('templates/footer');	
		
		//submitted to add and is invalid. Total stock cannot be negative.
		//if existing stock + qtytobeadded is going to be <0, we need to abandon
		/*
		elseif(isset($_POST['add']) and is_object(json_decode($_POST['item'])) and ''!=$_POST['quantity'] and !empty($_POST['quantity']) and 0>(json_decode($_POST['item'])->stock+$_POST['quantity'])):
			$data['details'] = $this->session->stock_details;
			$data['invent'] = $this->session->invent;	
			$this->load->view('templates/header');
			$data['error']="Negative stock entered is more than existing stock";
			$this->load->view('templates/error_template',$data);	
			$this->load->view('inventory/recordstock',$data);
			$this->load->view('templates/footer');	
		*/
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
			if (!empty($this->session->stock_details)):
				$stockdetails=$this->session->stock_details;
				foreach ($stockdetails as $std):
					foreach ($selectedinv as $sk=>$sv):
						if($std['inv_id']==$sv['id']):
							$selectedinv[$sk]['stock']+=$std['quantity'];
						endif;
					endforeach;
				
				endforeach;
			endif;
			$rowcount=1;
			$qtyadded1=$qtyadded=$_POST['quantity'];
			foreach ($selectedinv as $inv):
					if($_POST['quantity']>0):
						$diff=$inv['clbal']-$inv['stock'];
						//with positive clbal, negative diff is possible only at last row.
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
							if ($diff==0):
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
							/*if($inv['clbal']<=0):
								$rowcount++;
								continue;
							endif;*/
							if ($stock==0):
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

				//if all stock is factored in, exit the foreach loop
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
			
			//build an array to add to stock file
			$stocktoadd=$this->session->stock;
			$stocktoadd[]=array('location_id'=>$this->session->loc_id, 'item_id'=>$item->item_id, 'title'=>$item->title, 'myprice'=>$item->myprice, 'rate'=>$item->rate, 'stock'=>$qtyadded1);
			$this->session->stock=$stocktoadd;
			
			
			$data['invent']=$this->session->invent;
			$data['details']=$this->session->stock_details;
			$this->load->view('templates/header');
			$this->load->view('inventory/recordstock',$data);
			$this->load->view('templates/footer');	
			
		//sumbitted to complete
		else:	
			$details=$this->session->stock_details;
			foreach ($details as $det):
			$this->Inventory_model->update_stock($det['inv_id'], $det['quantity']);
			endforeach;
			$this->Inventory_model->batch_insert($this->session->stock);
		unset($_SESSION['inventory']);
		unset($_SESSION['stock_details']);
		unset($_SESSION['stock']);
		redirect('welcome/home');
		endif;
		
		}
	
		public function viewinventory(){
		$inventory=$this->Inventory_model->get_list_per_loc();
		foreach ($inventory as $k=>$v):
			$inventory[$k]['rate']=$v['myprice']*(100+$v['gstrate'])/100;
		endforeach;
		$data['invent']=$inventory;
		$this->load->view('templates/header');
		$this->load->view('inventory/viewinventory',$data);
		$this->load->view('templates/footer');		
		}
		
		public function printinventory(){
		$inventory=$this->Inventory_model->get_list_per_invid_per_loc();
		foreach ($inventory as $k=>$v):
			$inventory[$k]['rate']=$v['myprice']*(100+$v['gstrate'])/100;
		endforeach;
		$data['stockmyprice']=$this->Inventory_model->stockmyprice();
		$data['invmyprice']=$this->Inventory_model->invmyprice();
		$data['invent']=$inventory;
		$this->load->view('templates/header');
		//print_r($data);
		$this->load->view('inventory/printinventory',$data);
		$this->load->view('templates/footer');		
		}
		
		
}
