<?php
class Trns_summary extends CI_Controller{
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
		$this->load->model('Series_model');
		$this->load->model('Party_model');
		$this->load->model('Trns_details_model');
		$this->load->model('Trns_summary_model');

}



		public function summary()
		//If there is a series for credit purchase for a given location, Add Purchase will be available.
	{
			$crud = new grocery_CRUD();
			
			
			$crud->set_table('trns_summary')
				->set_subject('Transaction')
				//->set_theme('datatables')
				->columns('id','series_id','no','date', 'party_id', 'expenses', 'amount','remark')
				->display_as('series_id','Series')
				->display_as('no','Trn Number')
				->display_as('date','Date')
				->display_as('party_id','Party')
				->display_as('expenses','Expenses')
				->display_as('remark','Remark')
				->display_as('amount', 'Amount')
				->order_by('id','desc')
				->unset_add()
				->unset_delete()
				->unset_edit()
				->unset_print()
				->set_relation('series_id','series','{payment_mode_name}-{tran_type_name}')
				->set_relation('party_id','party','{name}--{city}')
				->callback_column('amount',array($this,'_callback_amount'))
				->callback_column('expenses',array($this,'_callback_expenses'))
				
				
				->add_action('Edit Summary',base_url('application/pencil.jpeg'),'','',array($this, 'check_editable'))
				->add_action('View Details',base_url('application/view_details.png'),'trns_summary/view_details')
				->add_action('Edi Details',base_url('application/view_details.png'),'trns_details/check_editable')
				->add_action('Print Bill', base_url('application/print.png'), 'reports/print_bill');
				
				/*
				//second parameter can be blank in datatables theme.
				->add_action('Edit Summary','','','',array($this, 'check_editable'))
				->add_action('View Details','','trns_summary/view_details')
				->add_action('Edi Details','','trns_details/check_editable1')
				->add_action('Print Bill', '', 'reports/print_bill');
				*/
				$series = $this->Series_model->get_all_series_by_location();
				
				$s3 = 'series_id = ';
				$i = 0;
				while ($i < count($series)) {
				 	# code...
				 if ($i+1 == count($series)):
					$s3 .= $series[$i]['id'];
				else:
					$s3 .= $series[$i]['id'].' or series_id = ';
				endif;
				 $i++;
			}
				$crud->where($s3);
				
				$output = $crud->render();
	
				if ($this->Series_model->get_series($this->session->loc_name,'credit','purchase')):
				//if ($this->session->loc_name=='Fort Ashrama'):
				$output->extra="<table width = 100% bgcolor=pink><tr><td align = center><a href =".site_url('trns_details/purch_add_details').">Add Purchase </a></td><td align = center><a href = ".site_url('trns_details/sales_add_details').">Add Sales</a href></td></td></tr></table>";
				else:
					$output->extra="<table width = 100% bgcolor=pink><tr><td align = center><a href = ".site_url('trns_details/sales_add_details').">Add Sales</a href></td></tr></table>";
				endif;

				$this->_example_output($output);                

	
	}
	
		public function _callback_amount($id, $row)
		{
		
		$sql=$this->db->select('SUM(quantity*(rate-cash_disc)-((quantity*(rate-cash_disc)))*discount/100) AS amount',false);
		$sql=$this->db->from ('trns_details');
		//$sql=$this->db->join('item', 'item.id=details.item_id');
		$sql=$this->db->where('trns_summary_id',$row->id);
		//$sql=$this->db->group_by('details.summary_id');
		$res=$this->db->get();
		$amount=$res->row()->amount;
		$amount+=(int)$row->expenses;
		//return $amount;
		return number_format($amount,2,'.','');
		}
		
		public function _callback_expenses($exp, $row){
			return number_format($exp, 2);
		}


		function check_editable($pk, $row){
		//check whether a transaction is editable
		$editable=1;
		if ($row->remark=='Cancelled'):
		$editable=0;
		endif;
		$payment_mode_name=$this->Series_model->get_payment_mode_name($row->series)->payment_mode_name;
		$dt=date_create_from_format('d/m/Y', $row->date);
		$date = date_format($dt,'Y-m-d');
		if ((ucfirst($payment_mode_name)=="Cash" and $date!=date("Y-m-d")) OR (ucfirst($payment_mode_name)!=="Cash" and Date("m",strtotime($date))!==Date("m"))):
		$editable=0;
		endif;
		
		if ($editable):
		return site_url('trns_summary/summary_edit/edit/'.$pk);
		else:
		return site_url('trns_summary/not_editable');
		endif;
		
		}

		function _example_output($output = null)
	{
		$this->load->view('templates/header');
		$this->load->view('templates/trans_template.php',$output);    
		$this->load->view('templates/footer');
	}   

		public function not_editable(){
			$this->load->view('templates/header');
			$this->load->view('trns_summary/not_editable');

		}	

		public function view_details($pk){
			$data['trns_details'] = $this->Trns_details_model->get_details($pk);
			$data['expenses'] = $this->Trns_summary_model->get_details_by_id($pk)['expenses'];
			$this->load->view('templates/header');
			$this->load->view('trns_details/view_details',$data);
			$this->load->view('templates/footer');

		}


		public function summary_edit($pk)
	{
		//for editing. In summary() edit is unset. As such summary/edit is not allowed.
			//unsubmitted
			if (!isset($_POST) || empty($_POST)):	
				$pk = $this->uri->segment(4);
				
				$series_id = $this->Trns_summary_model->get_details_by_id($pk)['series_id'];
				$series_details = $this->Series_model->get_series_details($series_id);
				$tran_type = $series_details['tran_type_name'];
				
				$tran_details = $this->Trns_summary_model->get_details_by_id($pk);
				$tran_type = $tran_details['tran_type_name'];
				$party_status = $tran_details['party_status'];
								
				if($tran_type == 'Sales' || $tran_type == 'Sale Return'):
				//sale/sale return from a regd party - party cannot be changed
				  	if(strtoupper($party_status) == 'REGD'):			
						$data['partychange'] = 'No';
					else:
				//sale/sale return from an unrd party - party can be changed only to another unrd
						$data['partychange'] = 'Yes';
						$data['party'] = $this->Party_model->getall_unregd();
					endif;
				else:
				//other transactions
						$data['partychange'] = 'Yes';
						$data['party'] = $this->Party_model->getall();
				endif;
				
				foreach ($tran_details as $k => $v):
					$data[$k] = $v;
				endforeach;	
				$p_id = $tran_details['party_id'];
				$p_details = $this->Party_model->get_details($p_id);
				$data['party_name'] = $p_details['name'].' - '.$p_details['city'];
				$data['pk'] = $pk;
				$this->load->view('templates/header');
				$this->load->view('trns_summary/summary_edit',$data);
				$this->load->view('templates/footer');
			//submitted	
			else:	
			//print_r($_POST);
			$party_id = $_POST['party'];
			$party_details = $this->Party_model->get_details($party_id);
			$series_id = $_POST['series_id'];
			//$data['series'] = $this->Series_model->get_series_details($series_id)['series'];
			$id = $_POST['id'];
			$data['series_id'] = $series_id;
			$data['series'] = $_POST['series'];
			$data['no'] = $_POST['no'];
			$data['date'] = $_POST['date'];
			$data['party_id'] = $party_id;
			$data['party_status'] = $party_details['status'];
			$data['expenses'] = $_POST['expenses'];
			$data['party_gstno'] = $party_details['gstno'];
			$data['party_state'] = $party_details['state'];
			$data['party_state_io'] = $party_details['state_io'];
			$data['remark'] = $_POST['remark'];
			
			//print_r($data);
				if ($this->Trns_summary_model->update($data,$id)):
					$mess = "Data Updated";
				else:
					$mess = "Error, Could not update";
				endif;	
			$this->load->view('templates/header');
			$this->output->append_output("$mess<a href =".site_url('trns_summary/summary'."> GO to List</a>"));
			$this->load->view('templates/footer');
			endif;		

}

			public function trns_search(){
			//unsubmitted
			if (!isset($_POST) || empty($_POST)):	
				$data['party']=$this->Party_model->getall();
				$this->load->view('templates/header');
				$this->load->view('trns_summary/trns_search',$data);
				$this->load->view('templates/footer');
			//submitted blank
			elseif ((!isset($_POST['trno']) or empty($_POST['trno'])) and (!isset($_POST['party']) or empty($_POST['party']))):
				$this->load->view('templates/header');
				$this->output->append_output("Nothing selected.<a href =".site_url('welcome/home'."> GO to Home</a>"));
			//submitted with party
			elseif (isset($_POST['party']) and !empty($_POST['party'])):
				$partyid=$_POST['party'];
				$crud = new grocery_CRUD();
				$crud->set_table('trns_summary')
				->set_subject('Transaction')
				//->set_theme('datatables')
				->columns('id','series_id','no','date', 'party_id', 'expenses', 'amount','remark')
				->display_as('series_id','Series')
				->display_as('no','Trn Number')
				->display_as('date','Date')
				->display_as('party_id','Party')
				->display_as('expenses','Expenses')
				->display_as('remark','Remark')
				->display_as('amount', 'Amount')
				->order_by('id','desc')
				->unset_add()
				->unset_delete()
				->unset_edit()
				->unset_print()
				->set_relation('series_id','series','{payment_mode_name}-{tran_type_name}')
				->set_relation('party_id','party','{name}--{city}')
				->callback_column('amount',array($this,'_callback_amount'))
				->callback_column('expenses',array($this,'_callback_expenses'))
				->add_action('Edit Summary',base_url('application/pencil.jpeg'),'','',array($this, 'check_editable'))
				->add_action('View Details',base_url('application/view_details.png'),'trns_summary/view_details')
				->add_action('Edi Details',base_url('application/view_details.png'),'trns_details/check_editable')
				->add_action('Print Bill', base_url('application/print.png'), 'reports/print_bill');
				$series = $this->Series_model->get_all_series_by_location();
				$s3 = 'series_id = ';
				$i = 0;
				while ($i < count($series)) {
				 	# code...
				  if ($i+1 == count($series)):
					$s3 .= $series[$i]['id'].' and party_id='.$partyid;
				else:
					$s3 .= $series[$i]['id'].' and party_id='.$partyid.' or series_id = ';
				endif;
				 $i++;
			}
				
				$crud->where($s3);
				
				$output = $crud->render();
				$output->extra='';
				$this->_example_output($output);                	
			//submitted with trno
			elseif (isset($_POST['trno']) and !empty($_POST['trno'])):
				$trno=$_POST['trno'];
				$crud = new grocery_CRUD();
				$crud->set_table('trns_summary')
				->set_subject('Transaction')
					//->set_theme('datatables')
				->columns('id','series_id','no','date', 'party_id', 'expenses', 'amount','remark')
				->display_as('series_id','Series')
				->display_as('no','Trn Number')
				->display_as('date','Date')
				->display_as('party_id','Party')
				->display_as('expenses','Expenses')
				->display_as('remark','Remark')
				->display_as('amount', 'Amount')
				->order_by('id','desc')
				->unset_add()
				->unset_delete()
				->unset_edit()
				->unset_print()
				->set_relation('series_id','series','{payment_mode_name}-{tran_type_name}')
				->set_relation('party_id','party','{name}--{city}')
				->callback_column('amount',array($this,'_callback_amount'))
				->callback_column('expenses',array($this,'_callback_expenses'))
				->add_action('Edit Summary',base_url('application/pencil.jpeg'),'','',array($this, 'check_editable'))
				->add_action('View Details',base_url('application/view_details.png'),'trns_summary/view_details')
				->add_action('Edi Details',base_url('application/view_details.png'),'trns_details/check_editable')
				->add_action('Print Bill', base_url('application/print.png'), 'reports/print_bill');
				$series = $this->Series_model->get_all_series_by_location();
				
				$s3 = 'series_id = ';

				$i = 0;
				while ($i < count($series)) {
				 	# code...

				 if ($i+1 == count($series)):
					$s3 .= $series[$i]['id'].' and no = '.$trno;
				else:
					$s3 .= $series[$i]['id'].' and no = '.$trno.' or series_id = ';
				endif;
				 $i++;
			}
				
				$crud->where($s3);
				$output = $crud->render();
				$output->extra='';
				$this->_example_output($output);                	
			endif;
			
			
			}

}
?>
