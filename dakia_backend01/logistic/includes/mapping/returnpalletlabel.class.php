<?php

//ob_start();
/*require_once(SETTING_DIR_REMOTE."includes/3rdparty/barcodegen/class/BCGFontFile.php");
require_once(SETTING_DIR_REMOTE."includes/3rdparty/barcodegen/class/BCGColor.php");
require_once(SETTING_DIR_REMOTE."includes/3rdparty/barcodegen/class/BCGDrawing.php");

// Including the barcode technology
require_once(SETTING_DIR_REMOTE."includes/3rdparty/barcodegen/class/BCGcode128.barcode.php");*/


class ReturnPalletLabel extends TCPDF
{
    const BORDER_OFF = 0;
    const BORDER_FRAME = 1;
    const END_POS_NEXT_LINE = 2;
    const END_POS_TO_RIGHT = 0;
    const FOOTER = "";

  
	
    private $pageNumber=1;
	private $pdf;
	private $firstpager=0;


    private $show_main_header_flag = true;

    /**
     * Generate full pdf
     */
    public static function buildPDFDocuments($palletid, $palletno, $totalShipments = 0, $manifestid = '', $shippingUserId)
    {
      
	  //	return $consignmentid; exit;
	    // instantiate PDF creation class
        $PdfObj = new ReturnPalletLabel();
		$page_size = array (100, 200);

	    $PdfObj->AddPage("P",$page_size);
        //$PdfObj->createBookingDocs('', "../images/");
		//$consignmentid =  $_GET['consignmentid'];
		
		//$pattern = '/[^0-9]*/';	
		//$palletid =  preg_replace($pattern,'', $palletno);
		
		//echo $palletid . " " . $palletno . "<br>";	
		
		$PdfObj->AddInformation($palletid, $palletno, $totalShipments, $manifestid, $shippingUserId);
		
		
		
 		// save invoice pdf path and invoice date to the database
		
		// Output PDF
		$PdfObj->Output("../_assets/pdf/palletlabel_". $palletid .".pdf", "F");
		//return $PdfObj->Output("../_assets/pdf/label ". date("Y-m-d H:i:s") .".pdf", "I")
		return  SETTING_MAIN_URL ."_assets/pdf/palletlabel_". $palletid .".pdf";
    }

    /**
     * Override Header method to show consistent header on each page.
     *
     */
    public function Header()
    {
		

        if ($this->show_main_header_flag)
        {
            
			$this->SetFont('Times','',10);
			$user = Sessionmanager::getUser();
          
            // Right hand header
            $this->SetFont('Times','B',13);
            $this->SetTextColor(25,25,112);
         
            $this->SetFont('','',10);
            $this->SetTextColor(0,0,0);
             
			
			
			
           
		//	$this->Cell(30,10,"Bar Code",1,0,'C');    
			
        }
		$this->SetDrawColor(0, 0, 0); // Hot Pink
		$this->SetLineWidth(0); // We will change the line width now to 2mm
	//	$this->Rect(5, 5, 200, 285, 'D');
    }

    /**
     * General footer
     *
     */
    public function Footer()
    {
        // Booking header
        $this->SetY(-15);
        //Select Times italic 8
        $this->SetFont('Times','I',8);
		//Print centered page number
    }

    public function AddPageNumber()
    {
        $this->Text(175,285,"Page No: ".$this->pageNumber);
        $this->pageNumber++;
    }   
    
  

   
    public function AddInformation($palletid, $palletno, $totalShipments, $manifestid, $shippingUserId)
    {
			
				$pallet = new Pallet($palletid);
				$carrier = $pallet->getCarrier();
				$userid = $pallet->getUserId();
				
				$total_carton = 0;
				
				//echo $palletid . " " .  $palletno;
				
				//die;
				
				/*
				
				if(trim($palletno) != '')
				{
					$carton_numbers_arr = Consignment::GetBagCountInPallet($palletno);
				
				//print_r($carton_numbers_arr);
					$total_carton = count($carton_numbers_arr);	
					
				}
				
				*/
				
				//echo "count".$total_carton;
				
				
				$shippingUser = new User($shippingUserId);
				$shippersReturnAddress = $shippingUser->getReturnAddress();
				
				//print_r($shippersReturnAddress);
				
				
				
				
				$user = new User($userid);
			
				$y1 = $this->GetY();
				$x1 = $this->GetX();			
				
				$this->SetXY($x1, $y1);
				$this->Image('../images/logo.jpg',5,10,40,20);
				
				$this->line(10,55,90,55);
				
				$this->line(10,70,90,70);
				$this->line(10,85,90,85);					
				$this->line(10,115,90,115);
				$this->line(10,140,90,140);
				
				$this->line(50,55,50,140);
				
				$this->line(10,40,90,40);
				
				$this->line(10,40,10,140);
				$this->line(90,40,90,140);					
				
				$this->line(10,100,90,100);
				
				$this->SetFont('Times','B',10);
				
				
				$this->Text($x1 + 10, 63, "Manifest Id");
				$this->Text($x1 + 10,78, "Date");
				$this->Text(20,94, "User"); 
				
				$this->Text(20,108, "Total Shipments");
				$this->Text(65,108 , $totalShipments);   
				

				
				
				
				
				
				$this->Text(62, 63, $manifestid);  	
				
				/*if($pallet->getClose() == "Y")
					$this->Text(62, 63, "CLOSE");  	
				else
					$this->Text(62, 63, "OPEN");	
				*/
				
				$this->Text(55,78, date("d-m-Y H:i"));  
				$this->Text(60,94 , strtoupper($user->getUserName()));  
				
				
				
				$this->Text(15,120, "Customer Address");
				$this->setXY(55, 120);
				$this->MultiCell(30, 20 , $shippersReturnAddress, 0, 'L', false, '1', $this->getX(), $this->getY());
									
			    //$this->Text(63,105, "test3");  
					
				
				
				
				
				//$this->Image('../images/hulogo.jpg',60,10,33,20);
				
				
				
				
	
			
				$this->pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
	
				$style = array(
				'position' => '',
				'align' => 'C',
				'stretch' => false,
				'fitwidth' => true,
				'cellfitalign' => '',
				'border' => false,
				'hpadding' => 'auto',
				'vpadding' => 'auto',
				'fgcolor' => array(0,0,0),
				'bgcolor' => false, //array(255,255,255),
				'text' => TRUE,
				'font' => 'helvetica',
				'fontsize' => 8,		
				'stretchtext' => 4		
			);
			
				
				$this->SetXY(10, 45);
								
				$this->SetFont('Times','B',15);				
				
				//$this->Text(40, 50, $carrier);
				
				$this->MultiCell(80, 80, $carrier , 0, 'C', false, '1', $this->getX(), $this->getY());
				
				
				$warehouseid = Sessionmanager::getUser()->getWarehouseId();
				$warehouse = new Warehouse($warehouseid);
				
				$country = new Country($warehouse->getCountryId());
				$countryName = $country->getName();
				
				
				$this->SetFont('Times','',9);	
				
				
				/*if ($warehouse->getStateRegion() != '') 
					$state =  $warehouse->getStateRegion() . "\n";
				else
					$state =  "";	*/
				
				$this->MultiCell(80, 80, $warehouse->getAddressLine1() ."\n". 
										 $warehouse->getAddressLine2() . "\n".
										 //$state.
										 $warehouse->getCityTown() . "\n".
										 $warehouse->getPostZipCode() . "\n".
										 $countryName
										 
										 	, 0, 'L', false, '1',  65, 15);
				
				
				
				
				$this->SetFont('Times','B',12);	
				//$this->Text(42, 60, 'Pallets');
				
							

				
				$imagecn='/var/www/vhosts/oneworldexpress.co.uk/httpdocs/remote/images/image35.png';
				$y2 = $y1 + 45;
				//foreach($consignmentresult as $con)
				//{
					$this->SetFont('Times','B',12);	
							
					//$this->Text($x1+25,$y2, $con->getMawb());  
					
					$this->write1DBarcode($palletno, 'C128', $x1+15,145,50,15, '',$style);
					
					$this->write1DBarcode($manifestid , 'C128', $x1+20,160,50,15, '',$style);
					
					//$barcode = $fid;
					
					/*$barCode = $this->generatebarcode($palletno,$imagecn);
					
					//$this->Image($topBarCode,16,43,80,8);
					$this->Image($barCode,$x1+20,170,40,15);*/
					
					
					$y2 += 5;
				//}
				
			
				
				$this->SetAutoPageBreak(false);
				$this->SetFont('Times','',8);
				
				/*if($carton_numbers_arr > 0)
				{				
					$this->AddCartonNumbersList($carton_numbers_arr, $palletno);			
				}
				*/
			
			
			 //  $this->totalWeight=$this->totalWeight+$c->getWeight();
		 
			
	
             
    }
	
	public function AddCartonNumbersList($carton_numbers_arr, $palletno)
	{
		
		//print_r($carton_numbers_arr);
		
		$page_size = array (100, 200);
		
		$this->AddPage("P", $page_size);		
		$this->Image('../images/logo.jpg',5,10,40,20);
		
		$y = 5;
		
		$this->SetFont('Times','B',12);	
		
		$this->MultiCell(80, 80, "Carton Numbers" , 0, 'C', false, '1', 10, 40);

		$this->SetFont('Times','',10);	
		
		if(is_array($carton_numbers_arr))
		{
		
			$count = 1;
			foreach($carton_numbers_arr as $carton)
			{
				$this->Text(35, 55 + $y, $count. ". " . $carton['bag_number']);
				$count++;
				$y+= 5;
			}
		}
		else
		{
			$this->Text(35, 55 + $y, $carton['bag_number']);
		}
		
		//$imagecn='/var/www/vhosts/oneworldexpress.co.uk/httpdocs/remote/images/image35.png';
		//$y2 = $y1 + 45;
				//foreach($consignmentresult as $con)
				//{
					$this->SetFont('Times','B',12);	
					
					$style = array(
				'position' => '',
				'align' => 'C',
				'stretch' => false,
				'fitwidth' => true,
				'cellfitalign' => '',
				'border' => false,
				'hpadding' => 'auto',
				'vpadding' => 'auto',
				'fgcolor' => array(0,0,0),
				'bgcolor' => false, //array(255,255,255),
				'text' => TRUE,
				'font' => 'helvetica',
				'fontsize' => 8,		
				'stretchtext' => 4		
			);
			
			
			//$this->write1DBarcode($palletno, 'C128', $x1+15,150,50,20, '',$style);
					
			$this->write1DBarcode($palletno, 'C128', 28,170,50,20, '',$style);
		
		/*$barCode = $this->generatebarcode($palletno,$imagecn);
					
					//$this->Image($topBarCode,16,43,80,8);
		$this->Image($barCode,28,170,40,15);*/
		
		
	}
	
public function createBookingDocs($consignment_list, $image_folder)
    {
        // get basket       
        $this->consignment_list=$consignment_list;
        $this->image_folder = $image_folder;
        // Need to set any information required for header, before calling add page.
        // - header written by the add page method

        // Order page
      //  $this->AddPage();
        // show the billing details
       
        // Booking page
       // $this->MultiCell(1,5,"\n\n",GlOrderPdf::BORDER_OFF);
        // build booking header
       // $this->setBookingHeader();
    }
}