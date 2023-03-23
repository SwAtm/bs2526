<?php
class Trnf_summary extends CI_Controller{
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
		$this->load->model('Trnf_details_model');
		$this->load->model('Inventory_model');
		$this->load->model('Location_model');		
		$this->load->model('Trnf_summary_model');		
		$this->load->helper('pdf_helper');

}



		public function summary()
		
	{
			$crud = new grocery_CRUD();
			
			
			$crud->set_table('trnf_summary')
				->set_subject('Transfer')
				->where('trnf_summary.from_id', $this->session->loc_id)
				->or_where('trnf_summary.to_id', $this->session->loc_id)
				->order_by('id','desc')
				->columns('id','date','from_id','to_id')
				->display_as('id','ID')
				->display_as('date','Date')
				->display_as('from_id','From')
				->display_as('to_id','To')
				->unset_print()
				->set_relation('from_id','locations','{name}')
				->set_relation('to_id','locations','{name}')
				->set_rules('date', 'Date', 'required')
				->unset_delete()
				->unset_add()
				->unset_edit()
				->add_action('View Details',base_url('application/view_details.png'),'trnf_summary/view_details');
				//better not to have facility to edit the summary, since that will involve changing loc id in inventory as well. Hence edit is unset above.
				/*
				$state = $crud->getState();
				$stateInfo = $crud->getStateInfo();
				if ('edit' == $state || 'update' == $state || 'update_validation' == $state):
					
					$details = $this->Trnf_summary_model->get_details_by_id($stateInfo->primary_key);
					if ($details['to_id'] == $this->session->loc_id):
						$crud->field_type('to_id','readonly')
						->set_rules('from_id', 'Location From', 'required|callback_check_location');
					else:
						$crud->field_type('from_id','readonly')
						->set_rules('to_id', 'Location To', 'required|callback_check_location');
					endif;
					
				endif;
				*/
				$output = $crud->render();
				$output->extra ='';
				$this->_example_output($output);                

	
	}

		function _example_output($output = null)
	{
		$this->load->view('templates/header');
		$this->load->view('templates/trans_template.php',$output);    
		$this->load->view('templates/footer');
	}   


		public function view_details($pk){
		$data['trnf_details'] = $this->Trnf_details_model->trnf_details_per_id($pk);
		$data['trnf_summary'] = $this->Trnf_summary_model->trnf_summary_per_id($pk);
			
		//generate a pdf
		$this->load->view('trnf_details/view_details',$data);
			

		}
}
/*
 		
		function check_location($str){
	
			if ($str==$this->session->loc_id):
				$this->form_validation->set_message('check_location', 'Both locations cannot be same');
				return false;
			else:
				return true;
			endif;		
		}
		
*/
?>
