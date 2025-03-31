<?php
tfpdf();
$amt = preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $det['amount']);
$ntw = new \NTWIndia\NTWIndia();
$pdf = new tFPDF('P', 'mm', array(210,148));
$pdf->setLeftMargin(20);
$pdf->SetAutoPageBreak(false);
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);
$pdf->Image(base_url(IMGPATH.'logo.jpg'),20,10,15,'');
//$pdf->setXY(5,10);
$pdf->Cell(128,5,'Ramakrishna Mission Ashrama',0,1,'C');
$pdf->SetFont('Arial','',10);
$pdf->Cell(128,5,'Fort, Belgaum, Karnataka - 590016',0,1,'C');
$pdf->Cell(128,5,'Ph: 0831 2432789 / 2970320 / 321 :: email: belgaum@rkmm.org',0,1,'C');
$pdf->ln(2);
$pdf->cell(118,0,'',1,1);
$pdf->ln(5);
$pdf->SetFont('Arial','B',14);
$pdf->cell(128,5,'JOURNAL VOUCHER',0,1,'C');
$pdf->ln(3);
$pdf->SetFont('Arial','',14);
$pdf->cell(60,5,'Journal Voucher: '.$det['series']."-",0,0,'L');
$pdf->cell(60,5,'Date: '.date('d-m-Y',strtotime($det['date'])),0,1,'R');
$pdf->ln(3);
$pdf->SetFont('Arial','',13);
//$pdf->cell(57,5,'Received with thanks from:',0,1,'L');
//$pdf->SetFont('Times','',13);
//$pdf->Multicell(180,6,ucwords(strtolower($det['name'].', '.$det['address'])),0,'L');
$pdf->Multicell(120,7,'Debit: '. ucwords(strtolower($det['name'].', '.$det['add1'])),0,'L');
$pdf->ln(1);
$pdf->Cell(120,5,$det['city'].' - '.$det['pin'],0,1,'L');

$pdf->ln(3);
$pdf->Multicell(120,7,'Credit: ',0,'L');
$pdf->ln(3);
//$pdf->Cell(90,5,($det['id_name']!=='NOT AVAILABLE'?$det['id_name'].': '.$det['id_no']:''),0,1,'R');
$pdf->ln(3);
$pdf->SetFont('Arial','I',13);
$pdf->cell(120,5,'A sum of Rupees:',0,1,'L');
$pdf->ln(1);
$pdf->SetFont('Times','',13);
$pdf->Multicell(118,5,$ntw->numToWord($det['amount']).' Only',0,'L');
$pdf->ln(3);
$pdf->SetFont('Arial','I',13);
$pdf->cell(20,5,"Being: ", 0,0,'L');
$pdf->SetFont('Times','',13);
$pdf->cell(118,5,ucfirst($det['remark']),0,1,'L');
$pdf->ln(3);
$pdf->SetFont('Arial','',13);
$pdf->cell(12,5,"Vide: ",0,0,'L');
$pdf->SetFont('Times','',13);
$pdf->Multicell(118,5,($det['mop']=="Cash"?$det['mop']:ucfirst($det['mop']). ": ").($det['chno']!==''?"No: ".$det['chno']:'')." ".(($det['chdate']!=='' and $det['chdate']!=='0000-00-00')?'Dt: '.$det['chdate']:'')." ".ucfirst($det['remark']),0,'L');
//$pdf->Multicell(180,5,"Vide: ".($det['mode_payment']=="Cash"?$det['mode_payment']:$det['mode_payment']. ": ").($det['ch_no']!==''?"No: ".$det['ch_no']:'')." ".($det['tr_date']!==''?'Dt: '.$det['tr_date']:'')." ".$det['pmt_details'],0,'L');
//$pdf->Image(base_url(IMGPATH.'Signature.jpg'), 120,103);
$pdf->Image(base_url(IMGPATH.'rupee.png'),20,110,5,'');
$pdf->SetFont('Arial','',14);
$pdf->setXY(24,110);
$pdf->cell(118,5,$amt,0,1,'L');
//$pdf->SetFont('Arial','',12);
//$pdf->cell(78,5,'Collected By',0,0,'L');

$pdf->ln(50);
$pdf->cell(118,5,'Secretary',0,1,'R');
$pdf->ln(1);
//$pdf->cell(128,0,'',1,1);
$pdf->Image(base_url(IMGPATH.'home.png'),74,200,5,'','',site_url('welcome/home'));
//$pdf->Image(base_url(IMGPATH.'pen.jpeg'),115,140,5,'','',site_url('receipts/letter/'.$det['id']));
$filename="receipt_".$det['series']."-".$det['no'].".pdf";
$pdf->Output($filename,'I');

?>





