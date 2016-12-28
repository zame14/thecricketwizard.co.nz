<?php
define('FPDF_FONTPATH','font/');


function prepare_receipt($data, $display=false){ 
	$debug = 0;

	global $BASEURL;
	$timeStamp = date('d-M-Y');
	
	
	require_once('fpdi_protection.php');
	require_once('fpdi.php');
	
	ob_start();
	$pdf = new FPDI_Protection('P', 'mm', 'A4');

	$pagecount = $pdf->setSourceFile("../PDF/PDF_templates/cw_receipt_template_blank.pdf");

	$tplidx = $pdf->ImportPage(1);
	
	$pdf->addPage();
	$pdf->useTemplate($tplidx, 0, 0);
	//$pdf->SetFont('Arial','B',12);
	
	$pdf->setXY(122, 71);
	$pdf->SetFont('Arial','',12); 
	$pdf->SetTextColor(99,99,99);
	
	$text="The Cricket Wizard\n";
	$text.="67 Pembroke Street\n";
	$text.="New Plymouth 4340\n";
//	$text.="Taranaki\n";
	$text.="NEW ZEALAND\n\n";
	$text.="Receipt Number: ".$data[1]['transactionnum']."\n";
	$text.="Date: ".date("d F Y")."\n\n";

	$pdf->MultiCell(72, 5, $text,0,'R');
	
	$name=$data[1]["firstname"]." ".$data[1]["lastname"];	

	$pdf->SetTextColor(01,171,171);
	$pdf->Text(16, 125,'Customer Details');
	
	$pdf->setXY(15.5, 127);
	$pdf->SetTextColor(99,99,99);
	$text = $name." \n".$data[1]["sponsor"]." \n".$data[1]["website"]." \n".$data[1]["email"];
	$pdf->MultiCell(100, 5, $text);

	/***************
	Payment details
	****************/
	//Ad Option
	$pdf->SetFont('Arial','',11);
	$pdf->setXY(25, 171);
	$pdf->MultiCell(0, 4, $data[1]["name"]);
	
	//duration
	$pdf->setXY(51, 171);
	$pdf->MultiCell(0, 4,$data[1]["duration_text"]);
	
	//unit price
	if($data[1]['duration']==1)
	{
		$fee = $data[1]['fee'];
	}
	else if($data[1]['duration']==3)
	{
		$fee = ($data[1]['fee']*$data[1]['duration'])-$data[1]['fee'];
	}
	else if($data[1]['duration']==6)
	{
		$fee = ($data[1]['fee']*$data[1]['duration'])-($data[1]['fee']*2);
	}
	$pdf->setXY(87, 172);
	$pdf->MultiCell(23, 3,"".number_format($fee, 2),0,'R');	
	
	//discount
	$pdf->setXY(130, 171);
	$pdf->MultiCell(0, 4,"-");
	
	//net price
	$pdf->setXY(160, 172);
	$pdf->MultiCell(23, 3,"".number_format($fee, 2),0,'R');	
	
	//total
	$pdf->SetFont('Arial','B',12);
	$pdf->setXY(160, 184);
	$pdf->MultiCell(23, 3,"".number_format($fee, 2),0,'R');		
	
	$permissionsArray = array("print");
	$pdf->setProtection($permissionsArray, "", "eblsDesignPasswordForROC");

	if($display==true){
		$filename = "../PDF/receipts/receipt_display.pdf";
		print $filename . "<br><br>";
		if(is_file($filename))unlink($filename); //die('after deletion');
		$pdf->Output($filename,"F");
	}else{
		$filename = "../PDF/receipts/receipt_".$data[1]['sponsor']."_".date('Y-m-d').".pdf";
		$pdf->Output($filename,"F");
	}
	$str = ob_get_flush();
	$pdf->closeParsers();
	return $filename;
} // end prepare_receipt
?>