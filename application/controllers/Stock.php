<?php
class Stock extends CI_Controller{
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
		//$this->load->model('Trns_details_model');
		//$this->load->model('Grocery_crud_model');
		//$this->load->model('Trnf_details_model');
		$this->load->model('Item_model');
		$this->load->model('Stock_model');
		$this->load->helper('pdf_helper');
		$this->load->library('session');
	}

		public function add(){
		//first run
		if (!isset($_POST)||empty($_POST)):			
			//if ($this->form_validation->run()==false):
				if (!$inventory = $this->Inventory_model->get_list_per_loc()):
					//nothing in the inventory	
					echo $this->load->view('templates/header','',true);
					die("Sorry, Inventory is empty<br> <a href = ".site_url('welcome/home').">Go Home</a href>&nbsp&nbsp&nbsp<a href = ".site_url('trns_summary/summary').">Or Go to List</a href>");
								
				endif;
				foreach ($inventory as $k=>$v):
					//$inventory[$k]['rate']=number_format($v['myprice']*(100+$v['gstrate'])/100,2,'.',',') ;
					$inventory[$k]['rate']=$v['myprice']*(100+$v['gstrate'])/100;
				endforeach;
				$this->session->inventory = $inventory;
				$data['invent']=$inventory;
				$this->load->view('templates/header');
				$this->load->view('stock/addstock',$data);
				//$this->load->view('templates/footer');
		//to add
		elseif(isset($_POST['add'])):
			$item = json_decode($_POST['item']);
			$inventory_id = $item->id;
			$quantity = $_POST['quantity'];
			
			$data['location_id'] = $this->session->loc_id;
			$data['inventory_id'] = $inventory_id;
			$data['stock'] = $_POST['quantity'];
			$data['date'] = date('Y-m-d');
			$data['remark'] = $_POST['remark'];
			$this->db->trans_start();
			$this->Inventory_model->update_stock($inventory_id, $quantity);
			$this->Stock_model->add($data);
			$inventory=$this->session->inventory;
			foreach ($inventory as $key=>$value):
				if ($inventory[$key]['id'] == $inventory_id):
					$inventory[$key]['stock']+=$quantity;
				endif;
			endforeach;
			$this->session->inventory = $inventory;
			$data['invent']=$inventory;
			$this->db->trans_complete();
			$this->load->view('templates/header');
			$this->load->view('stock/addstock',$data);
				
			
		//submitted to complete
		else:
		unset($_SESSION['inventory']);
		$this->load->view('templates/footer');
		endif;
		}
		
		public function list(){
		$stock = $this->Stock_model->liststock();
		foreach ($stock as $k=>$v){
		$stock[$k]['rate'] =$v['myprice']*(100+$v['gstrate'])/100;
		}
		//print_r($stock);
		$data['stock']=$stock;
		$this->load->view('templates/header');
		$this->load->view('stock/list',$data);
		$this->load->view('templates/footer');
		
		}
		
		public function printinventory()
		{
		$inventory = $this->Stock_model->printinventory();
		foreach ($inventory as $k=>$v){
		$inventory[$k]['rate'] =$v['myprice']*(100+$v['gstrate'])/100;
	}
		//print_r($inventory);
		$data['inventory']=$inventory;
		$this->load->view('templates/header');
		$this->load->view('stock/printinventory',$data);
		$this->load->view('templates/footer');
		
	}
		public function viewinventory()
		{
		$inventory = $this->Stock_model->printinventory();
		foreach ($inventory as $k=>$v){
		$inventory[$k]['rate'] =$v['myprice']*(100+$v['gstrate'])/100;
	}
		//print_r($inventory);
		$data['inventory']=$inventory;
		$this->load->view('templates/header');
		$this->load->view('stock/viewinventory',$data);
		$this->load->view('templates/footer');
		
	}
		
}
?>
