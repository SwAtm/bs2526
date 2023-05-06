<?php
tfpdf();
$c=$e=0;
foreach ($details as $dc):
	$c+=count($dc['det']);
endforeach;
//$c+=count($details);
$c+=count($details1);
if ($c>13):
$pdf = new tFPDF('P', 'mm', array(210,296));
//$noofpages = ceil(count($details)/28);
else:
$pdf = new tFPDF('L', 'mm', array(210,148));
endif;
$pdf->setLeftMargin(30);
$pdf->SetAutoPageBreak(false);
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);
//$pdf->Image(base_url(IMGPATH.'logo.jpg'),10,10,15,'');
$pdf->setXY(0,10);
$pdf->Cell(170,5,$this->session->cname.' - '.trim($this->session->loc_name),0,1,'C');
$pdf->ln(2);
$pdf->cell(170,0,'',1,1);
$pdf->ln(5);
$pdf->SetFont('Arial','',8);
$pdf->cell(125,5,ucfirst($rtype). '-wise Report from '.$frdate. ' to '.$todate,0,1,'C');
$pdf->ln(5);
//$pdf->cell(20,5,'Bill No',1,0,'L');
$pdf->cell(20,5,'Date',1,0,'L');
//$pdf->cell(50,5,'Party',1,0,'L');
$pdf->cell(15,5,'Books',1,0,'L');
$pdf->cell(15,5,'Articles',1,0,'L');
$pdf->cell(15,5,'Expenses',1,0,'L');
$pdf->cell(15,5,'CGST',1,0,'L');
$pdf->cell(15,5,'SGST',1,0,'L');
$pdf->cell(15,5,'IGST',1,0,'L');
$pdf->cell(15,5,'TOTAL',1,1,'L');
$totalbooks=$totalart=$totalexp=$totalcgst=$totalsgst=$totaligst=$totalamount=0;
foreach ($details as $d):
$bamount=$ramount=$expenses=$cgst=$sgst=$igst=$amount=0;
$pdf->cell(125,5, $d['name'],0,1,'C');
	foreach ($d['det'] as $de):
		$pdf->cell(20,5,$de['date'],1,0,'L');
		$pdf->cell(15,5,number_format($de['tbamount'],2,'.',','),1,0,'R');
		$pdf->cell(15,5,number_format($de['tramount'],2,'.',','),1,0,'R');
		$pdf->cell(15,5,number_format($de['texpenses'],2,'.',','),1,0,'R');
		$pdf->cell(15,5,number_format($de['tcgst'],2,'.',','),1,0,'R');
		$pdf->cell(15,5,number_format($de['tsgst'],2,'.',','),1,0,'R');
		$pdf->cell(15,5,number_format($de['tigst'],2,'.',','),1,0,'R');
		$damount=$de['tbamount']+$de['tramount']+$de['texpenses']+$de['tcgst']+$de['tsgst']+$de['tigst'];
		$pdf->cell(15,5,number_format($damount,2,'.',','),1,1,'R');
		$bamount+=$de['tbamount'];
		$ramount+=$de['tramount'];
		$expenses+=$de['texpenses'];
		$cgst+=$de['tcgst'];
		$sgst+=$de['tsgst'];
		$igst+=$de['tigst'];
		$amount+=$damount;
		//$pdf->cell(15,5,number_format($de['tigst'],2,'.',','),1,1,'R');
		if ($pdf->getY()>266):
		//$pdf->cell(15,5,$pdf->getY(),1,1,'R');
			$pdf->AddPage();
			$pdf->cell(20,5,'Date',1,0,'L');
			$pdf->cell(15,5,'Books',1,0,'L');
			$pdf->cell(15,5,'Articles',1,0,'L');
			$pdf->cell(15,5,'Expenses',1,0,'L');
			$pdf->cell(15,5,'CGST',1,0,'L');
			$pdf->cell(15,5,'SGST',1,0,'L');
			$pdf->cell(15,5,'IGST',1,0,'L');
			$pdf->cell(15,5,'TOTAL',1,1,'L');
		endif;
	endforeach;
	$pdf->cell(20,5,'Total',1,0,'C');
	$pdf->cell(15,5,number_format($bamount,2,'.',','),1,0,'R');
	$pdf->cell(15,5,number_format($ramount,2,'.',','),1,0,'R');
	$pdf->cell(15,5,number_format($expenses,2,'.',','),1,0,'R');
	$pdf->cell(15,5,number_format($cgst,2,'.',','),1,0,'R');
	$pdf->cell(15,5,number_format($sgst,2,'.',','),1,0,'R');
	$pdf->cell(15,5,number_format($igst,2,'.',','),1,0,'R');
	$pdf->cell(15,5,number_format($amount,2,'.',','),1,1,'R');
	$totalbooks+=$bamount;
	$totalart+=$ramount;
	$totalexp+=$expenses;
	$totalcgst+=$cgst;
	$totalsgst+=$sgst;
	$totaligst+=$igst;
	$totalamount+=$amount;
	if ($pdf->getY()>266):
		$pdf->AddPage();
	endif;
endforeach;
	$pdf->ln(5);
	/*$pdf->cell(20,5,'GrandTotal',1,0,'C');
	$pdf->cell(15,5,number_format($totalbooks,2,'.',','),1,0,'R');
	$pdf->cell(15,5,number_format($totalart,2,'.',','),1,0,'R');
	$pdf->cell(15,5,number_format($totalexp,2,'.',','),1,0,'R');
	$pdf->cell(15,5,number_format($totalcgst,2,'.',','),1,0,'R');
	$pdf->cell(15,5,number_format($totalsgst,2,'.',','),1,0,'R');
	$pdf->cell(15,5,number_format($totaligst,2,'.',','),1,0,'R');
	$pdf->cell(15,5,number_format($totalamount,2,'.',','),1,1,'R');
	$pdf->ln(5);*/
	//$pdf->cell(15,5,$e,1,1,'R');
	//$pdf->cell(15,5,$c,1,1,'R');
	$pdf->cell(155,5,'Summary',1,1,'C');
	$pdf->cell(20,5,'Date',1,0,'L');
	$pdf->cell(15,5,'Books',1,0,'L');
	$pdf->cell(15,5,'Articles',1,0,'L');
	$pdf->cell(15,5,'Expenses',1,0,'L');
	$pdf->cell(15,5,'CGST',1,0,'L');
	$pdf->cell(15,5,'SGST',1,0,'L');
	$pdf->cell(15,5,'IGST',1,0,'L');
	$pdf->cell(15,5,'TOTAL',1,0,'L');
	$pdf->cell(15,5,'CASH',1,0,'L');
	$pdf->cell(15,5,'UPI',1,1,'L');
foreach ($details1 as $dt):
		$pdf->cell(20,5,$dt['date'],1,0,'L');
		$pdf->cell(15,5,number_format($dt['tbamount'],2,'.',','),1,0,'R');
		$pdf->cell(15,5,number_format($dt['tramount'],2,'.',','),1,0,'R');
		$pdf->cell(15,5,number_format($dt['texpenses'],2,'.',','),1,0,'R');
		$pdf->cell(15,5,number_format($dt['tcgst'],2,'.',','),1,0,'R');
		$pdf->cell(15,5,number_format($dt['tsgst'],2,'.',','),1,0,'R');
		$pdf->cell(15,5,number_format($dt['tigst'],2,'.',','),1,0,'R');
		$damount=$dt['tbamount']+$dt['tramount']+$dt['texpenses']+$dt['tcgst']+$dt['tsgst']+$dt['tigst'];
		$pdf->cell(15,5,number_format($damount,2,'.',','),1,0,'R');
		$pdf->cell(15,5,number_format($dt['cash']+$dt['cashexp'],2,'.',','),1,0,'R');
		$pdf->cell(15,5,number_format($dt['upi']+$dt['upiexp'],2,'.',','),1,1,'R');
		
		if ($pdf->getY()>266):
		//$pdf->cell(15,5,$pdf->getY(),1,1,'R');
		$pdf->AddPage();
			$pdf->cell(155,5,'Summary',1,1,'C');
			$pdf->cell(20,5,'Date',1,0,'L');
			$pdf->cell(15,5,'Books',1,0,'L');
			$pdf->cell(15,5,'Articles',1,0,'L');
			$pdf->cell(15,5,'Expenses',1,0,'L');
			$pdf->cell(15,5,'CGST',1,0,'L');
			$pdf->cell(15,5,'SGST',1,0,'L');
			$pdf->cell(15,5,'IGST',1,0,'L');
			$pdf->cell(15,5,'TOTAL',1,0,'L');
			$pdf->cell(15,5,'CASH',1,0,'L');
			$pdf->cell(15,5,'UPI',1,1,'L');
		endif;
endforeach;

if ($c>13):
$pdf->Image(base_url(IMGPATH.'home.png'),105,290,5,'','',site_url('welcome/home'));
else:

$pdf->Image(base_url(IMGPATH.'home.png'),105,140,5,'','',site_url('welcome/home'));

endif;
		
$pdf->output();




echo "<pre>";
print_r($details);
echo "</pre>";

?>
