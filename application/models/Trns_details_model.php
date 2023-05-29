<?php
class Trns_details_model extends CI_Model{
	public function __construct()
		{		
		$this->load->database();
	}

	public function add($data){
	//called by trns_details/sales_complete_details, trns_details/edit_purchase_add, trns_details/edit_sales_add, trns_details/purch_complete_details
	if($this->db->insert('trns_details',$data)):
		return true;
	else:
		return false;
	endif;
		
	}

	public function get_details($pk){
		//called by Trns_summary/view_details, trns_details/check_editable, reports/print_bill
		$sql = $this->db->select('td.id, td.item_id, td.myprice, item.title,item.code,td.rate,td.quantity,td.discount,td.cash_disc,td.hsn,td.gst_rate,((rate-cash_disc)*quantity)-((rate-cash_disc)*quantity)*discount/100 as amount, td.trns_summary_id, td.inventory_id');
		$sql = $this->db->from('trns_details as td');
		$sql = $this->db->join('item','item.id = td.item_id');
		$sql = $this->db->where('td.trns_summary_id',$pk);
		$sql = $this->db->get();
		return $sql->result_array();

	}


		public function get_details_to_delete_purchase($pk){
		//called by trns_details/check_editable
		$sql = $this->db->select('td.id, td.item_id,item.title,item.code,td.rate,td.quantity,td.discount,td.cash_disc,td.hsn,td.gst_rate,((td.rate-cash_disc)*td.quantity)-((td.rate-cash_disc)*td.quantity)*discount/100 as amount, td.trns_summary_id, td.inventory_id');
		$sql = $this->db->from('trns_details as td');
		$sql = $this->db->join('item','item.id = td.item_id');
		$sql = $this->db->join('inventory','td.inventory_id = inventory.id');
		$sql = $this->db->where('td.trns_summary_id',$pk);
		$sql = $this->db->where('inventory.out_qty',0);
		$sql = $this->db->get();
		return $sql->result_array();
}

	public function confirm_one_entry($field, $value){
		//called by trns_details/check_editable
		$sql = $this->db->where($field,$value);
		$sql = $this->db->from('trns_details');
		$sql = $this->db->count_all_results();
		if ($sql>1):
			return false;
		else:
			return true;
		endif;


	}


	public function delete($id){
		//called by trns_details/edit_purchase_add, trns_details/edit_sales_add
		$sql = $this->db->where('id',$id);
		if ($sql = $this->db->delete('trns_details')):
			return true;
		else:
			return false;
		endif;
	}

		public function get_trans($id, $myprice){
		//called by item/det_stck
		$sql = $this->db->select('series.payment_mode_name, series.tran_type_name, trns_summary.series, trns_summary.date, trns_summary.no, trns_details.trns_summary_id, trns_details.item_id, trns_details.rate, trns_details.quantity, item.title ');
		$sql = $this->db->from('trns_details');
		$sql = $this->db->join('trns_summary', 'trns_details.trns_summary_id=trns_summary.id');
		$sql = $this->db->join('series', 'trns_summary.series = series.series');
		$sql = $this->db->join('item', 'trns_details.item_id=item.id');
		$sql = $this->db->join('inventory', 'trns_details.inventory_id=inventory.id');
		$sql = $this->db->where('trns_details.item_id', $id);
		$sql = $this->db->where('trns_details.myprice', $myprice);
		$sql = $this->db->where('inventory.location_id', $this->session->loc_id);
		$sql = $this->db->get();
		return $sql->result_array();
		}

		public function update_purchase_quantity($id, $quantity){
		
		//called by trns_details/edit_purchase_add
		$this->db->set('quantity',$quantity);
		$this->db->where('id',$id);
		$this->db->update('trns_details');
	}	
		
		public function get_billwise_details($frdate, $todate, $s){
		//called by reports/tran_report
		$sql = $this->db->select('ts.series, ts.no, ts.date, ts.party_id, ts.party_status, ts.expenses, party.name, party.city,  ts.party_state_io,
		round(sum(if (item_cat.name = "Books",
		(((td.rate-cash_disc)*td.quantity)-(((td.rate-cash_disc)*td.quantity)*discount/100))/(100+td.gst_rate)*100,0)),2)  
		as bamount,
		round(sum(if (item_cat.name = "Articles",
		(((td.rate-cash_disc)*td.quantity)-(((td.rate-cash_disc)*td.quantity)*discount/100))/(100+td.gst_rate)*100,0)),2)    
		as ramount,
		round  (sum((((td.rate-cash_disc)*td.quantity)-(((td.rate-cash_disc)*td.quantity)*discount/100))/(100+td.gst_rate)*td.gst_rate),2) as gst');
		$sql = $this->db->from('trns_summary as ts');
		$sql = $this->db->join('trns_details as td', 'ts.id = td.trns_summary_id');
		$sql = $this->db->join('party', 'ts.party_id = party.id');
		$sql = $this->db->join('item', 'td.item_id = item.id');
		$sql = $this->db->join('item_cat', 'item.cat_id = item_cat.id');
		$sql = $this->db->where('ts.date>=',$frdate);
		$sql = $this->db->where('ts.date<=',$todate);
		$sql = $this->db->where('ts.series=',$s); 
		$sql = $this->db->group_by('ts.series, ts.no');
		$sql = $this->db->get();
		return $sql->result_array();	
		}
		
		
		public function get_datewise_details($frdate, $todate, $s){
		//called by reports/tran_report
		$sql = "select t.date, sum(t.expenses) as texpenses, sum(d.bamount) as tbamount, sum(d.ramount) as tramount, sum(d.cgst) as tcgst, sum(d.sgst) as tsgst, sum(d.igst) as tigst
		from 
		(select trns_summary.id, trns_summary.series, trns_summary.date, trns_summary.expenses from trns_summary ) as t
		join
		(select trs.id, round(sum(if(item_cat.name = \"Books\", (((td.rate-cash_disc)*td.quantity)-(((td.rate-cash_disc)*td.quantity)*discount/100))/(100+td.gst_rate)*100,0)),2) as bamount, 
		round(sum(if(item_cat.name = \"Articles\", (((td.rate-cash_disc)*td.quantity)-(((td.rate-cash_disc)*td.quantity)*discount/100))/(100+td.gst_rate)*100,0)),2) as ramount, 
		round (sum(if(trs.party_state_io= \"I\",
		(((td.rate-cash_disc)*td.quantity)-(((td.rate-cash_disc)*td.quantity)*discount/100))/(100+td.gst_rate)*td.gst_rate,0)),2)/2 as cgst,
		round (sum(if(trs.party_state_io= \"I\",
		(((td.rate-cash_disc)*td.quantity)-(((td.rate-cash_disc)*td.quantity)*discount/100))/(100+td.gst_rate)*td.gst_rate,0)),2)/2 as sgst,
		round (sum(if(trs.party_state_io= \"O\",
		(((td.rate-cash_disc)*td.quantity)-(((td.rate-cash_disc)*td.quantity)*discount/100))/(100+td.gst_rate)*td.gst_rate,0)),2) as igst
		from trns_summary as trs
		join trns_details as td on trs.id = td.trns_summary_id
		join item on td.item_id = item.id 
		join item_cat on item_cat.id = item.cat_id 
		group by trs.id) as d
		on t.id = d.id 
		where t.date>=? and t.date<=? and t.series  = ?
		group by t.date";
		//  and t.series = $s";	
		//where t.date>=\"$frdate\" and t.date<=\"$todate\" and t.series  = \"$s\"
		return $this->db->query($sql, array ($frdate, $todate, $s))->result_array();	
		}
		
		
		public function get_datewise_compdetails($dat, $series){
		//called by reports/tran_report
		$sql = "select t.date, sum(t.expenses) as texpenses, sum(d.bamount) as tbamount, sum(d.ramount) as tramount, sum(d.cgst) as tcgst, sum(d.sgst) as tsgst, sum(d.igst) as tigst, sum(e.cash) as cash, sum(e.upi) as upi, sum(e.cashexp) as cashexp, sum(e.upiexp) as upiexp
		from 
		(select trns_summary.id, trns_summary.series, trns_summary.date, trns_summary.expenses from trns_summary ) as t
		join
		(select trs.id, round(sum(if(item_cat.name = \"Books\", (((td.rate-cash_disc)*td.quantity)-(((td.rate-cash_disc)*td.quantity)*discount/100))/(100+td.gst_rate)*100,0)),2) as bamount, 
		round(sum(if(item_cat.name = \"Articles\", (((td.rate-cash_disc)*td.quantity)-(((td.rate-cash_disc)*td.quantity)*discount/100))/(100+td.gst_rate)*100,0)),2) as ramount, 
		round (sum(if(trs.party_state_io= \"I\",
		(((td.rate-cash_disc)*td.quantity)-(((td.rate-cash_disc)*td.quantity)*discount/100))/(100+td.gst_rate)*td.gst_rate,0)),2)/2 as cgst,
		round (sum(if(trs.party_state_io= \"I\",
		(((td.rate-cash_disc)*td.quantity)-(((td.rate-cash_disc)*td.quantity)*discount/100))/(100+td.gst_rate)*td.gst_rate,0)),2)/2 as sgst,
		round (sum(if(trs.party_state_io= \"O\",
		(((td.rate-cash_disc)*td.quantity)-(((td.rate-cash_disc)*td.quantity)*discount/100))/(100+td.gst_rate)*td.gst_rate,0)),2) as igst
		from trns_summary as trs
		join trns_details as td on trs.id = td.trns_summary_id
		join item on td.item_id = item.id 
		join item_cat on item_cat.id = item.cat_id 
		group by trs.id) as d
		on t.id = d.id 
		join
		(select trns.id, trns.date, round(sum(if(series.payment_mode_name = \"Cash\", (((trnd.rate-cash_disc)*trnd.quantity)-(((trnd.rate-cash_disc)*trnd.quantity)*discount/100)),0)),2) as cash, 
		round(sum(if(series.payment_mode_name = \"UPI\", (((trnd.rate-cash_disc)*trnd.quantity)-(((trnd.rate-cash_disc)*trnd.quantity)*discount/100)),0)),2) as upi,
		if(series.payment_mode_name=\"Cash\",trns.expenses,0) as cashexp,
		if(series.payment_mode_name=\"UPI\",trns.expenses,0) as upiexp
		from trns_summary as trns join
		trns_details as trnd on trns.id = trnd.trns_summary_id join
		series on series.series = trns.series
		group by trns.id) as e
		on t.id = e.id
		where t.date=? and t.series in ?
		group by t.date";
		//  and t.series = $s";	
		//where t.date>=\"$frdate\" and t.date<=\"$todate\" and t.series  = \"$s\"
		$result = $this->db->query($sql, array ($dat, $series));	
		if ($result and $result->row_array()):
		return $result->row_array();
		else:
		return false;
		endif;
		}

		public function gstb2b($frdate, $todate){
		//called by reports/gstreports
		$sql = "select party.name, party.city, ts.party_gstno, ts.party_state, ts.series, ts.no, ts.date, td.gst_rate, 
		round(sum( (((td.rate-cash_disc)*td.quantity)-(((td.rate-cash_disc)*td.quantity)*discount/100))/(100+td.gst_rate)*100),2) as taxable,
		round(sum(if(ts.party_state_io=\"I\", (((td.rate-cash_disc)*td.quantity)-(((td.rate-cash_disc)*td.quantity)*discount/100))/(100+td.gst_rate)*td.gst_rate,0)),2)/2 as cgst,
		round(sum(if(ts.party_state_io=\"I\", (((td.rate-cash_disc)*td.quantity)-(((td.rate-cash_disc)*td.quantity)*discount/100))/(100+td.gst_rate)*td.gst_rate,0)),2)/2 as sgst,
		round(sum(if(ts.party_state_io=\"O\", (((td.rate-cash_disc)*td.quantity)-(((td.rate-cash_disc)*td.quantity)*discount/100))/(100+td.gst_rate)*td.gst_rate,0)),2) as igst
		from trns_summary as ts 
		join trns_details as td on ts.id=td.trns_summary_id
		join party on party.id = ts.party_id
		join series on series.series = ts.series
		where
		ts.party_status=\"REGD\" and
		td.gst_rate>0 and
		series.tran_type_name=\"Sales\" and
		ts.date>=? and
		ts.date<=?
		group by ts.series, ts.no, td.gst_rate";
		return $this->db->query($sql, array ($frdate, $todate))->result_array();	
		}
		

		public function gstb2cl($frdate, $todate){
		//called by reports/gstreports
		$sql = "select tsp.name, tsp.city, tsp.party_state, tsp.series, tsp.no, tsp.date, tdp.gst_rate, tdp.taxable, tdp.igst
		
		from (select party.name, party.city, ts.party_state, ts.series, ts.no, ts.date, ts.id,  
		round(sum( (((td.rate-cash_disc)*td.quantity)-(((td.rate-cash_disc)*td.quantity)*discount/100))/(100+td.gst_rate)*100) ,2) as tamount
		from trns_summary as ts 
		join trns_details as td on ts.id=td.trns_summary_id
		join party on party.id = ts.party_id
		join series on series.series = ts.series
		where
		ts.party_status!=\"REGD\" and
		ts.party_state_io=\"O\" and
		td.gst_rate>0 and
		series.tran_type_name=\"Sales\" and
		ts.date>=? and
		ts.date<=?
		group by ts.series, ts.no
		having tamount>=250000) as tsp
		join
		(select td.gst_rate, td.trns_summary_id, round(sum( (((td.rate-cash_disc)*td.quantity)-(((td.rate-cash_disc)*td.quantity)*discount/100))/(100+td.gst_rate)*100) ,2) as taxable, round(sum((((td.rate-cash_disc)*td.quantity)-(((td.rate-cash_disc)*td.quantity)*discount/100))/(100+td.gst_rate)*td.gst_rate),2) as igst
		from trns_details as td
		where td.gst_rate>0
		group by td.trns_summary_id, td.gst_rate) as tdp
		on tsp.id = tdp.trns_summary_id";
		return $this->db->query($sql, array ($frdate, $todate))->result_array();	
		}
		
		public function gstb2cs($frdate, $todate){
		$sql = "select tsp.party_state,  tdp.gst_rate, sum(tdp.taxable) as taxable, sum(tdp.cgst) as cgst, sum(tdp.sgst) as sgst, sum(tdp.igst) as igst
		
		from (select ts.party_state, ts.party_state_io, ts.id,  
		round(sum( (((td.rate-cash_disc)*td.quantity)-(((td.rate-cash_disc)*td.quantity)*discount/100))/(100+td.gst_rate)*100) ,2) as tamount
		from trns_summary as ts 
		join trns_details as td on ts.id=td.trns_summary_id
		join series on series.series = ts.series
		where
		ts.party_status!=\"REGD\" and
		td.gst_rate>0 and
		ts.date>=? and
		ts.date<=? and
		series.tran_type_name=\"Sales\" 
		group by ts.id
		HAVING not (ts.party_state_io=\"O\" and
		tamount>=250000)) as tsp
		join
		(select td.gst_rate, td.trns_summary_id, round(sum( (((td.rate-cash_disc)*td.quantity)-(((td.rate-cash_disc)*td.quantity)*discount/100))/(100+td.gst_rate)*100) ,2) as taxable, 
		round(sum(if(ts.party_state_io=\"I\", (((td.rate-cash_disc)*td.quantity)-(((td.rate-cash_disc)*td.quantity)*discount/100))/(100+td.gst_rate)*td.gst_rate,0)),2)/2 as cgst,
		round(sum(if(ts.party_state_io=\"I\", (((td.rate-cash_disc)*td.quantity)-(((td.rate-cash_disc)*td.quantity)*discount/100))/(100+td.gst_rate)*td.gst_rate,0)),2)/2 as sgst,
		round(sum(if(ts.party_state_io=\"O\", (((td.rate-cash_disc)*td.quantity)-(((td.rate-cash_disc)*td.quantity)*discount/100))/(100+td.gst_rate)*td.gst_rate,0)),2) as igst
		from trns_details as td
		join trns_summary as ts on ts.id=td.trns_summary_id
		where td.gst_rate>0
		group by td.trns_summary_id, td.gst_rate) as tdp
		on tsp.id = tdp.trns_summary_id
		group by tsp.party_state, tdp.gst_rate";
		return $this->db->query($sql, array ($frdate, $todate))->result_array();	
	}
			
		public function gstnil($frdate, $todate){
		$sql = "select 
		round(sum(if(ts.party_state_io=\"I\" and ts.party_status=\"REGD\" and gst_cat.name=\"Nil rated\", (((td.rate-cash_disc)*td.quantity)-(((td.rate-cash_disc)*td.quantity)*discount/100)),0)),2) as intrarnil,
		round(sum(if(ts.party_state_io=\"I\" and ts.party_status=\"REGD\" and gst_cat.name=\"Exempted\", (((td.rate-cash_disc)*td.quantity)-(((td.rate-cash_disc)*td.quantity)*discount/100)),0)),2) as intrarexe,
		round(sum(if(ts.party_state_io=\"I\" and ts.party_status!=\"REGD\" and gst_cat.name=\"Nil rated\", (((td.rate-cash_disc)*td.quantity)-(((td.rate-cash_disc)*td.quantity)*discount/100)),0)),2) as intraunil,
		round(sum(if(ts.party_state_io=\"I\" and ts.party_status!=\"REGD\" and gst_cat.name=\"Exempted\", (((td.rate-cash_disc)*td.quantity)-(((td.rate-cash_disc)*td.quantity)*discount/100)),0)),2) as intrauexe,
		round(sum(if(ts.party_state_io=\"O\" and ts.party_status=\"REGD\" and gst_cat.name=\"Nil rated\", (((td.rate-cash_disc)*td.quantity)-(((td.rate-cash_disc)*td.quantity)*discount/100)),0)),2) as interrnil,
		round(sum(if(ts.party_state_io=\"O\" and ts.party_status=\"REGD\" and gst_cat.name=\"Exempted\", (((td.rate-cash_disc)*td.quantity)-(((td.rate-cash_disc)*td.quantity)*discount/100)),0)),2) as interrexe,
		round(sum(if(ts.party_state_io=\"O\" and ts.party_status!=\"REGD\" and gst_cat.name=\"Nil rated\", (((td.rate-cash_disc)*td.quantity)-(((td.rate-cash_disc)*td.quantity)*discount/100)),0)),2) as interunil,
		round(sum(if(ts.party_state_io=\"O\" and ts.party_status!=\"REGD\" and gst_cat.name=\"Exempted\", (((td.rate-cash_disc)*td.quantity)-(((td.rate-cash_disc)*td.quantity)*discount/100)),0)),2) as interuexe
		from 
		trns_details as td join
		trns_summary as ts on td.trns_summary_id=ts.id join
		gst_cat on gst_cat.id = td.gcat_id join
		series on series.series = ts.series
		where
		series.tran_type_name=\"Sales\" and
		td.gst_rate=0 and 
		ts.date>=? and 
		ts.date<=?";
		return $this->db->query($sql, array ($frdate, $todate))->result_array();	
	
	}
	
		public function gsthsn($frdate, $todate){
		$sql = "select 	hsn, gst_rate, 
		sum(quantity) as quantity,
		round(sum( (((td.rate-cash_disc)*td.quantity)-(((td.rate-cash_disc)*td.quantity)*discount/100))/(100+td.gst_rate)*100) ,2) as taxable, 
		round(sum(if(ts.party_state_io=\"I\", (((td.rate-cash_disc)*td.quantity)-(((td.rate-cash_disc)*td.quantity)*discount/100))/(100+td.gst_rate)*td.gst_rate,0)),2)/2 as cgst,
		round(sum(if(ts.party_state_io=\"I\", (((td.rate-cash_disc)*td.quantity)-(((td.rate-cash_disc)*td.quantity)*discount/100))/(100+td.gst_rate)*td.gst_rate,0)),2)/2 as sgst,
		round(sum(if(ts.party_state_io=\"O\", (((td.rate-cash_disc)*td.quantity)-(((td.rate-cash_disc)*td.quantity)*discount/100))/(100+td.gst_rate)*td.gst_rate,0)),2) as igst from
		trns_details as td join
		trns_summary as ts on td.trns_summary_id=ts.id join
		series on series.series = ts.series
		where
		series.tran_type_name=\"Sales\" and
		td.gst_rate>0 and 
		ts.date>=? and 
		ts.date<=?
		group by hsn, gst_rate";	
		return $this->db->query($sql, array ($frdate, $todate))->result_array();		
	}
		
		public function gst32($frdate, $todate){
		//called by reports/gstreports
		$sql = "select tsp.name, tsp.city, tsp.party_state, tsp.party_status, tsp.series, tsp.no, tsp.date, tdp.gst_rate, tdp.taxable, tdp.igst
		
		from (select party.name, party.city, ts.party_state, ts.party_status, ts.series, ts.no, ts.date, ts.id  
		from trns_summary as ts 
		join trns_details as td on ts.id=td.trns_summary_id
		join party on party.id = ts.party_id
		join series on series.series = ts.series
		where
		ts.party_status!=\"REGD\" and
		ts.party_state_io=\"O\" and
		td.gst_rate>0 and
		series.tran_type_name=\"Sales\" and
		ts.date>=? and
		ts.date<=?
		group by ts.series, ts.no
		) as tsp
		join
		(select td.gst_rate, td.trns_summary_id, round(sum( (((td.rate-cash_disc)*td.quantity)-(((td.rate-cash_disc)*td.quantity)*discount/100))/(100+td.gst_rate)*100) ,2) as taxable, round(sum((((td.rate-cash_disc)*td.quantity)-(((td.rate-cash_disc)*td.quantity)*discount/100))/(100+td.gst_rate)*td.gst_rate),2) as igst
		from trns_details as td
		where td.gst_rate>0
		group by td.trns_summary_id, td.gst_rate) as tdp
		on tsp.id = tdp.trns_summary_id";
		return $this->db->query($sql, array ($frdate, $todate))->result_array();	
		}

		public function gstitc($frdate, $todate){
		//called by reports/gstreports
		$sql = "select party.name, party.city, ts.party_gstno, ts.party_state, ts.series, ts.no, ts.date,  
		round(sum( (((td.rate-cash_disc)*td.quantity)-(((td.rate-cash_disc)*td.quantity)*discount/100))/(100+td.gst_rate)*100),2) as taxable,
		round(sum(if(ts.party_state_io=\"I\", (((td.rate-cash_disc)*td.quantity)-(((td.rate-cash_disc)*td.quantity)*discount/100))/(100+td.gst_rate)*td.gst_rate,0)),2)/2 as cgst,
		round(sum(if(ts.party_state_io=\"I\", (((td.rate-cash_disc)*td.quantity)-(((td.rate-cash_disc)*td.quantity)*discount/100))/(100+td.gst_rate)*td.gst_rate,0)),2)/2 as sgst,
		round(sum(if(ts.party_state_io=\"O\", (((td.rate-cash_disc)*td.quantity)-(((td.rate-cash_disc)*td.quantity)*discount/100))/(100+td.gst_rate)*td.gst_rate,0)),2) as igst
		from trns_summary as ts 
		join trns_details as td on ts.id=td.trns_summary_id
		join party on party.id = ts.party_id
		join series on series.series = ts.series
		where
		ts.party_status=\"REGD\" and
		td.gst_rate>0 and
		series.tran_type_name=\"Purchase\" and
		ts.date>=? and
		ts.date<=?
		group by ts.series, ts.no";
		return $this->db->query($sql, array ($frdate, $todate))->result_array();	
		}
		
		public function gstnilinward($frdate, $todate){
		//called by reports/gstreports
		$sql = "select   
		round(sum(if(ts.party_state_io=\"I\", (((td.rate-cash_disc)*td.quantity)-(((td.rate-cash_disc)*td.quantity)*discount/100)),0)),2) as intra,
		round(sum(if(ts.party_state_io=\"O\", (((td.rate-cash_disc)*td.quantity)-(((td.rate-cash_disc)*td.quantity)*discount/100)),0)),2) as inter
		
		from trns_summary as ts 
		join trns_details as td on ts.id=td.trns_summary_id
		join series on series.series = ts.series
		join gst_cat on gst_cat.id = td.gcat_id
		where
		series.tran_type_name=\"Purchase\" and
		ts.date>=? and
		ts.date<=? and
		(ts.party_status=\"COMP\" or
		(ts.party_status!=\"COMP\" and
		(gst_cat.name=\"Nil rated\" or gst_cat.name=\"Exempted\")))
		
		";
		return $this->db->query($sql, array ($frdate, $todate))->result_array();	
	}
		
		public function ledger(){
		//called by party_trans/ledger
		$sql = "select a.id, a.name, a.city, a.obl, b.salesexp, b.purchexp, c.salepr, c.purchsr, d.rpt, d.pmt from
		(select party.id, party.name, party.city, party.obl from party) as a
		left join
		(select trns_summary.party_id, sum(if(series.tran_type_name='Sales' and series.payment_mode_name='Credit' or series.tran_type_name='Purchase Return' and  series.payment_mode_name='Credit',trns_summary.expenses,0)) as salesexp, sum(if(series.tran_type_name='Purchase' and series.payment_mode_name='Credit' or series.tran_type_name='Sales Return' and series.payment_mode_name='Credit' ,trns_summary.expenses,0)) as purchexp from party
		join trns_summary 
		on party.id = trns_summary.party_id
		left join series on series.series=trns_summary.series
		group by party.id) as b
		on a.id = b.party_id
		left join
		(select party.id as pid, round(sum(if(series.tran_type_name = 'Sales'  and series.payment_mode_name='Credit' or series.tran_type_name='Purchase Return' and series.payment_mode_name='Credit', (((td.rate-cash_disc)*td.quantity)-(((td.rate-cash_disc)*td.quantity)*discount/100)),0)),2) as salepr, 
		round(sum(if(series.tran_type_name = 'Purchase' and series.payment_mode_name='Credit'  or series.tran_type_name='Sale Return' and series.payment_mode_name='Credit' , (((td.rate-cash_disc)*td.quantity)-(((td.rate-cash_disc)*td.quantity)*discount/100)),0)),2) as purchsr
		from party join trns_summary as trs on party.id = trs.party_id
		join trns_details as td on trs.id = td.trns_summary_id
		join series on series.series = trs.series
		group by pid) as c
		on a.id=c.pid
		left join
		(select party_trans.party_id as partyid, round(sum(if(party_trans.type = 'rct',amount,0)),2) as rpt, round(sum(if(party_trans.type != 'rct',amount,0)),2) as pmt from party_trans
		group by party_trans.party_id) as d
		on a.id = d.partyid
		where a.obl<>0 or b.salesexp<>0 or b.purchexp<>0 or c.salepr<>0 or c.purchsr<>0 or d.rpt<>0 or d.pmt<>0 order by a.name
		";
		return $this->db->query($sql)->result_array();	
		//where series.location_name=? where a.salesexp<>0 or a.purchexp<>0 or b.salepr<>0 or b.purchsr<>0 or c.rpt<>0 or c.pmt<>0 where series.location_name=?
		
		}
			
		public function ind_ledger($id){
		//called by party_trans/ind_ledger
		$sql="select a.*, b.*, c.mop, c.trantype from
		(select trns_summary.id, trns_summary.series, trns_summary.no, trns_summary.date, trns_summary.expenses, trns_summary.remark from trns_summary) as a
		join 
		(select series.series, series.payment_mode_name as mop, series.tran_type_name as trantype from series 
		where series.payment_mode_name='Credit') as c
		on a.series=c.series
		join
		(select trns_summary.id as trsid, round(sum((((td.rate-cash_disc)*td.quantity)-(((td.rate-cash_disc)*td.quantity)*discount/100))),2) as amount
		from trns_details as td
		join trns_summary on trns_summary.id = td.trns_summary_id
		where trns_summary.party_id=?
		group by trns_summary.id) as b
		on a.id=b.trsid";
		return $this->db->query($sql, array ($id))->result_array();	
		}	
		
		public function get_minno_series($series, $frdate, $todate){
		//called by reports/gstreports	
		$sql="select trns_summary.no FROM trns_summary where trns_summary.id = (SELECT MIN(id) from trns_summary WHERE trns_summary.series=? and  trns_summary.date>=? and trns_summary.date<=?)";
		if($this->db->query($sql, array($series, $frdate, $todate))->row_array() and null!==($this->db->query($sql, array($series, $frdate, $todate))->row_array())):
				return $this->db->query($sql, array($series, $frdate, $todate))->row_array();	
		else:
			return array('no'=>'No Entry');
		endif;
		}
		
		
		public function get_maxno_series($series, $frdate, $todate){
		//called by reports/gstreports	
		$sql="select trns_summary.no FROM trns_summary where trns_summary.id = (SELECT MAX(id) from trns_summary WHERE trns_summary.series=? and  trns_summary.date>=? and trns_summary.date<=?)";
		if($this->db->query($sql, array($series, $frdate, $todate))->row_array() and null!==($this->db->query($sql, array($series, $frdate, $todate))->row_array())):
				return $this->db->query($sql, array($series, $frdate, $todate))->row_array();	
		else:
			return array('no'=>'No Entry');
		endif;
		}
		
		
		
		
		public function get_total_series($series, $frdate, $todate){
		//called by reports/gstreports	
		$sql="SELECT COUNT(id) as total from trns_summary where trns_summary.series=? and trns_summary.date>=? and trns_summary.date<=?";
		return $this->db->query($sql, array($series, $frdate, $todate))->row_array();	
		
	}
	
		public function get_cancelled_series($series, $frdate, $todate){
		//called by reports/gstreports	
		$sql="SELECT COUNT(id) as cancelled from trns_summary where trns_summary.series=? and trns_summary.date>=? and trns_summary.date<=? and trns_summary.remark=\"Cancelled\"";
		return $this->db->query($sql, array($series, $frdate, $todate))->row_array();	
	}
	
		public function discountreport($loc, $frdate, $todate){
		$sql="select item.title, td.rate, sum(td.quantity) as quantity, sum(((td.rate-td.cash_disc)*td.quantity*(100-td.discount)/100)/(100+gst_rate)*100) as netsales, sum(inventory.cost*td.quantity) as cost from
		trns_details as td join
		trns_summary as ts on td.trns_summary_id=ts.id join
		item on td.item_id=item.id join 
		inventory on td.inventory_id=inventory.id join
		series on series.series=ts.series
		where series.tran_type_name='Sales'
		and series.location_name=?
		and ts.date>=? and ts.date<=?
		group by inventory.id";
		return $this->db->query($sql, array($loc, $frdate, $todate))->result_array();	
		
		
		
		
		}
	
	
	
}
