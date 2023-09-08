<?php
defined('BASEPATH') OR exit('No direct script access allowed');

//Fixing DB errors caused while implementing FIFO towards end of May23. In that commit, selec_inv was selecting from all locations instead of from loggedin location. This controller and model fix those DB errors. 
//Original error in select_inv is also fixed. 

class Invent_update extends CI_Controller {

	
	public function __construct()
	{
		parent::__construct();

		$this->load->database();
		$this->load->helper('url');
		//$this->load->library('session');
		$this->load->model('Invent_update_model');
		}
		
		
		
		public function inv_upd(){
		
		$transactions = $this->Invent_update_model->gettrans();
		$transfers=$this->Invent_update_model->gettrnf();
		$this->inv_upd1($transactions, 'trns_details');
		$this->inv_upd1($transfers, 'trnf_details');
		echo "All Done";
		$this->load->view('templates/footer');
		}
		
		//print_r($transactions);
		//print_r($transfers);
		public function inv_upd1($trnsortrnf=null, $tablename=''){
		
		foreach ($trnsortrnf as $tr):
			$item_id=$tr['item_id'];
			$myprice=$tr['myprice'];
			$qty=$tr['quantity'];
			$selectedinv=$this->Invent_update_model->select_inv($item_id, $myprice, $tr['trnlocid']);
			//update wrong inventory
			$this->Invent_update_model->update_wrong_inventory($tr['inventory_id'], $tr['quantity']);
			//select right inventory
			$rowcount=1;
			foreach ($selectedinv as $inv):
				//at last row
				if($rowcount==count($selectedinv)):
					$rightinventory=$inv['id'];
				//not at last row but existing clbal is <=0
				elseif($inv['clbal']<=0):
						$rowcount++;
						continue;
				//not at last row and exising clbal is >0
				else:
					$rightinventory=$inv['id'];
				endif;
			endforeach;
			//update right inventory
			$this->Invent_update_model->update_right_inventory($rightinventory, $tr['quantity']);
			//update trns or trnf table
			$this->Invent_update_model->update_tns_trn($tablename, $tr['id'], $rightinventory);
		endforeach;
	}
		
		
		

}

