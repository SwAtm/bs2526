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
		unset ($_SESSION);
		$this->session->sess_destroy();
		$this->index();
	}
	
}
