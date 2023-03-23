<?php

tfpdf();
$c=0;
foreach ($details as $dc):
	$c+=count($dc['det']);
endforeach;
$c+=(count($details)*2);
if ($c>20):
$pdf = new tFPDF('P', 'mm', array(210,296));
//$noofpages = ceil(count($details)/28);
else:
$pdf = new tFPDF('L', 'mm', array(210,148));
endif;
$pdf->setLeftMargin(10);
$pdf->SetAutoPageBreak(false);
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);
//$pdf->Image(base_url(IMGPATH.'logo.jpg'),10,10,15,'');
$pdf->setXY(0,10);
$pdf->Cell(190,5,'Ramakrishna Mission Ashrama - '.$this->session->loc_name,0,1,'C');
$pdf->ln(2);
$pdf->cell(190,0,'',1,1);
$pdf->ln(5);
$pdf->SetFont('Arial','',8);
$pdf->cell(190,5,ucfirst($rtype). '-wise Report from '.$frdate. ' to '.$todate,0,1,'C');
$pdf->ln(5);
$pdf->cell(18,5,'Bill No',1,0,'L');
$pdf->cell(18,5,'Date',1,0,'L');
$pdf->cell(54,5,'Party',1,0,'L');
$pdf->cell(15,5,'Books',1,0,'L');
$pdf->cell(15,5,'Articles',1,0,'L');
$pdf->cell(15,5,'Expenses',1,0,'L');
$pdf->cell(15,5,'C/SGST',1,0,'L');
$pdf->cell(15,5,'IGST',1,0,'L');
$pdf->cell(15,5,'Total',1,1,'L');
foreach ($details as $d):
$bamount=$ramount=$expenses=$cgst=$sgst=$igst=$amount=0;
$pdf->cell(190,5, $d['name'],0,1,'C');
	foreach ($d['det'] as $de):
		$pdf->cell(18,5,$de['series'].'-'.$de['no'],1,0,'L');
		$pdf->cell(18,5,$de['date'],1,0,'L');
		$pdf->SetFont('Arial','',6);
		$pdf->cell(54,5,substr($de['name'].'-'.$de['city'],0,35),1,0,'L');
		$pdf->SetFont('Arial','',8);
		$pdf->cell(15,5,number_format($de['bamount'],2,'.',','),1,0,'R');
		$pdf->cell(15,5,number_format($de['ramount'],2,'.',','),1,0,'R');
		$pdf->cell(15,5,number_format($de['expenses'],2,'.',','),1,0,'R');
		$pdf->cell(15,5,number_format($de['cgst'],2,'.',','),1,0,'R');
		//$pdf->cell(15,5,number_format($de['sgst'],2,'.',','),1,0,'R');
		$pdf->cell(15,5,number_format($de['igst'],2,'.',','),1,0,'R');
		$damount=$de['bamount']+$de['ramount']+$de['expenses']+$de['cgst']+$de['sgst']+$de['igst'];
		$pdf->cell(15,5,number_format($damount,2,'.',','),1,1,'R');
		$bamount+=$de['bamount'];
		$ramount+=$de['ramount'];
		$expenses+=$de['expenses'];
		$cgst+=$de['cgst'];
		$sgst+=$de['sgst'];
		$igst+=$de['igst'];
		$amount+=$damount;
		if ($pdf->getY()>266):
		//$pdf->cell(15,5,$pdf->getY(),1,1,'R');
			$pdf->AddPage();
			$pdf->cell(18,5,'Bill No',1,0,'L');
			$pdf->cell(18,5,'Date',1,0,'L');
			$pdf->cell(54,5,'Party',1,0,'L');
			$pdf->cell(15,5,'Books',1,0,'L');
			$pdf->cell(15,5,'Articles',1,0,'L');
			$pdf->cell(15,5,'Expenses',1,0,'L');
			$pdf->cell(15,5,'C/SGST',1,0,'L');
			$pdf->cell(15,5,'IGST',1,0,'L');
			$pdf->cell(15,5,'Total',1,1,'L');
		endif;
	endforeach;
	$pdf->cell(90,5,'Total',1,0,'C');
	$pdf->cell(15,5,number_format($bamount,2,'.',','),1,0,'R');
	$pdf->cell(15,5,number_format($ramount,2,'.',','),1,0,'R');
	$pdf->cell(15,5,number_format($expenses,2,'.',','),1,0,'R');
	$pdf->cell(15,5,number_format($cgst,2,'.',','),1,0,'R');
	//$pdf->cell(15,5,number_format($sgst,2,'.',','),1,0,'R');
	$pdf->cell(15,5,number_format($igst,2,'.',','),1,0,'R');
	$pdf->cell(15,5,number_format($amount,2,'.',','),1,1,'R');
endforeach;
//$pdf->cell(15,5,$c,1,1,'R');

if ($c>20):
$pdf->Image(base_url(IMGPATH.'home.png'),105,290,5,'','',site_url('welcome/home'));
else:
$pdf->Image(base_url(IMGPATH.'home.png'),105,140,5,'','',site_url('welcome/home'));
endif;

$pdf->output();

/*
echo "<pre>";
print_r($details);


echo "</pre>";
*/


?>
