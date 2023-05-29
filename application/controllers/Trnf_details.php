<?php

class Trnf_details extends CI_Controller{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->helper('url');
		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->load->library('table');
		$this->load->library('grocery_CRUD');
		$this->output->enable_profiler(TRUE);
		$this->load->library('user_agent');
		$this->load->library('session');
		$this->load->model('Party_model');
		$this->load->model('Trnf_details_model');
		$this->load->model('Inventory_model');
		$this->load->model('Location_model');		
		$this->load->model('Trnf_summary_model');		
		$this->load->model('Item_model');		

}
		public function send(){

			//unsubmitted
			if (!isset($_POST)||empty($_POST)):			
			
				if (!$inventory = $this->Inventory_model->get_list_per_loc()):
					//nothing in the inventory	
					echo $this->load->view('templates/header','',true);
					die("Sorry, Inventory is empty<br> <a href = ".site_url('welcome/home').">Go Home</a href><a href = ".site_url('trnf_summary/summary').">Or Go to List</a href>");
						
				endif;
				foreach ($inventory as $k=>$v):
				//$inventory[$k]['rate']=number_format($v['myprice']*(100+$v['gstrate'])/100,2,'.',',') ;
				$inventory[$k]['rate']=number_format($v['myprice']*(100+$v['gstrate'])/100,'2','.',',');
				endforeach;
				foreach ($inventory as $k=>$v):
					if ($v['clbal']<=0):
					unset ($inventory[$k]);
					endif;
				endforeach;
				$data['invent'] = $inventory;
				$this->session->invent = $inventory;
				$this->load->view('templates/header');
				$this->load->view('trnf_details/add_details',$data);
				$this->load->view('templates/footer');	
			
			//submitted to add but invalid
			elseif(isset($_POST['add']) and (!is_object(json_decode($_POST['item'])) or ''==$_POST['quantity'] or empty($_POST['quantity']))):
					//nothing is submitted to add untill now
					if(!isset($this->session->send_details) or empty($this->session->send_details)):
					unset ($_POST);
					redirect(site_url('Trnf_details/send'));
					else:
					$data['invent']=$this->session->invent;
					$this->load->view('templates/header');
					$this->load->view('trnf_details/add_details',$data);
					$this->load->view('templates/footer');	
					endif;
				
			//submitted to add and is valid
			elseif(isset($_POST['add']) and is_object(json_decode($_POST['item'])) and ''!=$_POST['quantity'] and !empty($_POST['quantity'])):
				$item = json_decode($_POST['item']);
				
				//choose appropriate inventory_id and corrosponding hsn
				$selectedinv=$this->Inventory_model->select_inv($item->item_id, $item->myprice);
				$rowcount=1;
				$qtysold1=$qtysold=$_POST['quantity'];
				foreach ($selectedinv as $inv):
					//at last row
					if($rowcount==count($selectedinv)):
						$item->id=$inv['id'];
						$_POST['quantity']=$qtysold;
					//not at last row but existing clbal is > sold quantity
					elseif($qtysold<=$inv['clbal']):	
						$item->id=$inv['id'];
						$_POST['quantity']=$qtysold;
						$qtysold=0;
					//not at last row and exising clbal is < sold quantity. Need to add this row and move on to next.
					else:
						if($inv['clbal']<=0):
							$rowcount++;
							continue;
						endif;
						$item->id=$inv['id'];
						$_POST['quantity']=$inv['clbal'];
						$qtysold-=$inv['clbal'];
						$rowcount++;
					endif;
				
				
				//currently submitted data
				$details = array('inventory_id' => $item->id, 'quantity' => $_POST['quantity'],'item_id' => $item->item_id, 'myprice' => $item->myprice, 'rate' => $item->rate);
				//build array to add
				$det[] = $details;
				//if all quantity is factored in, exit the foreach loop
				if (0==$qtysold):
				break;
				endif;
			endforeach;	
			
				//Add to session. firts transaction - session is empty
				if (!isset($this->session->send_details)||empty($this->session->send_details)):
					$this->session->send_details=$det;
				else:
				//pull frm session
					$det1 = $this->session->send_details;
					foreach ($det as $d):
					$det1[]=$d;
					endforeach;
					$this->session->send_details=$det1;
				endif;
				
								
				//need to reduce the last trnf from clbal in inventory
				$inventory = $this->session->invent;
				foreach ($inventory as $key => $value):
					if ($value['item_id'] == $item->item_id and $value['myprice']==$item->myprice):
					$inventory[$key]['clbal']-=$qtysold1;
					endif;
				endforeach;
				foreach ($inventory as $key => $value):
					if ($value['clbal']<=0):
					unset ($inventory[$key]);
					endif;
				endforeach;
				//put chgd inventory in session
				$this->session->invent = $inventory;
				//$this->session->send_details = $det;
			
				$data['invent'] = $this->session->invent;
				$this->load->view('templates/header');
				$this->load->view('trnf_details/add_details',$data);
				$this->load->view('templates/footer');	
			else:
			//submitted to complete, no currently submitted data
			//if a joker submits empty bill:
			if (!isset($this->session->send_details)||empty($this->session->send_details)):
				echo $this->load->view('templates/header','',true);
				
				unset($_SESSION['invent']);
				if (isset($this->session->send_details)):
					unset($_SESSION['send_details']);
				endif;
				die("Sorry, You cannt create an empty transfer<br> <a href = ".site_url('welcome/home').">Go Home</a href>");
			endif;
			//unset($_POST);
			$_POST = array();
			$this->send_complete();

			endif;	
		}

		public function send_complete(){

			//unsubmitted
			if (!isset($_POST)||empty($_POST)):			
				$loc = array();
				$locations = $this->Location_model->get_list_except_loggedin($this->session->loc_id);
				foreach ($locations as $k=>$v):
					//$loc[]=array($v['id'] => $v['name']);
					$loc[$v['id']] = $v['name'];

				endforeach;
				$data['loc'] = $loc;
				$this->load->view('templates/header');
				$this->load->view('trnf_details/send_complete',$data);
				$this->load->view('templates/footer');
			//submitted
			else:
				//trnf summary
				$ts['date'] = date('Y-m-d');
				$ts['from_id'] = $this->session->loc_id;
				$ts['to_id'] = $_POST['to_id'];
				//trnf details
				$td = $this->session->send_details;
				//start transaction
				$this->db->trans_start();
				//add to trnf summary
				$this->Trnf_summary_model->add($ts);
				//get the max id
				$trnf_summary_id = $this->Trnf_summary_model->get_max_id()['id'];
				//add trnf summary id to each detail row and add to trnf details
				foreach ($td as $k):
					$k['trnf_summ_id'] = $trnf_summary_id;
					//$tds[] = $k;
				//endforeach;
				//foreach ($tds as $k):
					$this->Trnf_details_model->add($k);
				endforeach;
				//update inventory
				foreach($td as $key):
					//sending loc
					$this->Inventory_model->update_transfer_send($key);
					//receiving loc
					//pull inventory row based on $key['inventory_id']
					$inventr = $this->Inventory_model->get_details($key['inventory_id']);
					//unset id, replace loc_id, in_qty, out_qty, opbal, clbal
					unset ($inventr['id']);
					$inventr['location_id']=$ts['to_id'];
					$inventr['in_qty'] = $key['quantity'];
					$inventr['out_qty'] = 0;
					$inventr['opbal'] = 0;
					$inventr['clbal'] = $key['quantity'];
					//add to inventory table
					$this->Inventory_model->add($inventr);
				endforeach;
				unset($_SESSION['invent']);
				unset($_SESSION['send_details']);
				$this->db->trans_complete();
				$this->load->view('templates/header');				
				$this->load->view('templates/footer');
			endif;
		}
}
?>
