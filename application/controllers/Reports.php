<?php
	class Reports extends CI_Controller{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->helper('url');
		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->load->library('table');
		$this->load->model('Item_model');
		$this->load->model('Party_model');
		$this->load->model('Trns_summary_model');
		$this->load->model('Series_model');
		$this->load->model('Inventory_model');
		$this->load->model('Trns_details_model');
		$this->load->model('Trnf_details_model');
		$this->load->model('Profo_details_model');
		$this->load->library('session');
		$this->load->helper('pdf_helper');
		$this->output->enable_profiler(TRUE);
		$this->load->library('Qrcodeg');
		$this->load->helper('form');
	}

	public function print_bill($id){
		$id = $this->uri->segment(3);
		$data['summary']= $this->Trns_summary_model->get_details_by_id($id);
		$data['party'] = $this->Party_model->get_details($data['summary']['party_id']);
		//$data['details'] = $this->Trns_details_model->get_details($data['summary']['id']);
		$data['location'] = $this->session->loc_name;
		$gst = 0;
		$data['taxamt'] = 0;
		$data['notaxamt'] = 0;
		$data['totamount']= 0;
		$data['totquantity']=0;
		$details = $this->Trns_details_model->get_details($data['summary']['id']);
		//sum quantity in details array
		$newdetails=array();
		$found='n';
		foreach ($details as $k):
			foreach ($newdetails as $key=>$d):
				if($k['item_id']==$d['item_id'] and $k['hsn']==$d['hsn'] and $k['rate']==$d['rate'] and $k['discount']==$d['discount'] and $k['cash_disc']==$d['cash_disc']):
					$newdetails[$key]['quantity']+=$k['quantity'];
					$newdetails[$key]['amount']+=$k['amount'];
				$found='y';
				endif;
			endforeach;
		if ('n'==$found):
		$newdetails[]=$k;
		endif;
		$found='n';
		endforeach;			
		$data['details']=$newdetails;	
		
		//This is not reqd now. If RCM comes into effect, then this will be reqd since then in details file gst rate would have been recorded to work out RCM.
		/*
		if (strtoupper($data['summary']['party_status'])!='REGD' AND strtoupper($data['summary']['tran_type_name'])=='PURCHASE'):
			foreach ($data['details'] as $det=>$v):
				$data['details'][$det]['gst_rate']=0;
				//print_r($det);
				//echo "<br>";
			endforeach;
		endif;
		*/
				
		foreach ($data['details'] as $det):
			if ($det['gst_rate']>0):
			$data['taxamt']+=$det['amount']/(100+$det['gst_rate'])*100;
			$gst+=$det['amount']/(100+$det['gst_rate'])*$det['gst_rate'];
			else:
			$data['notaxamt']+=$det['amount'];
			endif;
		$data['totamount']+=$det['amount'];
		$data['totquantity']+=$det['quantity'];
		endforeach;
		if (strtoupper($data['summary']['party_state_io'])=='I'):
			$data['cgst'] = $data['sgst'] = $gst/2;
			$data['igst'] = 0;
		else:
			$data['igst'] = $gst;
			$data['cgst'] = $data['sgst'] = 0;
		endif;
		$data['totamount']+=$data['summary']['expenses'];
		//generate QR Code for UPI Sales
		if ($data['summary']['payment_mode_name'] == "UPI" and $data['summary']['tran_type_name'] == "Sales"):
		$text = "upi://pay?pa=".   			 // payment method.
                //"gpay-11192753290@okbizaxis".          // VPA number.
                //"373901010035580@UBIN0537390.ifsc.npci".          // VPA number.
                "ramak83109@barodampay".          // VPA number.
                "&am=".number_format($data['totamount'],2,".",",").       // this param is for fixed amount (non editable).
                "&pn=Ramakrishna%20Mission Ashrama, Belgaum".      // to showing your name in app.
                "&cu=INR".                  // Currency code.
                "&mode=02";                 // mode O2 for Secure QR Code.
                //"&trxnID=".$data['summary']['payment_mode_name'].' - '.$data['summary']['tran_type_name']. ' - '.$data['summary']['no'];
                //"&orgid=189999" +            //If the transaction is initiated by any PSP app then the respective orgID needs to be passed.
                //"&sign=MEYCIQC8bLDdRbDhpsPAt9wR1a0pcEssDaV".   // Base 64 encoded Digital signature needs to be passed in this tag
                //"Q7lugo8mfJhDk6wIhANZkbXOWWR2lhJOH2Qs/OQRaRFD2oBuPCGtrMaVFR23t"
		$file = SAVEPATH.'/qrc.png';
		$ecc = 'L';
		$pixel_Size = 40;
		$frame_Size = 0;
		QRcode::png($text, $file, $ecc, $pixel_Size, $frame_Size);
		endif;
		
		$this->load->view('reports/print_bill',$data);
		
		
		}
		
		public function tran_report(){
		//set validation rules
		$this->form_validation->set_rules('frdate', 'From Date', 'required');
		$this->form_validation->set_rules('todate', 'To Date', 'required');
		$this->form_validation->set_rules('rtype', 'Type of Report', 'required');
		$this->form_validation->set_rules('ttype[]', 'Type of Transaction', 'required');
		$data['series'] = $this->Series_model->get_all_series_by_location();
		//first pass
		if ($this->form_validation->run()==false):
			$this->load->view('templates/header');
			$this->load->view('reports/tran_report',$data);
			$this->load->view('templates/footer');
		//submitted, validated
		else:
			//print_r($_POST);
			//var_dump($_POST);
			$series=$this->input->post('ttype');
			//$frdate = DateTime::createFromFormat('Y-m-d',$_POST['frdate'])->format('Y-m-d');
			$frdate=date('Y-m-d',strtotime($this->input->post('frdate')));
			$todate=date('Y-m-d',strtotime($this->input->post('todate')));
			//var_dump($frdate);
			if ('bill' == $this->input->post('rtype')):
				foreach ($series as $s):
					$details[$s]['det'] = $this->Trns_details_model->get_billwise_details($frdate, $todate, $s);
					$ser = $this->Series_model->get_details_from_series($s);
					$details[$s]['name'] = $ser['payment_mode_name']." - ".$ser['tran_type_name'];
				endforeach;
			//allocate GST
			
			foreach ($details as $d=>$v):
			//if ('name'==$v):
			//continue; d
			//endif;
				foreach ($v['det'] as $de=>$ve):
					if ('I' == strtoupper($ve['party_state_io'])):
						$details[$d]['det'][$de]['sgst']=$details[$d]['det'][$de]['cgst']=$ve['gst']/2;
						$details[$d]['det'][$de]['igst']=0;
					else:
						$details[$d]['det'][$de]['sgst']=$details[$d]['det'][$de]['cgst']=0;
						$details[$d]['det'][$de]['igst']=$ve['gst'];
					endif;
				
				
				endforeach;
			
			endforeach;
			
			
			$data['details']=$details;
			$data['frdate']=$frdate;
			$data['todate']=$todate;
			$data['rtype']=$this->input->post('rtype');
			$this->load->view('reports/billwise', $data);
			
			else:
			foreach ($series as $s):
			$details[$s]['det'] = $this->Trns_details_model->get_datewise_details($frdate, $todate, $s);
			$ser = $this->Series_model->get_details_from_series($s);
			$details[$s]['name'] = $ser['payment_mode_name']." - ".$ser['tran_type_name'];
			endforeach;
			$c=0;
			foreach ($details as $dc):
				foreach ($dc['det'] as $dcdet):
					if (!empty($dcdet)):
						$c++;
					endif;
				endforeach;
			endforeach;
			echo $c;
			if ($c==0):
			die ("No transcations. <a href=".site_url('welcome/home').">Go Home</a>");
			endif;
			//get all dates in an array
			$end = new DateTime($todate);
			$end = $end->modify( '+1 day' );
			$dater= new DatePeriod(new DateTime($frdate), new DateInterval('P1D'), $end);
			$im=0;
			foreach ($dater as $da):
			$dates[]=$da->format('Y-m-d');
			endforeach;
			print_r($dates);
			foreach ($dates as $dat):
			$tempr=array();
			if ($tempr=$this->Trns_details_model->get_datewise_compdetails($dat,$series)):
			$details1[$im]=$this->Trns_details_model->get_datewise_compdetails($dat,$series);
			$im++;
			else:
			continue;
			endif;
			endforeach;
			unset($tempr);
			
			//print_r($details1);
			
			//$data['dates']=$dates;			,
			$data['details']=$details;
			$data['details1']=$details1;
			$data['frdate']=$frdate;
			$data['todate']=$todate;
			$data['rtype']=$this->input->post('rtype');			
				
			$this->load->view('reports/datewise', $data);
			endif;
			$this->load->view('templates/footer');
				
		endif;
		
		}
		
		public function gstreports(){
		//set validation rules
		$this->form_validation->set_rules('frdate', 'From Date', 'required');
		$this->form_validation->set_rules('todate', 'To Date', 'required');
		//$this->form_validation->set_rules('rtype', 'Type of Report', 'required');
		//$this->form_validation->set_rules('ttype[]', 'Type of Transaction', 'required');
		//$data['series'] = $this->Series_model->get_all_series_by_location();
		//first pass
		if ($this->form_validation->run()==false):
			$this->load->view('templates/header');
			$this->load->view('reports/gst_reports');
			$this->load->view('templates/footer');
		else:
		//submitted, validated	
			$data['frdate']=date('Y-m-d',strtotime($this->input->post('frdate')));
			$data['todate']=date('Y-m-d',strtotime($this->input->post('todate')));
			$data['det']['b2b']=$this->Trns_details_model->gstb2b($data['frdate'], $data['todate']);
			$data['det']['b2cl']=$this->Trns_details_model->gstb2cl($data['frdate'], $data['todate']);
			$data['det']['b2cs']=$this->Trns_details_model->gstb2cs($data['frdate'], $data['todate']);
			$data['det']['nil']=$this->Trns_details_model->gstnil($data['frdate'], $data['todate']);
			$data['det']['hsn']=$this->Trns_details_model->gsthsn($data['frdate'], $data['todate']);
			$data['det']['gst32']=$this->Trns_details_model->gst32($data['frdate'], $data['todate']);
			$data['det']['gstitc']=$this->Trns_details_model->gstitc($data['frdate'], $data['todate']);
			$data['det']['gstnilinward']=$this->Trns_details_model->gstnilinward($data['frdate'], $data['todate']);
			//get all sales series
			$salesseries=$this->Series_model->get_all_sales_series();
			foreach ($salesseries as $s):
			$ser[]=$s['series'];
			endforeach;
			foreach ($ser as $series):
				$data['det']['documents'][$series]['begin']=$this->Trns_details_model->get_minno_series($series,$data['frdate'], $data['todate'])['no'];
				
				$data['det']['documents'][$series]['end']=$this->Trns_details_model->get_maxno_series($series,$data['frdate'], $data['todate'])['no'];
				
				$data['det']['documents'][$series]['total']=$this->Trns_details_model->get_total_series($series,$data['frdate'], $data['todate'])['total'];
				
				$data['det']['documents'][$series]['cancelled']=$this->Trns_details_model->get_cancelled_series($series,$data['frdate'], $data['todate'])['cancelled'];
			endforeach;
			//$data['det']['outward']=$this->Trns_details_model->outward($data['frdate'], $data['todate']);
			$data['series']=$ser;
			
			$this->load->view('reports/gst', $data);
		endif;
		
		
		
		}


}
?>
