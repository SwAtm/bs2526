<?php
/*
echo "<pre>";
echo $frdate;
echo $todate;
print_r($discountreport);
print_r($profit);
echo "</pre>";
*/


tfpdf();
$i=1;
$pdf = new tFPDF('P', 'mm', array(210,296));
$pdf->setLeftMargin(10);
$pdf->SetAutoPageBreak(false);
foreach ($discountreport as $k=>$v):
if ($k!='Railway Station'):
continue;
endif;
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);
$pdf->Image(base_url(IMGPATH.'logo.jpg'),20,10,15,'');
//$pdf->setXY(10,10);
$pdf->setLeftMargin(20);
$pdf->Cell(180,5,$this->session->cname,0,1,'C');
$pdf->SetFont('Arial','',12);
$pdf->Cell(180,5,$this->session->cname.' :: Ph: '.$this->session->cphone.,0,1,'C');
$pdf->SetFont('Arial','',12);
$pdf->ln(5);
$tnetsales=$tprofit=0;
$pdf->Cell(180,5,'Sales report for '.$k. ' for the period from '.date('d-m-Y', strtotime($frdate)).' to '.date('d-m-Y', strtotime($todate)),0,1,'C');
$pdf->ln(2);
$pdf->SetFont('Arial','',10);
$pdf->cell(180,0,'',1,1);
$pdf->ln(5);
//$pdf->Cell(25,5,'Bill No',1,0,'C');
//$pdf->Cell(20,5,'Date',1,0,'C');
$pdf->Cell(50,5,'Item',1,0,'C');
$pdf->Cell(20,5,'Rate',1,0,'C');
$pdf->Cell(20,5,'Qty',1,0,'C');
//$pdf->Cell(15,5,'Disc',1,0,'C');
//$pdf->Cell(15,5,'Cash_D',1,0,'C');
$pdf->Cell(20,5,'Total',1,0,'C');
$pdf->Cell(20,5,'Discount',1,0,'C');
$pdf->Cell(20,5,'Discount %',1,0,'C');
$pdf->Cell(25,5,'',1,1,'C');

	foreach ($v as $key):
	//$pdf->Cell(25,5,$key['series'].' - '.$key['no'],1,0,'C');
	//$pdf->Cell(20,5,date('d-m-Y', strtotime($key['date'])),1,0,'C');
	$pdf->Cell(50,5,substr($key['title'],0,20),1,0,'C');
	$pdf->Cell(20,5,$key['rate'],1,0,'C');
	$pdf->Cell(20,5,$key['quantity'],1,0,'C');
	//$pdf->Cell(15,5,$key['discount'],1,0,'C');
	//$pdf->Cell(15,5,$key['cash_disc'],1,0,'C');
	$pdf->Cell(20,5,number_format($key['netsales'],2,'.',','),1,0,'C');
	$pdf->Cell(20,5,number_format($key['profit'],2,'.',','),1,0,'C');
	$pdf->Cell(20,5,number_format($key['profitpt'],2,'.',','),1,0,'C');
	$pdf->Cell(25,5,'',1,1,'C');
	$tnetsales+=$key['netsales'];
	$tprofit+=$key['profit'];
		if ($pdf->GetY()>=270):
			$pdf->ln(5);
			$pdf->Cell(190,5,'Page '.$i,0,1,'C');
			$i++;
			$pdf->AddPage();
			$pdf->Cell(190,5,'Sales report for '.$k. ' for the period from '.date('d-m-Y', strtotime($frdate)).' to '.date('d-m-Y', strtotime($todate)),0,1,'C');
			$pdf->ln(2);
			$pdf->cell(190,0,'',1,1);
			$pdf->ln(5);
			//$pdf->Cell(25,5,'Bill No',1,0,'C');
			//$pdf->Cell(20,5,'Date',1,0,'C');
			$pdf->Cell(50,5,'Item',1,0,'C');
			$pdf->Cell(20,5,'Rate',1,0,'C');
			$pdf->Cell(20,5,'Qty',1,0,'C');
			//$pdf->Cell(15,5,'Disc',1,0,'C');
			//$pdf->Cell(15,5,'Cash_D',1,0,'C');
			$pdf->Cell(20,5,'Total',1,0,'C');
			$pdf->Cell(20,5,'Discount',1,0,'C');
			$pdf->Cell(20,5,'Discount %',1,0,'C');
			$pdf->Cell(25,5,'',1,1,'C');
		endif;
	endforeach;
$pdf->cell(90, 5, 'Total', 1,0,'C');
$pdf->cell(20, 5, number_format($tnetsales,2,'.',','), 1,0,'C');
$pdf->cell(20, 5, number_format($tprofit,2,'.',','), 1,0,'C');
$pdf->Cell(20,5,'',1,0,'C');
$pdf->Cell(25,5,'',1,1,'C');
$pdf->SetY(266);
$pdf->Cell(190,5,'Page '.$i,0,1,'C');
$i++;
//$pdf->ln(5);
$pdf->AddPage('L', 'A5');
$pdf->setLeftMargin(45);
$pdf->cell(100,10,'Sales Summary - '.$k.' From '.date('d-m-Y', strtotime($frdate)).' to '.date('d-m-Y', strtotime($todate)),0,1,'C');
$pdf->cell(50,10,'Total Sales',1,0,'C');
$pdf->cell(50,10,number_format($tnetsales,2,'.',','),1,1,'R');
$pdf->cell(50,10,'Discount',1,0,'C');
$pdf->cell(50,10,number_format($tprofit,2,'.',','),1,1,'R');
$pdf->cell(50,10,'',1,0,'C');
$pdf->cell(50,10,'',1,1,'R');
$pdf->cell(50,10,'',1,0,'C');
$pdf->cell(50,10,'',1,1,'R');
$pdf->cell(50,10,'',1,0,'C');
$pdf->cell(50,10,'',1,1,'R');
$pdf->cell(50,10,'',1,0,'C');
$pdf->cell(50,10,'',1,1,'R');
//$tnetsales=$tprofit=0;
endforeach;
$pdf->ln(5);
foreach ($profit as $p):
$pdf->Cell(60,5,$p,0,0);
endforeach;
$pdf->ln(10);
$pdf->Image(base_url(IMGPATH.'home.png'),105,null,5,'','',site_url('welcome/home'));
//$pdf->Cell(135,5,'Home',0, 0, 'C',0, site_url('welcome/home'));
$pdf->output();

?>
