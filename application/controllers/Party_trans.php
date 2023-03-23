<?php
class Party_trans extends CI_Controller{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->helper('url');
		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->load->library('table');
		$this->load->library('grocery_CRUD');
		//$this->output->enable_profiler(TRUE);
		$this->load->library('user_agent');
		$this->load->library('session');
		$this->load->model('Series_model');
		$this->load->model('Party_model');
		$this->load->model('Trns_details_model');
		$this->load->model('Trns_summary_model');
		$this->load->model('Party_trans_model');
		$this->load->helper('pdf_helper');
}



		public function transactions()
		
	{
			$crud = new grocery_CRUD();
			
			
			$crud->set_table('party_trans')
				->set_subject('Transaction')
				->set_theme('datatables')
				->columns('id','type','no', 'date','party_id','mop', 'chno', 'chdate','amount','remark')
				->add_fields('no', 'date', 'type', 'party_id','mop', 'chno','chdate','amount','remark')
				->required_fields('party_id','type','amount')
				->display_as('type','Trn Type')
				->display_as('no','Trn Number')
				->display_as('date','Date')
				->display_as('party_id','Party')
				->display_as('mop','Payment Mode')
				->display_as('chno','Ch No')
				->display_as('chdate', 'Ch Date')
				->display_as('amount', 'Amount')
				->display_as('remark', 'Remark')
				->order_by('id','desc')
				->field_type('type','dropdown',array('rct'=>'Receipt','pmt'=>'Payment', 'crn'=>'Credit Note'))
				->field_type('mop','dropdown',array('cash'=>'Cash','chq'=>'Cheque/DD', 'bktr'=>'Bank Transfer'))
				->set_relation('party_id','party','{name}--{city}');
							
				
				$output = $crud->render();
	
				$this->_example_output($output);                

	
	}
		function _example_output($output = null)
	{
		$this->load->view('templates/header');
		$this->load->view('our_template.php',$output);    
		$this->load->view('templates/footer');
	}   
	
		function ledger(){
		
		$data['ledger']=$this->Trns_details_model->ledger();
		
		$this->load->view('templates/header');
		$this->load->view('party_trans/ledger',$data);    
		$this->load->view('templates/footer');
		
		
		}
		
		function ind_ledger(){
		$id = $this->uri->segment(3);
		$data['party']=$this->Party_model->get_details($id);
		$data['trans']=$this->Trns_details_model->ind_ledger($id);
		$data['rtpt']=$this->Party_trans_model->get_details_by_party($id);
		$data['location']=$this->session->loc_name;
		$ledger=array();
		if ($data['party']['obl']>0):
		$debit=$data['party']['obl'];
		$credit=0;
		else:
		$debit=0;
		$credit=0-$data['party']['obl'];
		endif;
		$ledger[]=array('date'=>"0000-00-00", 'doc'=>'Op Bal', 'debit'=>$debit, 'credit'=>$credit, 'balance'=>0, 'remark'=>'');
		if (isset($data['trans']) and !empty($data['trans'])):
			foreach ($data['trans'] as $trans):
				if($trans['trantype']=='Sales'):
				$debit=$trans['amount']+$trans['expenses'];
				$credit=0;
				else:
				$debit=0;
				$credit=$trans['amount']+$trans['expenses'];
				endif;
			$doc=$trans['trantype'];	
			$ledger[]=array('date'=>$trans['date'], 'doc'=>$doc.' '.$trans['series'].'-'.$trans['no'], 'debit'=>$debit, 'credit'=>$credit, 'balance'=>0, 'remark'=>$trans['remark']);
			endforeach;
		endif;
		if (isset($data['rtpt']) and !empty($data['rtpt'])):
			foreach ($data['rtpt'] as $rtpt):
				if($rtpt['type']=='rct'):
				$debit=0;
				$credit=$rtpt['amount'];
				else:
				$debit=$rtpt['amount'];
				$credit=0;
				endif;
			$doc=$rtpt['type'];
			$ledger[]=array('date'=>$rtpt['date'], 'doc'=>$doc.' '.$rtpt['no'], 'debit'=>$debit, 'credit'=>$credit, 'balance'=>0,'remark'=>$rtpt['remark']);
			endforeach;
		endif;
		array_multisort(array_column($ledger, 'date'), SORT_ASC, $ledger);
		$balance=0;
		foreach ($ledger as $key=>$val):
		$ledger[$key]['balance']=$balance+$val['debit']-$val['credit'];
		$balance=$ledger[$key]['balance'];
		endforeach;
		$data['ledger']=$ledger;
		$this->load->view('templates/header');
		$this->load->view('party_trans/ind_ledger',$data);    
		$this->load->view('templates/footer');
	}
	/*
		public function _callback_amount($id, $row)
		{
		$sql=$this->db->select('SUM(quantity*(rate-cash_disc)-((quantity*(rate-cash_disc)))*discount/100) AS amount',false);
		$sql=$this->db->from ('trns_details');
		//$sql=$this->db->join('item', 'item.id=details.item_id');
		$sql=$this->db->where('trns_summary_id',$row->id);
		//$sql=$this->db->group_by('details.summary_id');
		$res=$this->db->get();
		$amount=$res->row()->amount;
		$amount=$amount+$row->expenses;
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

			
*/
}
?>
