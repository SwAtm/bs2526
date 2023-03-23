<?php
tfpdf();

$pdf = new tFPDF('P', 'mm', array(210,296));
$y = 266;

$i=1;
$pdf->setLeftMargin(10);
$pdf->SetAutoPageBreak(false);
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);
$pdf->Image(base_url(IMGPATH.'logo.jpg'),10,10,15,'');
$pdf->setXY(10,10);
$pdf->Cell(190,5,'Ramakrishna Mission Ashrama',0,1,'C');
$pdf->SetFont('Arial','',14);
$pdf->Cell(190,5,'Fort, Belgaum, Karnataka - 590016',0,1,'C');
$pdf->SetFont('Arial','',12);
$pdf->Cell(190,5,'Ph: 0831 2432789 / 2970320 / 321 :: email: belgaum@rkmm.org',0,1,'C');
$pdf->ln(2);
//$pdf->cell(190,0,'',1,1);
//$pdf->ln(5);
$pdf->cell(190,5, 'Purchase Order No: '.$pos['id'].'  Dated: '.$pos['date'],0,1,'C');
$pdf->cell(190,5, 'To: '.$pos['name'].', '.$pos['city'],0,1,'C');
$pdf->SetFont('Arial','',12);
$pdf->Cell(150,5,'Item',1,0,'C');
$pdf->Cell(20,5,'Rate',1,0,'C');
$pdf->Cell(20,5,'Quantity',1,1,'C');
foreach ($podetails as $pod):
$pdf->Cell(150,5,$pod['title'],1,0,'L');
$pdf->Cell(20,5,$pod['rate'],1,0,'R');
$pdf->Cell(20,5,$pod['quantity'],1,1,'R');

if ($pdf->GetY()>=270):
$pdf->ln(5);
$pdf->Cell(190,5,'Page '.$i,0,1,'C');
$i++;
$pdf->AddPage();
$pdf->Cell(150,5,'Item',1,0,'C');
$pdf->Cell(20,5,'Rate',1,0,'C');
$pdf->Cell(20,5,'Quantity',1,1,'C');
endif;

endforeach;
$pdf->SetY($y);
$pdf->Cell(190,5,'Page '.$i,0,1,'C');
$pdf->Image(base_url(IMGPATH.'home.png'),85,$y+10,5,'','',site_url('welcome/home'));
$pdf->Image(base_url(IMGPATH.'list.png'),105,$y+10,5,'','',site_url('po_summary/summary'));
$pdf->output();


?>
