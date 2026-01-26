<?php
ob_start();
class PickNoteReport extends TCPDF
{
    const SERVICE_LIST_X = 10;
	const SERVICE_LIST_Y = 50;
	const LINE_HEIGHT = 5;
	const LEFT_MARGIN = 20;

	const ROWS_FIRST_PAGE = 1; //23;
	const ROWS_PAGE = 40;

	const START_Y = 70;
	const END_Y = 260;

	private $pdf;
	private $service_list = array();
	private $consignment_list = array();
	protected $diff_array = array();
	private $handling;
  
    private $pageNumber=1;
	private $firstpager=0;
	static $picknoteid;
	public $driver_copy = "";


    private $show_main_header_flag = true;

    /**
     * Generate full pdf
     */
	
	
    public static function buildPDFDocuments($pickNoteId, $manifestIdArray, $driverCopy = "YES")
    {
      
	    $pdfObjectNew = new PickNoteReport('P','mm','A4');
		
		self::$picknoteid = $pickNoteId;

		//$pdfObjectNew->createBookingDocs('', "../images/");
      	if(count($manifestIdArray) > 0)
			{
			//ManifestSummaryReport::$manifestid= $mid;
			$pdfObjectNew->addSummaryAccount($pickNoteId, $manifestIdArray, self::START_Y, true);
			
			if($driverCopy == "YES")
			{
				$pdfObjectNew->AddPage();
				$pdfObjectNew->addSummaryAccount($pickNoteId, $manifestIdArray, self::START_Y, true, $driverCopy);
			}
			
			// Output PDF
			$filename = "Delivery-Note.pdf";
			$fileNameNew	=	"../_assets/manifest/". $filename;
			 
			$pickUp = new PickUp($pickNoteId);
			$pickUp->setPickUpPDF($fileNameNew);
			$pickUp->save();
			 
			$pdfObjectNew->Output($fileNameNew, 'F');
			/*if($mid != "")
			{
			 	$manifest = new Manifest($mid);
			 	$manifest->setPdfFile($fileNameNew);
			 	$manifest->Save();
			}*/
			//return $filename;
			return $pdfObjectNew->Output($fileNameNew, 'I');
		
		}
		
		
    }
	
	 public static function SavePDFFile($pickNoteId, $manifestIdArray, $type, $driverCopy = "YES")
    {
		
		$pdfObjectNew = new PickNoteReport('P','mm','A4');
		
		self::$picknoteid = $pickNoteId;

		//$pdfObjectNew->createBookingDocs('', "../images/");		

		
      	if(count($manifestIdArray) > 0 || $pickNoteId > 0)
		{

			if(is_array($manifestIdArray))			
				$pdfObjectNew->addSummaryAccount($pickNoteId, $manifestIdArray, self::START_Y, true, $type);
			else
				$pdfObjectNew->addSummaryAccount($pickNoteId, array($manifestIdArray), self::START_Y, true, $type);	
			
			if($driverCopy == "YES")
			{
				$pdfObjectNew->addSummaryAccount($pickNoteId, $manifestIdArray, self::START_Y, true, $type, $driverCopy);
			}

			
			// Output PDF
			$filename = "PickNoteReport-".$pickNoteId.".pdf";
			$fileNameNew	=	"../_assets/manifest/". $filename; 
			$pdfObjectNew->Output($fileNameNew, 'F');
			return $fileNameNew;
			//return $filename;
			
		
		}
		
		
    }
	
	

    

   	private function addSummaryAccount($pickNoteId, $manifestIdArray, $y, $new_page = true, $type, $driverCopy) 
	{
		
		
		$user = Sessionmanager::getUser();	
		if($driverCopy == "YES")
			$this->Text(10,25,"Driver Copy", false, false, true, 0, 0, 'C');	
			
		
		//if (sizeof($consignmentlist) > 0 )
		if (sizeof($manifestIdArray) > 0)
		{
			
		
		$i = 0;
		
		
		//echo $this->firstpager; exit;
		if($this->firstpager == 0) 
		{
		
			
			$this->pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
			$style = array(
			'position' => '',
			'align' => 'C',
			'stretch' => false,
			'fitwidth' => true,
			'cellfitalign' => '',
			'border' => true,
			'hpadding' => 'auto',
			'vpadding' => 'auto',
			'fgcolor' => array(0,0,0),
			'bgcolor' => false, //array(255,255,255),
			'text' => false,
			'font' => 'helvetica',
			'fontsize' => 8,		
			'stretchtext' => 4		
		);
		
		
          	$this->firstpager++;   
			$this->SetAutoPageBreak(false);
			$lastnumber = 'loser';	
			$this->SetFont('Arial','B',12);
		
			} else {
			//$this->AddPage();
			$this->SetFont('Arial','B',12);
		
			$this->Ln(10);
			}
			
			$x = $this->GetX();
			
		
			$servicename = "";
			$total_number_pieces = 0;	
			$total_weight = 0;	
			
			

			
			//echo count($manifestIdArray);
			//die;
			
			$this->AddPage();
			$i = 0;
			
			// $x = $this->GetX();
			 //$y = $this->GetY();

			$licence_number = '';
			$driverName = '';
			
			foreach ($manifestIdArray as $mid)
			{	
			
				$m = new Manifest($mid);
				
				$licence_number = $m->getLicenceNumber();
				$driverName = $m->getNameOfDriver();
				
		
			//$servicename = new servicefilter();
			//$servicename->addSCodeFilter($c->getHandling());
			//$sname= $servicename->getColumnList('');
			
			

		
		
				$number="";
				
				
				//$y = $this->GetY();	
			
			//$this->setFont("helvetica", "L", 7);
			
			//$this->AddFont('DejaVu','','DroidSansFallback.ttf',true);
			
			//$this->SetFont('DejaVu','',8); 
			
			
			
				$this->SetFont('Arial','',6);		
			
			
			
   			
			//$this->MultiCell(30,3.6,"Hawb:" .@$c->getHawb()."\nTracking:".@$c->getAwb()."\n",0,'L');	///////////
			
		//print_r($c);	
			
		//if($c->getServiceType() != $servicename)
		{
			//echo $servicename . "==" . $c->getServiceType() . "<bR>";
			
			
			if($i == 10)
				$this->AddPage();
				
			$i++;	
			
			//$i = 0;
			
			
			 //print_r($m);
			 //echo count($manifestIdArray);		
			 //die;

			
			
			/*if($c->getServiceType() == 'Asendia UK')
				$this->Image('../images/ascendia.gif',10,10,53,20);
			else
				$this->Image('../images/logo.jpg',6,10,53,20);*/
			
			 $this->SetFont('Arial','',10);
			 $this->Ln(10);
			 $number="";
			 
				
				
			 $servicename = $m->getProduct(); 

				
			 $total_number_pieces += $m->getPieces();
			 $total_weight += $m->getWeight();		 
			
			 //$this->MultiCell(30,3.6,"Hawb:" .@$m->getId()."\nTracking:".@$m->getId()."\n" . "Reference:" . @$m->getId() ,0,'L');
			 
			  $x = $this->GetX() + 6;
			 
			 if($this->GetY() > self::START_Y)
			 	$y = $this->GetY() ;

			 
			  $this->SetXY($x, $y);
			  
			  $stylem = array(
				'position' => '',
				'align' => 'C',
				'text' => true,
				//'font' => 'helvetica',
				//'fontsize' => 8,
				//'stretchtext' => 4
			);
			  
			  
			  $this->write1DBarcode($m->getId(), 'C128', $this->GetX() + 2 , $this->GetY()+7, 50, 7, 0.3, 
			  						$stylem, 'N');
			  
        	
			  $current_y = $this->GetY();
           $current_x = $this->GetX();	  
			  
			  $this->SetXY($current_x + 40, $y);	 
           $current_y = $this->GetY();
           $current_x = $this->GetX();			  

			   
           $this->MultiCell(40, 3.6, $m->getAccount(),0,'C', false, 0, $current_x, $current_y + 7);
			
           $this->SetXY($current_x + 40, $current_y);
           $current_x = $this->GetX();
			  $current_y = $this->GetY();
			  
			  
			  $this->MultiCell(45,3.6,$m->getProduct() ,0,'C', false, 0, $current_x, $current_y + 7);
			  
			  $multiCellHeight1 = $this->GetY() - $current_y;
			  $this->SetXY($current_x + 45, $current_y);
           $current_x = $this->GetX();
			  $current_y = $this->GetY();
			  
 			  
			  $this->MultiCell(35,3.6,$m->getPieces(),0,'C', false, 0, $current_x, $current_y + 7);
			  
  			  $this->SetXY($current_x + 35, $current_y);
           $current_x = $this->GetX();
			  $current_y = $this->GetY();
             
           $this->MultiCell(30,3.6,number_format($m->getWeight(),2) . " kg",0,'C', false, 1, $current_x, $current_y + 7);
			  
			  $this->SetXY($this->GetX(), $current_y+5);
			  $current_x = $this->GetX();
			  $current_y = $this->GetY();
			
			  /*$this->MultiCell(50,3.6,"Pieces:".$m->getId()."\n"."Description of Goods:"."\n"
			  					.$m->getId()."\n"."Value for Customs: ". $m->getId()." ".$m->getId()."\n"."Date: ". 
								$date."\n"."Dispatch Date: ". $m->getId()."\n"."Reference: ".$m->getId() ,0,'L');
			   */
//			  $this->MultiCell(50,3.6,"Pieces:".$c->getNumberPieces()."\n"."Description of Goods:"."\n".$c->getDescription()."\n"."Value for Customs: ". $c->getValue()." ".$c->getCurrency()."\n"."Date: ". $date."\n"."Reference: ".$c->getReference(),0,'L');
			  $current_x2 = $this->GetX();
			  $current_y2 = $this->GetY();
		  	  $multiCellHeight2 = $this->GetY() - $current_y;
				
		
			  
			
			  //$this->SetX($x);
			  //$this->SetY($current_y);
			  


			  
			
			  
			  if($multiCellHeight2>$multiCellHeight1)
			  {
				  $row_height = $multiCellHeight2;
			  }
			  else
			  {
				  $row_height = $multiCellHeight1;
			  }

  			  
			  $this->Cell(40,$row_height + 8,'',1,'L');
			
			  $this->Cell(40,$row_height + 8,'',1,'L');
			  
			  //$this->SetXY($this->GetX() + 50, $y); 
			  
			  $this->Cell(45,$row_height + 8,'',1,'L');
			  $this->Cell(35,$row_height + 8,'',1,'L');
			  $this->Cell(30,$row_height + 8,'',1,'L');
			  
			  //$this->SetX($this->GetX());
			  //$this->SetY($this->GetY());

			  
			 // $this->SetXY($this->GetX() + 30, $y);
			  
			 // $this->Cell(50,$row_height,'',1,'L'); 
			  
			  //$this->SetXY($current_x + 50, $current_y); 
			 
			  //$current_x = $this->GetX();           
			  
			  //$this->SetXY($current_x, $current_y);   
			  
			  //$this->MultiCell(50,$row_height,'',1,'L');
			  
			  //$this->SetXY($current_x + 30, $current_y);			  
			  //$this->Cell(30,$row_height,'',1,'L');
			  
  			  //$this->SetXY($current_x + 30, $current_y);			  
			  //$this->Cell(30,$row_height,'',1,'L');
			  			  
			  //$this->Cell(30,$row_height,'',1,'L');
			
              //$this->MultiCell(45,$row_height,'',1,'L');
			  //$x2 = $this->GetX();
			  //$y2 = $this->GetY();
			  
			  
			  

			  
			  
		}
		
						 
  }
			
		   $this->SetFont('Arial','',10);
		   
		  // $current_x = $x2;
		   //$current_y = $this->GetY();	
		   
		   //echo $x2 . " " .  $y2; 
		      
		   //$this->SetXY($current_x + 20, $current_y);
		   $this->Text($current_x,  $current_y + 50,Translation::GetCaption("TOTAL_WEIGHT")." : ");	
		   $this->Text($current_x + 45,  $current_y + 50,number_format($total_weight,2) . " kg");	
		   
		   //$this->SetXY($current_x, $current_y + 5);
			$this->Text($current_x,  $current_y + 55, Translation::GetCaption("TOTAL_NUMBER_PIECES")." : ");	
	       $this->Text($current_x + 45, $current_y + 55,$total_number_pieces);	
		   
		   //$pickUp = new PickupSmart($pickNoteId);
		   
		   //$this->Text($current_x + 65, $current_y +  60,"Delivery Note : ". $pickUp->getDeliveryNote());
		   
		   //$this->Text($current_x + 65, $current_y +  65,"Manifest Id : ". implode(",",$manifestIdArray));
		   
		  
		   
		   
		   //if($type == 'Pickup')
		   {
		 //  if(date("Y-m-d",strtotime($pickUp->getPickupDate())) != '1970-01-01')
		   	$pickup_date = date("d.m.Y", $m->getCollectionDate());
			   //$this->Text($current_x + 65, $current_y +  65,"Collected By : ");
			  // $this->Text($current_x + 65, $current_y +  70,"Name Of Driver : " . $driverName);
			  // $this->Text($current_x + 65, $current_y +  75,"ID Of Driver (Driver Licence Number) : " .                           $licence_number);
			   $this->Text($current_x, $current_y +  60,Translation::GetCaption("customer") . "-ID : ");
				$this->Text($current_x + 45, $current_y +  60, SessionManager::getUser()->getUserAccount());
				
			//	$this->Text($current_x, $current_y +  65,Translation::GetCaption("CUSTOMER_ACCOUNT") . " : ");
			//   $this->Text($current_x + 45, $current_y +  65, SessionManager::getUser()->getUserAccount());
				
			//   $this->Text($current_x, $current_y +  65,Translation::GetCaption("CUSTOMER_ADDRESS") . " : ");
			//	$this->MultiCell(100,10,str_replace(", ","\n",SessionManager::getUser()->getReturnAddress()),0,'L', false, 1, $current_x + 45, $current_y + 65);
//			 	$this->Text($current_x, $current_y +  70,Translation::GetCaption("CUSTOMER_ADDRESS") . " : ". SessionManager::getUser()->getReturnAddress() );
				//$this->Text($current_x, $current_y +  90,Translation::GetCaption("DATE"). " : " );
				//$this->Text($current_x+ 45, $current_y +  90, $pickup_date);
			   $this->Text($current_x, $current_y +  85,Translation::GetCaption("SIGNATURE"). " : " );
				$this->Text($current_x + 45, $current_y +  85,"______________________________________");
			   
		   }
		  	
		}
	}
   
 
}
