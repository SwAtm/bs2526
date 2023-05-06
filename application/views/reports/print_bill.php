<?php
tfpdf();
//$amt = preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $det['amount']);
//$ntw = new \NTWIndia\NTWIndia();
if ((count($details)>12) OR ($summary['payment_mode_name']=='Credit' and $summary['tran_type_name'] == 'Sales')):
$pdf = new tFPDF('P', 'mm', array(210,296));
$y = 266;
//$noofpages = ceil(count($details)/28);
else:
$pdf = new tFPDF('L', 'mm', array(210,148));
$y = 118;
endif;
$i=1;
$pdf->setLeftMargin(10);
$pdf->SetAutoPageBreak(false);
$pdf->SetFillColor(200);
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);
$pdf->Image(base_url(IMGPATH.'logo.jpg'),10,10,15,'');
$pdf->setXY(10,10);
$pdf->Cell(190,5,$this->session->cname.' - '.rtrim($location),0,1,'C');
$pdf->SetFont('Arial','',12);
$pdf->Cell(190,5,$this->session->caddress.' :: Ph: '.$this->session->cphone,0,1,'C');
$pdf->SetFont('Arial','',12);
$pdf->Cell(190,5,'email: '.$this->session->cemail.':: GSTIN: .'.$this->session->cgst,0,1,'C');
$pdf->ln(2);
$pdf->cell(190,0,'',1,1);
$pdf->ln(5);
$pdf->SetFont('Arial','B',12);
$pdf->cell(60,5,$summary['payment_mode_name'].' - '.$summary['tran_type_name']. ' - '.$summary['no'],0,0,'L');
$pdf->SetFont('Arial','',12);
$pdf->cell(60,5,'Party GST No: '.$summary['party_gstno'],0,0,'L');
$pdf->cell(60,5,'Date: '.date('d-m-Y',strtotime($summary['date'])),0,1,'R');
$pdf->ln(3);
$pdf->SetFont('Arial','',10);
$pdf->Cell(100,5,'Party Name and Address: '.$party['name'].', '.$party['city'],0,0,'L');
if ($summary['tran_type_name'] == "Purchase Return" or $summary['tran_type_name'] == "Sales"):
$pdf->Cell(90,5,'Place of Supply: '.$summary['party_state'],0,1,'R');
else:
$pdf->Cell(90,5,'',0,1,'R');
endif;
$pdf->Cell(55,5,'Item',1,0,'C');
$pdf->Cell(25,5,'Code',1,0,'C');
$pdf->Cell(20,5,'HSN',1,0,'C');
$pdf->Cell(20,5,'Rate',1,0,'C');
$pdf->Cell(15,5,'Qty',1,0,'C');
$pdf->Cell(15,5,'Disc',1,0,'C');
$pdf->Cell(15,5,'Cash_D',1,0,'C');
//$pdf->Cell(15,5,'Tax. Amt',1,0,'C');
//$pdf->Cell(15,5,'Tax',1,0,'C');
$pdf->Cell(25,5,'Total',1,1,'C');
foreach ($details as $d):
$pdf->Cell(55,5,substr($d['title'],0,25),1,0,'L');
$pdf->Cell(25,5,$d['code'],1,0,'C');
//$pdf->Cell(25,5,$d['rate'],1,0,'C');
$pdf->Cell(20,5,$d['hsn'],1,0,'C');
$pdf->Cell(20,5,number_format($d['rate'],2,".",","),1,0,'C');
$pdf->Cell(15,5,$d['quantity'],1,0,'C');
//$pdf->Cell(15,5,$d['discount'],1,0,'C');
$pdf->Cell(15,5,number_format($d['discount'],2,".",","),1,0,'C');
$pdf->Cell(15,5,number_format($d['cash_disc'],2,".",","),1,0,'C');
//$pdf->Cell(25,5,$d['cash_disc'],1,0,'C');
$pdf->Cell(25,5,number_format($d['amount'],2,".",","),1,1,'R');

if ($pdf->GetY()>=270):
$pdf->ln(5);
$pdf->Cell(190,5,'Page '.$i,0,1,'C');
$i++;
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);
$pdf->Image(base_url(IMGPATH.'logo.jpg'),10,10,15,'');
$pdf->setXY(0,10);
$pdf->Cell(190,5,$this->session->cname.' - '.$location,0,1,'C');
$pdf->SetFont('Arial','',14);
$pdf->Cell(190,5,$this->session->caddress,0,1,'C');
$pdf->Cell(190,5,'Ph: '.$this->session->cphone.' :: email: '.$this->session->cemail,0,1,'C');
$pdf->ln(2);
$pdf->cell(190,0,'',1,1);
$pdf->ln(5);
$pdf->SetFont('Arial','B',14);
$pdf->cell(95,5,$summary['payment_mode_name'].' - '.$summary['tran_type_name']. ' - '.$summary['no'],0,0,'L');
$pdf->cell(95,5,'Date: '.date('d-m-Y',strtotime($summary['date'])),0,1,'R');
$pdf->ln(3);
$pdf->SetFont('Arial','',10);
$pdf->Cell(100,5,'Party Name and Address: '.$party['name'].', '.$party['city'],0,0,'L');
$pdf->Cell(90,5,'Place of Supply: '.$summary['party_state'],0,1,'R');
$pdf->Cell(55,5,'Item',1,0,'C');
$pdf->Cell(25,5,'Code',1,0,'C');
$pdf->Cell(20,5,'HSN',1,0,'C');
$pdf->Cell(20,5,'Rate',1,0,'C');
$pdf->Cell(15,5,'Qty',1,0,'C');
$pdf->Cell(15,5,'Disc',1,0,'C');
$pdf->Cell(15,5,'Cash_D',1,0,'C');
//$pdf->Cell(15,5,'Tax. Amt',1,0,'C');
//$pdf->Cell(15,5,'Tax',1,0,'C');
$pdf->Cell(25,5,'Total',1,1,'C');
endif;

endforeach;
$pdf->Cell(120,5,'Total Quantity',1,0,'C');
$pdf->Cell(15,5,$totquantity,1,0,'C');
$pdf->Cell(30,5,'Total + expenses',1,0,'C');
$pdf->Cell(25,5,number_format($totamount,2,".",","),1,1,'R');


/*
if ($cgst+$igst>0):
	if ($cgst>0):
	$str = "Taxable Amount: ".number_format($taxamt,2,".",",")."  CGST = SGST = ".number_format($cgst,2,".",",")." No-Tax Amount = ".number_format($notaxamt,2,".",",")." Expenses: ".number_format($summary['expenses'],2,".",",")." Total Payable: ".number_format($totamount,2,".",",");
	else:
	$str = "Taxable Amount: ".number_format($taxamt,2,".",",")."  IGST = ".number_format($igst,2,".",",")." No-Tax Amount = ".number_format($notaxamt,2,".",",")." Expenses: ".number_format($summary['expenses'],2,".",",")." Total Payable: ".number_format($totamount,2,".",",");
	endif;
else:
	$str = " No-Tax Amount = ".$notaxamt;
endif;
*/

$pdf->SetY($y);
$pdf->Cell(190,5,'Remark: '.$summary['remark'],0,1,'L');
$str = "Taxable Amount: ".number_format($taxamt,2,".",",")."  CGST = SGST = ".number_format($cgst,2,".",",")."  IGST = ".number_format($igst,2,".",",")." No-Tax Amount = ".number_format($notaxamt,2,".",",")." Expenses: ".number_format($summary['expenses'],2,".",",")." Total Payable: ".number_format($totamount,2,".",",");
$pdf->MultiCell(150,5,$str,0,'L');

if ($summary['payment_mode_name'] == "UPI" and $summary['tran_type_name'] == "Sales"):
$pdf->Image(SAVEPATH.'qrc.png',165,$y,'15','');
endif;

//$pdf->Image(base_url(IMGPATH.'home.png'),85,$y+10,5,'','',site_url('welcome/home'));
//$pdf->Image(base_url(IMGPATH.'list.png'),105,$y+10,5,'','',site_url('trns_summary/summary'));
$pdf->ln(5);
$pdf->Cell(63,5,'Home',0, 0, 'C',1, site_url('welcome/home'));
$pdf->Cell(63,5,'List',0, 0, 'C',1, site_url('trns_summary/summary'));
$pdf->Cell(63,5,'Add Sales',0, 1, 'C',1, site_url('trns_details/sales_add_details'));
$pdf->SetY($y+10);
$pdf->Cell(190,5,'Signature',0,0,'R');
$filename="bill_".$summary['payment_mode_name'].' - '.$summary['tran_type_name']. ' - '.$summary['no'].".pdf";
$pdf->Output($filename,'I');

/*







echo "<pre>";
print_r($summary);
print_r($party);
print_r($details);
echo "</pre>";
echo "<a href =".site_url('welcome/home').">Home</a>"
?>


<?php

$pdf->SetFont('Arial','I',13);
$pdf->cell(57,5,'Received with thanks from:',0,0,'L');
$pdf->SetFont('Times','',13);
$pdf->Cell(123,5,$det['name'],0,1,'L');
$pdf->Multicell(180,6,ucwords(strtolower($det['address'])),0,'L');
$pdf->Cell(90,5,$det['city_pin'],0,0,'L');
$pdf->Cell(90,5,($det['id_name']!=='NOT AVAILABLE'?$det['id_name'].': '.$det['id_no']:''),0,1,'R');
$pdf->ln(3);
$pdf->SetFont('Arial','I',13);
$pdf->cell(180,5,'A sum of Rupees:',0,1,'L');
$pdf->ln(1);
$pdf->SetFont('Times','',13);
$pdf->Multicell(180,5,$ntw->numToWord($det['amount']).' Only',0,'L');
$pdf->ln(3);
$pdf->SetFont('Arial','I',13);
$pdf->cell(20,5,"Towards: ", 0,0,'L');
$pdf->SetFont('Times','',13);
$pdf->cell(160,5,$det['purpose'],0,1,'L');
$pdf->ln(3);
$pdf->SetFont('Arial','',13);
$pdf->cell(12,5,"Vide: ",0,0,'L');
$pdf->SetFont('Times','',13);
$pdf->Multicell(168,5,($det['mode_payment']=="Cash"?$det['mode_payment']:$det['mode_payment']. ": ").($det['ch_no']!==''?"No: ".$det['ch_no']:'')." ".($det['tr_date']!==''?'Dt: '.$det['tr_date']:'')." ".$det['pmt_details'],0,'L');
//$pdf->Multicell(180,5,"Vide: ".($det['mode_payment']=="Cash"?$det['mode_payment']:$det['mode_payment']. ": ").($det['ch_no']!==''?"No: ".$det['ch_no']:'')." ".($det['tr_date']!==''?'Dt: '.$det['tr_date']:'')." ".$det['pmt_details'],0,'L');
$pdf->Image(base_url(IMGPATH.'Signature.jpg'), 170,103);
$pdf->Image(base_url(IMGPATH.'rupee.png'),25,110,5,'');
$pdf->SetFont('Arial','',14);
$pdf->setXY(30,110);
$pdf->cell(70,5,$amt,0,0,'L');
$pdf->SetFont('Arial','',12);
$pdf->cell(78,5,'Collected By',0,0,'L');
$pdf->cell(27,5,'Secretary',0,1,'L');
$pdf->ln(1);
$pdf->cell(180,0,'',1,1);
//if ($det['amount']>2000 AND $det['mode_payment']=="Cash"):
/*
if ($det['pan']=='' OR $det['mode_payment']=="Cash"):
$mess="Our PAN: AAAAR1077P. Under Schedule I, Article 53, Exemption (b) of the Indian Stamp Act, Charitable Institutions are not required to issue any stamped receipt for amounts received by them.";
elseif (strtotime($det['date'])<strtotime('01-06-2021')):
$mess="Donations are exempt from Income Tax u/s 80G(5)(vi) of the IT Act 1961, vide order no DIT(E)/848/8E/109/69-70, dated 12-01-2009 which has been further extended in perpetuity by letter no DIT(E)/109/69-70 dated 26-09-2011. Our PAN: AAAAR1077P. Under Schedule I, Article 53, Exemption (b) of the Indian Stamp Act, Charitable Institutions are not required to issue any stamped receipt for amounts received by them.";
else:
$mess="Donations are exempt from Income Tax u/s 80G(5)(vi) of the IT Act 1961, vide Provisional Approval No. AAAAR1077PF20214, dated 28-05-2021. Our PAN: AAAAR1077P. Under Schedule I, Article 53, Exemption (b) of the Indian Stamp Act, Charitable Institutions are not required to issue any stamped receipt for amounts received by them.";



	if ($det['section_code']=='80G'):
		if (strtotime($det['date'])<strtotime('01-06-2021')):
			$mess="Donations are exempt from Income Tax u/s 80G(5)(vi) of the IT Act 1961, vide order no DIT(E)/848/8E/109/69-70, dated 12-01-2009 which has been further extended in perpetuity by letter no DIT(E)/109/69-70 dated 26-09-2011. Our PAN: AAAAR1077P. Under Schedule I, Article 53, Exemption (b) of the Indian Stamp Act, Charitable Institutions are not required to issue any stamped receipt for amounts received by them.";
		else:
			$mess="Donations are exempt from Income Tax u/s 80G(5)(vi) of the IT Act 1961, vide Provisional Approval No. AAAAR1077PF20214, dated 28-05-2021. Our PAN: AAAAR1077P. Under Schedule I, Article 53, Exemption (b) of the Indian Stamp Act, Charitable Institutions are not required to issue any stamped receipt for amounts received by them.";
		endif;
	else:
		$mess="Our PAN: AAAAR1077P. Under Schedule I, Article 53, Exemption (b) of the Indian Stamp Act, Charitable Institutions are not required to issue any stamped receipt for amounts received by them.";
	endif;


$pdf->ln(1);
$pdf->SetFont('Arial','',10);
$pdf->Multicell(180,5,$mess,0,'L');
//$pdf->cell(180,5,"...",0,0,'C','',site_url('login/home'));
$pdf->Image(base_url(IMGPATH.'home.png'),105,140,5,'','',site_url('login/home'));
$pdf->Image(base_url(IMGPATH.'pen.jpeg'),115,140,5,'','',site_url('receipts/letter/'.$det['id']));
$filename="receipt_".$det['series']."-".$det['sub_series']."-".$det['no'].".pdf";
$pdf->Output($filename,'I');
//redirect (site_url('login/home','refresh'));
//$pdf->close();
//copy($filename,'http://192.168.1.244/home/freak/Public/receipt.pdf');

*/
?>





