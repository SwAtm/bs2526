<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	
	public function __construct()
	{
		parent::__construct();

		$this->load->database();
		$this->load->helper('url');
		$this->load->model('Location_model');
		$this->load->model('Company_model');
		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->load->library('session');
		$this->load->model('Inventory_model');
		$this->load->model('Trns_details_model');
		$this->load->model('Trnf_details_model');
		}
	
	
	
	public function index()
	{
			$loc = $this->Location_model->getall();
			$data['location']=$loc;
			$this->load->view('welcome/index',$data);
		
	}
	
	public function verify(){
	$this->form_validation->set_rules('user','User Name','required');
	
		if ($this->form_validation->run()==false):
		$this->index();
		else:
		//submitted and ok
		//Avoid different users logging from multiple tabs
		
			if (isset($this->session->loc_id) and !empty($this->session->loc_id)):
				Die("Sorry, already logged in");
			else:
		$id=$_POST['user'];
		$user=$this->Location_model->getdetails($id);
		$company=$this->Company_model->getall();
		$this->session->loc_id=$user['id'];
		$this->session->loc_name=$user['name'];
		$this->session->loc_auto_bill_no=$user['auto_bill_no'];
		$this->session->cname=$company['name'];
		$this->session->caddress=$company['address'];
		$this->session->ccity=$company['city'];
		$this->session->cemail=$company['email'];
		$this->session->cgst=$company['gst'];
		$this->session->csdate=$company['sdate'];
		$this->session->cedate=$company['edate'];
		$this->session->cphone=$company['phone'];
		$this->home();
			endif;
		endif;
	}

	
	public function home(){

		if (isset($this->session->loc_id)||!empty($this->session->loc_id)):
		$this->load->view('templates/header');
		$this->load->view('welcome/home');
		else:
		$this->index();
		endif;	
	}


	public function logout(){
		/*
		$invcl=$this->Inventory_model->get_list_per_loc();
		$mess=array();
		foreach ($invcl as $lst):
		$id=$lst['item_id'];
		$myprice=$lst['myprice'];
		$opstock=$this->Inventory_model->get_opbal($id, $myprice)['opbal'];
		$trans=$this->Trns_details_model->get_trans($id, $myprice);
		$trnf_out = $this->Trnf_details_model->get_trnf_out($id, $myprice);
		$trnf_in = $this->Trnf_details_model->get_trnf_in($id, $myprice);
		$bal=$opstock;
			
			foreach ($trans as $row):
				if ($row['tran_type_name']=='Purchase Return'||$row['tran_type_name']=='Sales'):
				$bal-=$row['quantity'];
				else:
				$bal+=$row['quantity'];
				endif;
			endforeach;
			
			
			
			foreach ($trnf_out as $row):
				$bal-=$row['quantity'];	
			endforeach;
			
			
			
			foreach ($trnf_in as $row):
				$bal+=$row['quantity'];	
			endforeach;
			
			
			if ($lst['clbal']!=$bal):
				$mess[]=array('item_id'=>$lst['item_id'], 'myprice'=>$lst['myprice'], 'invcl'=>$lst['clbal'], 'detcl'=>$bal);
			endif;	
		endforeach;
		if (!empty($mess)):
			print_r($mess);
			
		unset ($_SESSION);
		$this->session->sess_destroy();
		$this->load->view('templates/footer');
		echo count($mess);
		else:
		*/
		$clbal=$this->Inventory_model->getbal_per_loc()['clbal'];
		$opbal=$this->Inventory_model->getbal_per_loc()['opbal'];
		$trnsout=$this->Trns_details_model->getinout_per_loc()['outqty'];	
		$trnsin=$this->Trns_details_model->getinout_per_loc()['inqty'];	
		$trnfout=$this->Trnf_details_model->getouttrnf_per_loc()['quantity'];
		$trnfin=$this->Trnf_details_model->getintrnf_per_loc()['quantity'];
		$trclbal=$opbal+$trnsin+$trnfin-$trnsout-$trnfout;
		if (($clbal-$trclbal)<>0):
		echo "There seems an issue. Pl inform admin<Br>";
		echo "Inv Clbal: ".$clbal;
		echo "<br>";
		echo "Tr ClBal: ".$trclbal;
		echo "<br>";
		echo "Diff: ".($clbal-$trclbal);
		unset ($_SESSION);
		$this->session->sess_destroy();
		$this->load->view('templates/footer');
		else:
		unset ($_SESSION);
		$this->session->sess_destroy();
		//$this->load->view('templates/footer');
		$this->index();
		endif;
	}
	
}
