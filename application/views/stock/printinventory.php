<?php

tfpdf();

$pdf = new tFPDF('P', 'mm', array(210,296));
//$noofpages = ceil(count($details)/28);

$pdf->setLeftMargin(10);
//$pdf->SetAutoPageBreak(false);
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);
//$pdf->Image(base_url(IMGPATH.'logo.jpg'),10,10,15,'');
$pdf->setXY(0,10);
$pdf->Cell(190,5,$this->session->cname.' - '.$this->session->loc_name,0,1,'C');
$pdf->ln(2);
$pdf->cell(190,0,'',1,1);
$pdf->ln(5);
$pdf->SetFont('Arial','',8);
$pdf->cell(190,5,'Inventory',0,1,'C');
$pdf->ln(5);
$pdf->cell(15,5,'Inv Id',1,0,'L');
$pdf->cell(20,5,'Code',1,0,'L');
$pdf->cell(20,5,'Rate',1,0,'L');
$pdf->cell(50,5,'Title',1,0,'L');
$pdf->cell(15,5,'Cl Balance',1,0,'L');
$pdf->cell(15,5,'Stock',1,0,'L');
$pdf->cell(30,5,'Diff Qty',1,0,'L');
$pdf->cell(30,5,'Diff Amt',1,1,'L');
$diffamt = 0;
foreach ($inventory as $d):
$pdf->cell(15,5,$d['id'],1,0,'L');
$pdf->cell(20,5, $d['code'],1,0,'L');
$pdf->cell(20,5,$d['rate'],1,0,'L');
$pdf->cell(50,5,$d['title'],1,0,'L');
$pdf->cell(15,5,$d['clbal'],1,0,'L');
$pdf->cell(15,5,$d['stock'],1,0,'L');
$pdf->cell(30,5,$d['clbal']-$d['stock'],1,0,'L');	
$pdf->cell(30,5,($d['clbal']-$d['stock'])*$d['rate'],1,1,'L');	
$diffamt+=($d['clbal']-$d['stock'])*$d['rate'];
endforeach;
$pdf->cell(15,5,$diffamt,1,0,'L');	
$pdf->Image(base_url(IMGPATH.'home.png'),105,290,5,'','',site_url('welcome/home'));

$pdf->output();

/*
echo "<pre>";
print_r($details);


echo "</pre>";
*/


?>
