<?php
tfpdf();
if ((count($ledger)>16) ):
$pdf = new tFPDF('P', 'mm', array(210,296));
$y = 266;
else:
$pdf = new tFPDF('L', 'mm', array(210,148));
$y = 118;
endif;
$i=1;
$pdf->setLeftMargin(10);
$pdf->SetAutoPageBreak(false);
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);
$pdf->Image(base_url(IMGPATH.'logo.jpg'),10,10,15,'');
$pdf->setXY(0,10);
$pdf->Cell(190,5,$this->session->cname.' - '.$location,0,1,'C');
$pdf->SetFont('Arial','',14);
$pdf->Cell(190,5,$this->session->caddress.', '.$this->session->ccity,0,1,'C');
$pdf->SetFont('Arial','',12);
$pdf->Cell(190,5,'Ph: '.$this->session->cphone.' :: email: '.$this->session->cemail,0,1,'C');
$pdf->ln(2);
$pdf->cell(190,0,'',1,1);
$pdf->ln(5);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(190,5,'Party Ledger for '.$party['name'].' - '.$party['city'],0,1,'C');
$pdf->Cell(25,5,'Date',1,0,'C');
$pdf->Cell(45,5,'Details',1,0,'C');
$pdf->Cell(25,5,'Debit',1,0,'C');
$pdf->Cell(25,5,'Credit',1,0,'C');
$pdf->Cell(30,5,'Balance',1,0,'C');
$pdf->Cell(40,5,'Remark',1,1,'C');
$pdf->SetFont('Arial','',12);
foreach ($ledger as $led):
$pdf->Cell(25,5,$led['date'],1,0,'C');
$pdf->Cell(45,5,ucfirst($led['doc']),1,0,'L');
$pdf->Cell(25,5,($led['debit']==0?'':$led['debit']),1,0,'R');
$pdf->Cell(25,5,($led['credit']==0?'':$led['credit']),1,0,'R');
$pdf->Cell(30,5,number_format($led['balance'],2),1,0,'R');
$pdf->Cell(40,5,$led['remark'],1,1,'L');

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
$pdf->SetFont('Arial','B',12);
$pdf->Cell(190,5,'Party Ledger for '.$party['name'].' - '.$party['city'],0,1,'C');
$pdf->Cell(25,5,'Date',1,0,'C');
$pdf->Cell(45,5,'Details',1,0,'C');
$pdf->Cell(25,5,'Debit',1,0,'C');
$pdf->Cell(25,5,'Credit',1,0,'C');
$pdf->Cell(30,5,'Balance',1,0,'C');
$pdf->Cell(40,5,'Remark',1,1,'C');
$pdf->SetFont('Arial','',12);
endif;
endforeach;
$pdf->Image(base_url(IMGPATH.'home.png'),85,$y+10,5,'','',site_url('welcome/home'));
$pdf->Image(base_url(IMGPATH.'list.png'),105,$y+10,5,'','',site_url('party_trans/ledger'));

$pdf->Output();
?>
