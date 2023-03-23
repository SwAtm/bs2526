<?php
require('tfpdf.php');

class PDF_MC_Table extends tFPDF
{
var $widths;
var $aligns;
//me
var $pageadd;
var $hrow;


function SetHdr($header)
{
$this->hrow=$header;
}

//em
function SetWidths($w)
{
	//Set the array of column widths
	$this->widths=$w;
}

function SetAligns($a)
{
	//Set the array of column alignments
	$this->aligns=$a;
}

function Row($data)
{
	//Calculate the height of the row
	$nb=0;
	for($i=0;$i<count($data);$i++)
		$nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
	$h=5*$nb;
	//Issue a page break first if needed
	$this->CheckPageBreak($h);
	//Draw the cells of the row
	for($i=0;$i<count($data);$i++)
	{
		$w=$this->widths[$i];
		$a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
		//Save the current position
		$x=$this->GetX();
		$y=$this->GetY();
		//Draw the border
		$this->Rect($x,$y,$w,$h);
		//Print the text
		$this->MultiCell($w,5,$data[$i],0,$a);
		//Put the position to the right of the cell
		$this->SetXY($x+$w,$y);
	}
	//Go to the next line
	$this->Ln($h);
	
	//$this->Cell(210,5,$this->PageNo(),0,0,'C');

}

function CheckPageBreak($h)
{
	//If the height h would cause an overflow, add a new page immediately. 
	//me Added +5, pageno()
		//me
		$this->pageadd=0;
		//em
		if($this->GetY()+$h+5>$this->PageBreakTrigger):
		//me
		$this->Cell(210,5,$this->PageNo(),0,1,'C');
		//em
		$this->AddPage($this->CurOrientation);
		//me
		for($i=0;$i<count($this->hrow);$i++)
		{
		$w=$this->widths[$i];
		$t=$this->hrow[$i];
		$j=$i;
		if (count($this->hrow)-1==$j):
		$this->Cell($w,5,$t,1,1);
		else:
		$this->Cell($w,5,$t,1,0);
		endif;
		//em
		}
	
		endif;
}

function NbLines($w,$txt)
{
	//Computes the number of lines a MultiCell of width w will take
	$cw=&$this->CurrentFont['cw'];
	if($w==0)
		$w=$this->w-$this->rMargin-$this->x;
	$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
	$s=str_replace("\r",'',$txt);
	$nb=strlen($s);
	if($nb>0 and $s[$nb-1]=="\n")
		$nb--;
	$sep=-1;
	$i=0;
	$j=0;
	$l=0;
	$nl=1;
	while($i<$nb)
	{
		$c=$s[$i];
		if($c=="\n")
		{
			$i++;
			$sep=-1;
			$j=$i;
			$l=0;
			$nl++;
			continue;
		}
		if($c==' ')
			$sep=$i;
		$l+=$cw[$c];
		if($l>$wmax)
		{
			if($sep==-1)
			{
				if($i==$j)
					$i++;
			}
			else
				$i=$sep+1;
			$sep=-1;
			$j=$i;
			$l=0;
			$nl++;
		}
		else
			$i++;
	}
	return $nl;
}
}
?>
