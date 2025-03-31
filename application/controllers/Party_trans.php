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
		$this->load->library('Ntw');
		$this->output->enable_profiler(TRUE);
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
				->columns('id','type','series','no', 'date','party_id','mop', 'chno', 'chdate','amount','remark')
				->fields('series', 'no', 'date', 'type', 'party_id','mop', 'chno','chdate','amount','remark')
				->change_field_type('series', 'invisible')
				->change_field_type('no', 'invisible')
				->required_fields('date', 'party_id','type','amount', 'mop')
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
				->field_type('mop','dropdown',array('cash'=>'Cash','chq'=>'Cheque/DD', 'bktr'=>'Bank Transfer', 'crnt'=>'Credit Note'))
				->set_relation('party_id','party','{name}--{city}')
				->callback_before_insert(array($this,'getnumber'))
				->set_rules('mop', 'Payment Method', 'callback_check_mop')
				->unset_clone()
				->unset_delete()
				->unset_edit()
				->add_action('Delete','','Party_trans/delete_confirm')
				->add_action('Print','','Party_trans/printvr'); 							
				
				$output = $crud->render();
				
				$this->_example_output($output);                

	
	}
		
		function check_mop($mop){
		if($this->input->post('type')=='crn' and $mop!='crnt'):
			
				$this->form_validation->set_message('check_mop','Trn Type Credit Note should have mop as Credit Note');
				return false;
			
		elseif($this->input->post('type')!='crn' and $mop=='crnt'):
			
				$this->form_validation->set_message('check_mop', 'Trn Type Receipt/Payment should NOT have mop as Credit Note');
				return false;
			
		else:
			return true;
		endif;
		
		
		}
		
		
		function getnumber($post){
		if($post['type']=='rct'):
			if($post['mop']=='cash'):
				$series='CSRT';
			else:
				$series='CRRT';
			endif;
		elseif($post['type']=='pmt'):
			if($post['mop']=='cash'):
				$series=substr(date('F'),0,3).' -';
			else:
				$series=date('m').' B-';
			endif;
		else:
		$series=date('m').' J- ';
		endif;	
		if($no=$this->Party_trans_model->get_max_no($series)):
			$no++;
		else:
			$no=1;
		endif;
		$post['series']=$series;
		$post['no']=$no;
		return $post;
		
		}
		
		function printvr($id){
		//$id=$this->uri->segment('3');	
		$details=$this->Party_trans_model->get_details_by_id($id);
		$data['det']=$details;
		$this->load->view('templates/header');
		if($details['type']=='rct'):
			$this->load->view('party_trans/receipt', $data);
		elseif($details['type']=='pmt'):
			$this->load->view('party_trans/voucher', $data);	
		else:
			$this->load->view('party_trans/journal', $data);
		endif;
		$this->load->view('templates/footer');	
		}
	
		function delete_confirm($id){
		$details=$this->Party_trans_model->get_details_by_id($id);
		$data['details']=$details;
		$this->load->view('templates/header');
		$this->load->view('party_trans/delete_confirm',$data);
		$this->load->view('templates/footer');
		}
	
		function delete(){
		$id=$this->uri->segment('3');
		//echo $id;
		$this->load->view('templates/header');
		if($this->Party_trans_model->delete($id)):
		$data['delmes']= "Transaction deleted. Please inform Accounts if necessary";
		else:
		$data['delmes']="Transaction could not be deleted. Pl contact admin";
		endif;
		$this->load->view('party_trans/delete',$data);
		$this->load->view('templates/footer');
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
		
		//there is a provision to add opbal in party table. following function not used.
		function add_ob(){
		$this->form_validation->set_rules('party_id', 'Party', 'required');
		$this->form_validation->set_rules('amount', 'Amount', 'required');
		$this->form_validation->set_rules('drcr', 'DrCr', 'required');
		//unsubmitted
		if ($this->form_validation->run()==false):		
			$party=$this->Party_model->getall();
				//$i=0;
				foreach ($party as $p=>$v):
				$data['pty'][$v['id']]=$v['name'].' - '.$v['city'];
				//$data['pty'][$i]['party_name']=$p['name'].' - '.$p['city'];
				//$i++;
				endforeach;
				//$data['drcr']=array('name'=>'drcr', 'value'=>'Debit');
				$this->load->view('templates/header');
				$this->load->view('party_trans/add_ob',$data);    
				$this->load->view('templates/footer');
		else:
			//echo "Data to be added to table<br>";
			//print_r($_POST);
			$data=array('series'=>'OB', 'no'=>1, 'date'=>date('Y-m-d'), 'type'=>'OB', 'party_id'=>$_POST['party_id'], 'mop'=>'OB', 'amount'=>$_POST['amount'], 'remark'=>'Opening Balance');
			if($this->Party_trans_model->add($data)):
				echo "Data Added successfuly";
				
			else:
				echo "Could not Add Data. Pl contact Admin";	
			endif;
			echo "<a href=".site_url('welcome/home').">Go Home</a>";	
		endif;
				
					
		
		}
}
?>
