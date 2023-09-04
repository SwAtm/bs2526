<?php
tfpdf();
$pdf = new tFPDF('P', 'mm', array(210,296));
$pdf->setLeftMargin(10);
$pdf->SetAutoPageBreak(false);
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);
$pdf->cell(190, 5, 'Closing Balance as on: '.Date('d-m-Y').' at '.$this->session->loc_name, 0,1);
$i=1;
$pdf->SetFont('Arial','',12);
$pdf->Cell(15,7,'Code',1,0,'C');
$pdf->Cell(100,7,'Item',1,0,'C');
$pdf->Cell(20,7,'Rate',1,0,'C');
$pdf->Cell(20,7,'Cl Bal',1,0,'C');
$pdf->Cell(30,7,'',1,1,'C');
foreach ($stock as $st):
$pdf->Cell(15,7,$st['id'],1,0,'C');
$pdf->Cell(100,7,substr($st['title'],0,35),1,0,'L');
$pdf->Cell(20,7,number_format($st['rate'],2,'.',''),1,0,'C');
$pdf->Cell(20,7,$st['clbal'],1,0,'C');
$pdf->Cell(30,7,'',1,1,'C');

if ($pdf->GetY()>=270):
$pdf->ln(5);
$pdf->Cell(190,5,'Page '.$i,0,1,'C');
$i++;
$pdf->AddPage();
$pdf->Cell(15,5,'Code',1,0,'C');
$pdf->Cell(100,5,'Item',1,0,'C');
$pdf->Cell(20,5,'Rate',1,0,'C');
$pdf->Cell(20,5,'Cl Bal',1,0,'C');
$pdf->Cell(30,5,'',1,1,'C');
endif;

endforeach;
$pdf->SetY(266);
$pdf->Cell(190,5,'Page '.$i,0,1,'C');
$pdf->Image(base_url(IMGPATH.'home.png'),105,276,5,'','',site_url('welcome/home'));
$pdf->output();




?>
