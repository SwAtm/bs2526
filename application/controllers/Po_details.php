<?php
class Po_details extends CI_Controller{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->helper('url');
		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->load->library('table');
		$this->output->enable_profiler(TRUE);
		$this->load->library('grocery_CRUD');
		$this->load->library('session');
		$this->load->model('po_summary_model');
		$this->load->helper('pdf_helper');
	}

	public function add($id=null)
	{
		//if po already exists
		if ($details=$this->po_summary_model->get_details($this->uri->segment('3'))):
			redirect('po_details/edit/'.$this->uri->segment('3'));
			//$this->edit($this->uri->segment('3'));
		//new po
		elseif (!$_POST or empty($_POST)):
			$data['id'] = $this->uri->segment('3');
			$party = $this->po_summary_model->get_party($id);
			$data['items'] = $this->po_summary_model->get_items($party['id']);
			$data['party']=$party;
			$this->load->view('templates/header');
			$this->load->view('po_details/add.php',$data);    
			$this->load->view('templates/footer');
		
		//new po submitted to complete
		else:
			//print_r($_POST);
			$posid=$_POST['id'];
			if (empty($_POST['podet']) or !isset($_POST['podet'])):
			die ("Nothing to add.<a href = ".site_url('welcome/home').">Go Home</a>");
			endif;
			foreach ($_POST['podet'] as $item):
				if (!isset($item['quantity']) or 0==$item['quantity']):
					continue;
				else:
				$podetails[]=array('po_summary_id'=>$posid,'item_id'=>$item['item_id'],'rate'=>$item['rate'], 'quantity'=>$item['quantity']);
				//print_r($item);
				endif;
				
			endforeach;
			//print_r($podetails);
			if (!isset($podetails) or empty($podetails)):
			echo "Nothing to add. <a href =".site_url('po_summary/summary').">Go to PO List</a>";
			elseif ($this->po_summary_model->add($podetails)):
			echo "Data added. <a href =".site_url('po_summary/summary').">Go to PO List</a>";
			else:
			echo "Failed to add data. <a href =".site_url('po_summary/summary').">Go to PO List</a>";
			endif;
			$this->load->view('templates/footer');
		endif;
		
	}
	
	public function edit($id=null){
	
	if (!$_POST or empty($_POST)):
		//print_r($id);
		$party = $this->po_summary_model->get_party($id);
		$data['id']=$id;
		$data['items'] = $this->po_summary_model->get_items($party['id']);
		$data['addeditems']=$this->po_summary_model->get_items_edit($id);
		$data['party']=$party;
		$this->load->view('templates/header');
		$this->load->view('po_details/edit',$data);
		$this->load->view('templates/footer');
	else:
		$posid=$_POST['id'];
		foreach ($_POST['podet'] as $item):
			if (!isset($item['quantity']) or 0==$item['quantity']):
				continue;
			else:
				$podetails[]=array('po_summary_id'=>$posid,'item_id'=>$item['item_id'],'rate'=>$item['rate'], 'quantity'=>$item['quantity']);
				//print_r($item);
			endif;
				
		endforeach;
		//print_r($podetails);
		if ($this->po_summary_model->delete($posid) and $this->po_summary_model->add($podetails)):
			echo "Data edited. <a href =".site_url('po_summary/summary').">Go to PO List</a>";
		else:
			echo "Failed to edit data. <a href =".site_url('po_summary/summary').">Go to PO List</a>";
		endif;
		$this->load->view('templates/footer');	
	endif;
	}
	
	public function print1($id){
	
	$posummary = $this->po_summary_model->get_party($id);
	$data['pos'] = array ('id'=>$id, 'date'=>$posummary['date'], 'name'=>$posummary['name'],'city'=>$posummary['city']);
	$data['podetails'] = $this->po_summary_model->get_details_print($id);
	$this->load->view('templates/header');
	$this->load->view('po_details/printpo',$data);
	$this->load->view('templates/footer');
	
	}
}
?>
