<?php
ob_start();
class GlOrderPdf8 extends TCPDF
{
    const BORDER_OFF = 0;
    const BORDER_FRAME = 1;
    const END_POS_NEXT_LINE = 2;
    const END_POS_TO_RIGHT = 0;
    const FOOTER = "Corporate Address: Unit 28 Sheraton Business Centre, 20 Wadsworth Road, Greenford, Middlesex, UB6 7JB";

    private $image_folder;
    private $order_status;
    private $order_number;
    private $your_ref;
    private $bookings_collection_booking_id;
    private $bookings_collection_description;
    private $bookings_collection_time;
    private $insurance;
    private $price;
    private $total;
	public $typeheader;
	
	

    private $transaction_id;
    private $transaction_date;
    private $transaction_time;
    private $transaction_total;
    private $consignment_list;
    private $totalShipments=0;
    private $totalWeight=0;
    private $totalNumberOfPieces=0;
    private $pageNumber=1;
	private $pdf;
	private $tagNumber;

    private $show_main_header_flag = true;

    /**
     * Generate full pdf
     */
    public static function buildPDFDocuments($consignment_list)
    {
		//print_r($consignment_list);
		//die;
        // instantiate PDF creation class
		///die;
		
		//print_r($this->con
		//$this->tagNumber = $tagnumber;
		
		//echo $this->tagNumber;
		
		//die;
		
        $PdfObj = new GlOrderPdf8('P','mm','A4');
        $PdfObj->createBookingDocs($consignment_list, "../images/");
        $PdfObj->AddInformation($consignment_list);
		
		$tag = $_GET['tag'];
		
        // Output PDF
        return $PdfObj->Output("BagManifest" . "_"  . $tag . ".pdf", "I");
    }

    /**
     * Override Header method to show consistent header on each page.
     *
     */
    public function Header()
    {
        if ($this->show_main_header_flag)
        {
            $this->SetFont('Arial','',10);
            // Logo

		    if(!isset($_SESSION['country']))
				$this->Image('../images/cpostint-logo.png',10,10,53);
			else
				//$this->Image('../images/yps.png',10,10,53);
				
				
			$this->AddShippingAddress();	
			$this->AddDestinationAddress();
          
            // Right hand header
            $this->SetFont('Arial','',8);
            $this->SetTextColor(25,25,112);
        
            //$this->SetFont('','',10);
            $this->SetTextColor(0,0,0);

   			
			
			//$this->Text(176,30,"Bag Tag: " . $this->tagNumber);
			
			if(isset($_GET['tag']) && $_GET['tag'] != '')
			{			
				$this->Text(10,30,"Tag: " .  $_GET['tag']);
			}
			
			$this->SetTextColor(100, 0, 0, 0);
			
			if(isset($_GET['hawb']) && $_GET['hawb'] == 'all')
			{			
				$this->Text(90,30,"All Bags Manifest", "B");
			}
			elseif(isset($_GET['hawb']))
			{
				//$this->SetTextColor(100, 0, 0, 0);
				$this->SetFont('Arial','B',10);
				$this->Text(90,30,"Bag Number " . $_GET['hawb'] . " Manifest", "B");
			}
			
			$this->SetTextColor(0, 0, 0, 100);
			
			$this->SetFont('Arial','B',8);
			
            $this->Text(184,30,$today = date("d.M.y"));
			
			
            $this->Text(176,30,"Date: ");
            $this->Text(184,30,$today = date("d.M.y"));
			
            $this->AddPageNumber();
            $this->Ln(-250);
			$this->SetFont('Arial','B',9);	
			$this->Cell(30,10,"Shipment Details",1,0,'C');
            $this->Cell(33,10,"Shipper",1,0,'C');
            $this->Cell(30,10,"Consignee",1,0,'C');
            //$this->Cell(15,10,"Service",1,0,'C');
            $this->Cell(15,10,"Weight",1,0,'C');
			$this->Cell(15,10,"Value",1,0,'C');
            $this->Cell(30,10,"Description",1,0,'C'); 
			$this->Cell(38,10,"Tracking No.",1,0,'C'); 
        }
		$this->SetDrawColor(0, 0, 0); // Hot Pink
		$this->SetLineWidth(1); // We will change the line width now to 2mm
		$this->Rect(5, 5, 200, 290, 'D');
	//	$this->SetX(10);
	//	$this->SetY(30);
    }

    /**
     * General footer
     *
     */
    public function Footer()
    {
        // Booking header
        $this->SetY(-15);
        //Select Arial italic 8
        $this->SetFont('Arial','I',8);
		//Print centered page number
    }
	
	public function AddDestinationAddress()
	{
		$x1 = 150;
		
		$flight_no = trim($_GET["flight"]);
		$flightFilter = new FlightInfoFilter();
		$flightFilter->addFlightNumberFilter($flight_no);
		$flightFilter->addStatusFilter("Open");
		
		//print_r($flightFilter);
		//$flightFilter->addDate(date("Y/m/d"));
		
		$list = $flightFilter->getColumnList("address_line_1, address_line_2, city, postcode, country");
		
		//echo "count " . count($list);
	
		if(count($list) > 0)
		{
			$con = $list[0];
			$addressline1 = $con->getAddressLine1();
			$addressline2 = $con->getAddressLine2();
			$city = $con->getCity();
			$postcode = $con->getPostCode();
			$countryname = $con->getCountry();
			
			$this->SetFont('Times','',7);
			//$this->Text($x1 , $y1 + 20, "ONE WORLD LOGISTICS LLC - " . strtoupper($city));
			$this->Text($x1 , $y1 + 20, strtoupper($addressline1)); 		
			$this->Text($x1 , $y1 + 23, strtoupper($addressline2) . ", " . strtoupper($postcode));
			$this->Text($x1 , $y1 + 26, strtoupper($countryname)); 
		}
		
	}
	
	public function AddShippingAddress()
	{
			
			
			if(isset($_SESSION['username']) && isset($_SESSION['country']))
			{
			
				$username = $_SESSION['username'];
			}
			else
			{
				$user = SessionManager::getUser();
				$username = $user->getUserName();
				$company = $user->getCompany();
			}
				
				$host = YPS_HOST;
				$user = YPS_USER;
				$password = YPS_PASSWORD;
				$db = YPS_DB;
	
				
				
				
				$con = mysqli_connect($host, $user, $password);
				mysqli_select_db($con, $db);
				
				$query1 = "select countryname, company, warehouse.addressline1, stateregion, citytown, postzipcode, countryname from admin, warehouse,country where 
						admin.warehouseid = warehouse.warehouseid and warehouse.countryid = country.countryid 
						and adminusername  = '$username'";
							
				
				
				//mail("mkazim4u@gmail.com", "yps", $query1);//echo $query1;
				
				
				
				$results = mysql_query($con, $query1);
				while($data=mysqli_fetch_array($results))
				{
					//$adminusername =$data['adminusername'];
					//$countryname =$data['countryname'];
					$addressline1 =$data['addressline1'];
					$stateregion = $data['stateregion'];
					$city = $data['citytown'];
					$postcode = $data['postzipcode'];
					$countryname = $data['countryname'];
					$company = $data['company'];
				}
				
				$this->SetFont('Times','',7);
				$this->Text($x1 + 15 ,$y1 + 17, "YourPersonalShopper.com");
				$this->Text($x1 + 15 ,$y1 + 20, strtoupper($addressline1)); 		
				$this->Text($x1 + 15, $y1 + 23, strtoupper($city) . ", " . strtoupper($stateregion). ", " .  strtoupper($postcode));
				$this->Text($x1 + 15, $y1 + 26, strtoupper($countryname)); 
				
			
			
	}

    public function AddPageNumber()
    {
        $this->Text(175,285,"Page No: ".$this->pageNumber);
        $this->pageNumber++;
    }

    public function setIncludeHeaderFlag ($includeFlag)
    {
        $this->show_main_header_flag = $includeFlag;
    }

    private function setBookingHeader()
    {
        // Booking header
        $this->SetFont('Arial','B',10);
        $this->setTextColor(255,255,255);
        $this->setDrawColor(204, 204, 204);
        $this->SetFillColor(29,104,198);
        $this->Cell(190,5,'Bookings',1,2,'C', true);
    }

    private function setBookingDetails()
    {
        // Booking details
        $this->Ln(1);
        $this->Ln(1);
        $this->Ln(1);
        $this->Ln(1);
        $this->setTextColor(0,0,0);
        $this->SetFillColor(133,175,222);
        $this->SetFont('Arial','B',10);
        $this->Cell(30,5,$this->bookings_quote_id,1,0,'L', true);
        $this->Cell(160,5,$this->bookings_description ,1,1,'L', true);
        $this->SetFillColor(255,255,255);
        $this->Cell(190,5,$this->bookings_collection_time,1,1,'L', true);
    }

    private function setBookingAddresses()
    {
        $this->SetFont('Arial','',10);
        // Booking details
        $y = $this->GetY();
        $x = $this->getX();
        $this->SetXY ($x + 95, $y);
        $this->MultiCell(95,5,$this->destination_address,1,'L',false);
        $this->SetXY ($x, $y);
        $this->MultiCell(95,5,$this->collection_address,1,'L',false);
	    $this->SetFont('Arial','B',10);
        $this->setTextColor(0,0,0);
        $this->setDrawColor(204, 204, 204);
        $this->SetFillColor(237,242,248);
        $this->Cell(190,5,'Parcels' . $this->total_weight,1,1,'L', true);
    }

    public function AddInformation($consignment_list, $tagnumber)
    {
  			$this->pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
			$style = array(
			'position' => '',
			'align' => 'C',
			'stretch' => false,
			'fitwidth' => false,
			'cellfitalign' => '',
			'border' => true,
			'hpadding' => 'auto',
			'vpadding' => 'auto',
			'fgcolor' => array(0,0,0),
			'bgcolor' => false, 
			'text' => false,
			'font' => 'helvetica',
			'fontsize' => 8,		
			'stretchtext' => 4		
		);
	   
		//echo $type.'00000000'.$servicetype;	exit;
		//	$this->Cell(33,7,"Barcode",1,0,'C');

       
             
             
			
			 $this->SetAutoPageBreak(false);
		        		
		$i = 0;
		$lastnumber = 'loser';	
		$this->SetFont('Arial','B',12);
		//$this->SetX(80);
		//$this->SetY(30);
		$this->Text(80,30,$type); 
  		$this->SetFont('Arial','',7);
		$this->SetX(10);
		$this->SetY(45);
		
		$hawbArray = array();
		
		foreach($this->consignment_list as $c)
		{
						
						 
			if(!in_array($c->getHawb(), $hawbArray))
				$hawbArray[] = $c->getHawb();
			//$ccc; exit;
			//echo $type[0]; exit;
			//$consignments= ConsignmentFilter::GetConsignmentsFromBagNumber($ccc,$type[0],$servicetype); 
		 
			//foreach ($consignments as $c)
			//{
			//$bagdetails = bagnumbersfilter::getBagFromConsignmentID($c->getConsignmentId());
			
			$x = $this->GetX();
			$y = $this->GetY() ;
						
			if ($i==10)
			{
			$i = 0;	
			$this->AddPage();
			$x = $this->GetX();
			$y = $this->GetY();
			
			//echo "acn " . $y;
			
			$this->SetY($y + 35);
			
			//echo "test " .$this->GetY();

			//$this->createBookingDocs($this->consignment_list, "../images/");
			$this->SetFont('Arial','B',12);
			//$this->Text(80,30,$type); 
			
			$this->SetFont('Arial','',7);
			//$this->Header();
			//$this->Ln(10);
			}
			$i++;
			$number="";
			   $x = $this->GetX();
			  
			   $y = $this->GetY();
			   
			   $this->SetX($x);
			   $this->SetY($y+2);
			   
			   //echo $c->getState();
			   
			   if($c->getState() != '' && $c->getState() > 0)
			   {
				   $parcel = new Parcel($c->getState());
				   $this->MultiCell(50,3.5,"HAWB:\n".$c->getHawb()."\nTracking:\n".$parcel->getTrackingNumber()."\n",0,'L');
			   }
               elseif($c->getAWB()!="")
				{
				 	$this->MultiCell(50,3.5,"HAWB:\n".$c->getHawb()."\nTracking:\n".$c->getAwb()."\n",0,'L');
				 	//$this->MultiCell(50,3.5,"TAG:".$c->getMawb()."\nHAWB: " . $c->getHawb() . "\nTracking:\n ".$c->getAWB()."\n",0,'L');
				}
               else
               {
			     	//$this->MultiCell(50,3.5,"TAG:".$c->getMawb()."\nBag Number:".$c->getBagNumber()."\nTracking:".$c->getAWB()."\n",0,'L');
				 	$this->MultiCell(50,3.5,"HAWB: ".$c->getHawb(),0,'L');
               }
			  $current_y = $this->GetY();
              $current_x = $this->GetX();
			  $this->SetXY($current_x+30 , $y);
			  
			  
			 if(isset($_SESSION['username']) && isset($_SESSION['country']))
			 {
			
				$username = $_SESSION['username'];
			 }
			 else
			 {
				$user = SessionManager::getUser();
				$username = $user->getUserName();
			 }
			  
			  
			    //$username = $_SESSION['username'];
				
				$host = YPS_HOST;
				$user = YPS_USER;
				$password = YPS_PASSWORD;
				$db = YPS_DB;

			
			
			
			$con = mysqli_connect($host, $user, $password);
			mysqli_select_db($con, $db);
				
				
				$query1 = "select countryname, company, warehouse.addressline1, stateregion, citytown, postzipcode, countryname from admin, warehouse,                    country where 
						admin.warehouseid = warehouse.warehouseid and warehouse.countryid = country.countryid 
						and adminusername  = '$username'";
							
				//echo $query1;
				
				
				
				$results = mysqli_query($con, $query1);
				
				while($data=mysqli_fetch_array($results))
				{
					//$adminusername =$data['adminusername'];
					//$countryname =$data['countryname'];
					$addressline1 =$data['addressline1'];
					$stateregion = $data['stateregion'];
					$city = $data['citytown'];
					$postcode = $data['postzipcode'];
					$countryname = $data['countryname'];
					$company = $data['company'];
					
				}
             
			  //if($c->getSenderName()!="")
              // $this->Cell(33,8, $c->getCompany() . " " . $addressline1 . " " . $stateregion ,0,'L');
			  
			   $current_y = $this->GetY();
               $current_x = $this->GetX();
			  
			   $this->MultiCell(32,3.5,
			   						  trim("YourPersonalShopper.com")."\n".
			  						  trim( $addressline1)."\n".
									  
									  $city."\n".
									  $postcode . "\n" .
									  $countryname ,0,'L');
									  
			   $this->SetXY($current_x + 33, $current_y);						  
              //else
              //$this->Cell(33,8,"One World Express",0,'L');
              $current_y = $this->GetY();
              $current_x = $this->GetX();
			  
			  if(trim(str_replace(",", "", $c->getCompany())) !== "")
			  	$company = "c\o " . $c->getCompany();
		
              $this->MultiCell(30,3.5,
			  						  trim($c->getContact())."\n".	
			  						  trim($company)."\n".
			  						  trim($c->getAddressLine1())."\n".
									  trim($c->getAddressLine2())."\n".
									  $c->getCity()."\n".
									  $c->getCountry(),0,'L');
              $date="";
			    
			  $multiCellHeight1 = $this->GetY() - $current_y;
			  
              if($c->getDateBooked()!="")
              $date=$c->getDateBooked();
              else
              $date=$c->getDatePrinted();
			  
              $this->SetXY($current_x + 30, $current_y);
              $current_x = $this->GetX();
			  $current_y = $this->GetY();
              //$this->Cell(15,8,$c->getService(),0,'L');
              $this->Cell(15,8,$c->getWeight(),0,'L');
			  $this->Cell(15,8,$c->getValue()." ".$c->getCurrency(),0,'L');
			  
			  //$current_y = $this->GetY();
              //$current_x = $this->GetX();
             //echo $current_x;
			 
			 if(strtolower($c->getCountry()) == 'bermuda')
			 	$notes = "Post Office: " . $c->getNotes();
				
			 //$this->writeHTMLCell(30,3.5, '', '', $notes,0, '', '', 'L');
			 
			  $this->Multicell(30,3.5,"Pieces:".$c->getNumberPieces()."\n"."Description:"."\n".$c->getDescription()."\n"."Value : ". $c->getValue()." ".$c->getCurrency(). "\n" . $notes."\n",0,'L');
			  
			 // $this->Cell(15, 8, $c->getAwb(), 0, 1);
			  
			  //$current_x = $this->GetX();
			  //$current_y = $this->GetY();
			  
			  //$this->SetXY($current_x + 70, $current_y);
			  
			 $style = array(
				//'position' => '',
				'align' => 'C',
				//'stretch' => false,
				'fitwidth' => true,
				//'cellfitalign' => '',
				//'border' => true,
				//'hpadding' => 'auto',
				//'vpadding' => 'auto',
				'fgcolor' => array(0,0,0),
				//'bgcolor' => false, //array(255,255,255),
				'text' => true,
				'font' => 'helvetica',
				'fontsize' => 8,
				'stretchtext' => 4
			);
			  
			  
			  if($c->getState() != '' && $c->getState() > 0)
			  {			  
			  	$this->write1DBarcode($parcel->getTrackingNumber(), 'C128', $current_x + 62, $current_y + 3, '', 10, 0.2, $style, 'N');
			  }
			  else
			  {
				  $parcel = new Parcel($c->getState());
				  
				  $this->write1DBarcode($c->getAwb(), 'C128', $current_x + 62, $current_y + 3, '', 10, 0.2, $style, 'N');
			  }
			  
			//  $this->SetXY($current_x, $current_y);
             // $current_x = $this->GetX();
			//echo $current_x; exit;  
			  $multiCellHeight2 = $this->GetY() - $current_y;
			
			  $this->SetX($x);
			  $this->SetY($y);
			  if($multiCellHeight2>$multiCellHeight1)
			  {
				  $row_height = $multiCellHeight2;
			  }
			  else
			  {
				  $row_height = $multiCellHeight1;
			  }

  			  $this->Cell(30,$row_height,'',1,'L');
			  $this->Cell(33,$row_height,'',1,'L');
			  
			  $this->MultiCell(30,$row_height,'',1,'L');
			  
			  $this->SetXY($current_x, $current_y);
              $current_x = $this->GetX();
			  
			  $this->Cell(15,$row_height,'',1,'L');
			  
			  $this->Cell(15,$row_height,'',1,'L');
			  
			  $this->Cell(30,$row_height,'',1,'L');
			  
              $this->MultiCell(38,$row_height,'',1,'C');
			  //$this->Cell(33,$row_height,'',1,'L');
			  //$column = array (20, 47, 51, 115, 150, 154, 120);
			  //$this->Cell(33,$row_height,$pdf->write1DBarcode($consignment->getAWB(), 'C128', $column[5], 34, '40', 7, 0.7, $style, 'Y'),1,'L');
			  //$this->Cell(33,$row_height,$this->pdf->write1DBarcode('CODE 39', 'C39', '', '', '', 18, 0.4, $style, 'N'),1,'L');
			  
			//  $this->pdf->Cell(0, 0, 'CODE 39 - ANSI MH10.8M-1983 - USD-3 - 3 of 9', 0, 1);
			//  $this->pdf->write1DBarcode('CODE 39', 'C39', '', '', '', 18, 0.4, $style, 'N');
			  //$lastnumber = $bagdetails[0]->getBagNumber();
			 
			  
          //    $this->Ln(0);
              $this->totalWeight=$this->totalWeight+$c->getWeight();
              $this->totalShipments++;
              $this->totalNumberOfPieces=$this->totalNumberOfPieces+$c->getNumberPieces ();
			  
			  $this->totalValue= $this->totalValue + $c->getValue();
             //}
			 
		}
		
		//echo count($hawbArray);

//$this->totalShipments
      //       $this->Ln(15);
             $this->Cell(1);
			 $this->SetFont('Arial','B',9);
			 $this->Cell(70,10,"Totals:                   Shipments                    Total Weight                     Pieces                     Value",0,'L');
			 $this->Ln(5);
        	 $this->SetFont('Arial','',9);
			 $this->Cell(70,10,"                                ". count($hawbArray)."                                  ".$this->totalWeight."KG"."                              ".count($this->consignment_list)."                              ".$this->totalValue,0,'L');
           //  $this->Ln(50);

         //   $this->SetFont('Arial','B',10);
         //   $this->Cell(33     ,7,"SIGN: _______________________     ",0,'L');
         //   $this->Cell(33,7,"                                        PRINT: _______________________                       ",0,'L');
         //   $this->Cell(33,7,"                                                                              DATE: ____/____/____",0,'L');
    }

    private function setBookingParcels()
    {
        // detail the parcels
        $this->SetFont('','',10);
        $this->setTextColor(51,51,51);
        $this->SetFillColor(0,0,0);
        $this->SetFont('','B',10);
        $this->Cell(10,5,$this->parcel_number,1,0,'L');
        $this->Cell(180,5,$this->parcel_text,1,1,'L');
    }

    private function setBookingDetailsTotal()
    {
        $this->Cell(30,5,'1',1,0,'L');
        $this->Cell(140,5,'Total',1,0,'R');
        $this->Cell(20,5,$this->payment_total,1,1,'R');
        $this->Ln(5);

    }

    private function setBookingCosts()
    {
        // Booking costings
        $this->SetFont('Arial','B',10);
        $this->setTextColor(51,51,51);
        $this->setDrawColor(204, 204, 204);
        $this->SetFillColor(237,242,248);
        $this->Cell(190,5,'Costings',1,1,'L', true);
        //
        $this->SetFont('','B',10);
        $this->Cell(30,5,'Parcel Delivery',1,0,'L');
        $this->SetFont('','',10);
        $this->Cell(160,5,$this->cost_delivery,1,1,'R');
        //
        if (CONFIG_INSURANCE)
        {
            $this->SetFont('','B',10);
            $this->Cell(30,5,'Insurance',1,0,'L');
            $this->SetFont('','',10);
            $this->Cell(160,5,$this->insurance,1,1,'R');
        }
        //
        $this->SetFont('','B',10);
        $this->Cell(30,5,'V.A.T.',1,0,'L');
        $this->SetFont('','',10);
        $this->Cell(160,5,$this->cost_vat,1,1,'R');
        $this->SetFont('','B',10);
        $this->SetFont('','B',10);
        $this->Cell(30,5,'Total',1,0,'L');
        $this->SetFont('','',10);
        $this->Cell(160,5,$this->cost_total,1,1,'R');
    }

    private function setOrderDetails()
    {
        $x = 110;
        $y = 50;
        //
        $valArray = array (
                'Transaction ID' => $this->payment_transaction_id,
                'Transaction date' => $this->payment_transaction_date,
                'Transaction time' => $this->payment_transaction_time,
                'Total amount' => $this->payment_transaction_total
            );
        $this->showTable($x, $y, $valArray, 'Payment Details');
    }


    private function showBillingDetails($orderObj)
    {
        // Get the basket
        $basket = $orderObj->getBasket();

        $x = 20;
        $y = 50;
        $valArray = array (
                'Billing Name' => $basket->getCustomerName(),
                'Email Address' => $basket->getEmailAddress(),
                'Contact Number' => $basket->getPhoneNumber()
            );
        $this->showTable($x, $y, $valArray, 'Billing Details');
	}

    private function showTable($x, $y, $valArray, $title="")
    {
        $col1Width = 32;
        $col2Width = 45;

        // Set postion
        $this->setXY ($x, $y);

        if ($title != "")
        {
            // title
            $this->SetFont('Arial','B',10);
            $this->setTextColor(51,51,51);
            $this->setDrawColor(204, 204, 204);
            $this->SetFillColor(237,242,248);
            $this->Cell($col1Width + $col2Width,5,$title,1,1,'L', true);
        }

        foreach ($valArray as $key=>$val)
        {
            $this->setX ($x);
            $this->SetFont('','B',10);
            $this->Cell($col1Width,5,$key,1,0,'L');
            $this->SetFont('','',10);
            $this->Cell($col2Width,5,$val,1,1,'L');
        }
    }

    public function createBookingDocs($consignment_list, $image_folder)
    {
        // get basket       
		//print_r($consignment_list);
		//die;
		
        $this->consignment_list=$consignment_list;
		//echo count($this->consignment_list);exit;
        $this->image_folder = $image_folder;
        // Need to set any information required for header, before calling add page.
        // - header written by the add page method

        // Order page
		
        $this->AddPage();
        // show the billing details
       
        // Booking page$this
        $this->MultiCell(1,5,"\n\n",GlOrderPdf::BORDER_OFF);
        // build booking header
       // $this->setBookingHeader();
    }
}