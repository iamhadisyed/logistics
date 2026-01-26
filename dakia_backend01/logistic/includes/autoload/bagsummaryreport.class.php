<?php
ob_start();

class BagSummaryReport extends TFPDF
{
    const SERVICE_LIST_X = 10;
	const SERVICE_LIST_Y = 100;
	const LINE_HEIGHT = 5;
	const LEFT_MARGIN = 20;

	const ROWS_FIRST_PAGE = 1; //23;
	const ROWS_PAGE = 40;

	const START_Y = 50;
	const END_Y = 260;

	private $pdf;
	private $service_list = array();
	private $consignment_list = array();
	protected $diff_array = array();
	private $handling;
	private $scandate;
	private $mawbno;
  
    private $pageNumber=1;
	private $firstpager=0;


    private $show_main_header_flag = true;

    /**
     * Generate full pdf
     */
    public static function buildPDFDocuments($scandate,$mawbno,$handling)
    {
     
	 
	 // $mawbno = '618-97316155';
	   
	   $PdfObj = new BagSummaryReport('P','mm','A4');
       $PdfObj->createBookingDocs('', "../images/");
	   $PdfObj->addSummaryAccount(self::START_Y, true, $scandate, $mawbno, $handling);	
     
		 // Output PDF
		$PdfObj->Output("../_assets/manifest/".time()."bag_summary.pdf", "F");
		return "../_assets/manifest/".time()."bag_summary.pdf";
		//return $PdfObj->Output("../_assets/manifest/".time()."bag_summary.pdf", "I");	  
	  
	  
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
		$this->SetLineWidth(1); // We will change the line width now to 2mm
		$this->Rect(5, 5, 200, 285, 'D');
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

    public function setIncludeHeaderFlag ($includeFlag)
    {
        $this->show_main_header_flag = $includeFlag;
    }

    private function setBookingHeader()
    {
        // Booking header
        $this->SetFont('Times','B',10);
        $this->setTextColor(255,255,255);
        $this->setDrawColor(204, 204, 204);
        $this->SetFillColor(29,104,198);
        $this->Cell(190,5,'Invoice',1,2,'C', true);
    }

    


   	private function addSummaryAccount($y, $new_page = true, $scandate, $mawbno, $handling) 
	{
			
			
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
			'text' => false,
			'font' => 'helvetica',
			'fontsize' => 8,		
			'stretchtext' => 4		
		);
			
	
		//$this->Image('/var/www/vhosts/sandbox.oneworldexpress.co.uk/httpdocs/remote/images/logo1.png',130,10,53,20);
		//$this->SetFontSize(10);
		$this->SetFont('times', 'BI', 10, '', 'false');
		
		//bag report start
		$consignmentFilter = new ConsignmentFilter();
		$list = $consignmentFilter->GetBagSummaryReportMawb($scandate, $mawbno, $handling);	
		
		if(count($list) > 0)
		{
			
			$heading = $_GET["carrier"];
			if($heading != "")
			{
				if($heading == "RoyalMailIntTrackedAndSigned")
				{
					$heading = "RoyalMailIntT&S";
				}
				$heading =  $heading ." Bag Summary Report";
			}
			else
			{
				$heading =  "Bag Summary Report";
			}
			
			
			if($scandate != "")
			{
				$date = $scandate;
			}
			else
			{
				$date = date("Y-m-d");
			}
			
			$this->Text(50, 40, "*************     $heading  - " . $date . "     *************");
			
			$this->SetFont('times', '', 8, '', 'false');
			
			
			//$handling = "'REGPOST INT','REGPOST','REGPOSTHUN'";	
			
			
	
			$left = self::LEFT_MARGIN;
			//
			$cols = array (60, 120);
			$cols1 = array(60,40,20,20,20,40);
			$x_end = $left;
			for ($i=0; $i < sizeof($cols); $i++) $x_end += $cols[$i];
	
			
			$y = (10 * self::LINE_HEIGHT);
			
			
			$y += (1 * self::LINE_HEIGHT);
			
			$this->setXY ($left, $y);
			
			$this->SetFont('times', 'B', 8, '', 'false');
			//
			
			$this->Cell($cols[0], self::LINE_HEIGHT, "MAWB", 1, 0, "C");
			$this->Cell($cols[1], self::LINE_HEIGHT, "TOTAL BAGS", 1, 2, "C");
	
			$bag_total = "0";
			$mno = "";
			$bag_number = "";
			$total_scanned = 0;
			$total_notscanned = 0;
			$carrier = "";
		
			foreach ($list as $summary)
			{
			
				$this->SetFont('times', '', 8, '', 'false');	
				
					$y += (1 * self::LINE_HEIGHT);
					if($y>=260)
					{
						$y = 25;
						$this->AddPage();
					}
					$this->setXY ($left, $y);
					if($summary->getMawb() != $mno)
					{	
						$this->SetFont('times','B',9);
						$this->Cell($cols[0], self::LINE_HEIGHT, $summary->getMawb(), 1, 0, "L");
						$total_shipment = $consignmentFilter->GetTotalShipment($summary->getMawb(), $scandate ,$handling);
						$total = $total_shipment[0]->getNumberPieces();
						$this->Cell($cols[1], self::LINE_HEIGHT, $total_shipment[0]->getNumberPieces(), 1, 2, "C");
						$total_scanned = 0;
						$total_notscanned = 0;
						$y += (1 *self::LINE_HEIGHT);
						
						$this->setXY ($left, $y);
					
						$this->Cell($cols1[0], self::LINE_HEIGHT, "Service", 1, 0, "C");
						$this->Cell($cols1[1], self::LINE_HEIGHT, 'Bag Number', 1, 0, "C");
						$this->Cell($cols1[2], self::LINE_HEIGHT, 'Pieces', 1, 0, "C");
						$this->Cell($cols1[3], self::LINE_HEIGHT, 'Adv Wgt', 1, 0, "C");
						$this->Cell($cols1[4], self::LINE_HEIGHT, 'Ckd Wgt', 1, 0, "C");
						$this->Cell($cols1[3], self::LINE_HEIGHT, "Status", 1, 0, "C");
						$y += (1 *self::LINE_HEIGHT);
						
						
					}
					$this->SetFont('times','',8);
					if($summary->getHandling() != $carrier)
					{	
					
						
						$ServiceFilter = new ServiceFilter();
						$ServiceFilter->addCodeExactFilter($summary->getHandling());
						$servicename = $ServiceFilter->getColumnList('name');
						if(count($servicename) > 0)
						{
							$this->setXY ($left, $y);
							$this->Cell($cols[0], self::LINE_HEIGHT, $servicename[0]->getName(), 1, 0, "C");
							$this->Cell($cols[1], self::LINE_HEIGHT, "", 1, 2, "L");
							$y += (1 *self::LINE_HEIGHT);
						}
					}
						
						
						$this->setXY ($left, $y);
					
						$this->Cell($cols1[0], self::LINE_HEIGHT, "", 1, 0, "C");
						$this->Cell($cols1[1], self::LINE_HEIGHT, $summary->getBagNumber(), 1, 0, "C");
						$this->Cell($cols1[2], self::LINE_HEIGHT, $summary->getNumberPieces(), 1, 0, "C");
						$this->Cell($cols1[3], self::LINE_HEIGHT, $summary->getBagWeight(), 1, 0, "C");
						$this->Cell($cols1[4], self::LINE_HEIGHT, $summary->getBagWeightActual(), 1, 0, "C");
						
						//echo $summary->getBagNumber() . " " . $summary->getDateScanned() . "<br>";
						
						if($summary->getDateScanned() != NULL && $summary->getDateScanned() != '' && $summary->getDateScanned() != '0000-00-00 00:00:00')
						{
							$this->Cell($cols1[3], self::LINE_HEIGHT, "Scanned", 1, 0, "C");
							$total_scanned += $summary->getNumberPieces();
						}
						else
						{
							$this->Cell($cols1[3], self::LINE_HEIGHT, "Not Scanned", 1, 0, "C");
							$total_notscanned += $summary->getNumberPieces();
						}
						$total1 = $total_scanned + $total_notscanned;
					
						if($total == $total1)
						{
								$y += (1 *self::LINE_HEIGHT);
						
								$this->setXY ($left, $y);
								$this->SetFont('times','B',9);
								$this->Cell($cols1[0], self::LINE_HEIGHT, "Total Scanned", 1, 0, "C");
								$this->Cell($cols1[1], self::LINE_HEIGHT, $total_scanned, 1, 0, "C");
								$this->Cell(60, self::LINE_HEIGHT, "Total Not Scanned", 1, 0, "C");
								$this->Cell($cols1[3], self::LINE_HEIGHT, $total_notscanned, 1, 0, "C");
						}
								
				$mno =  $summary->getMawb();	
				$carrier = $summary->getHandling();
			}
			
					
		
		
		$y += (1 *self::LINE_HEIGHT);
		$this->setXY ($left, $y);
		$this->AddPage();
		
		
			
			
		}
		
		$countrylist = $consignmentFilter->GetCountrySummaryReportMawb($scandate, $mawbno ,$handling);
		if(count($countrylist) > 0)
		{
			//$this->Image('/var/www/vhosts/sandbox.oneworldexpress.co.uk/httpdocs/remote/images/logo1.png',130,10,53,20);
			//$this->SetFontSize(10);
			$this->SetFont('times', 'BI', 10, '', 'false');
			
			$heading = $_GET["carrier"];
			if($heading != "")
			{
				if($heading == "RoyalMailIntTrackedAndSigned")
				{
					$heading = "RoyalMailIntT&S";
				}
				$heading =  $heading ." Country Summary Report";
			}
			else
			{
				$heading =  " Country Summary Report";
			}
			
			if($scandate != "")
			{
				$date = $scandate;
			}
			else
			{
				$date = date("Y-m-d");
			}
			
			
			
			$this->SetFont('times', '', 8, '', 'false');
			$left = self::LEFT_MARGIN;
			
			$cols = array (30,30,40,40,40);
			$cols1 = array(60,40,40,40);
			$x_end = $left;
			for ($i=0; $i < sizeof($cols); $i++) $x_end += $cols[$i];
	
			
			$y = (10 * self::LINE_HEIGHT);
			$y += (1 * self::LINE_HEIGHT);
			
			$this->setXY ($left, $y);
			
			$this->SetFont('times', 'B', 8, '', 'false');
			//
			
			/*$this->Cell($cols[0], self::LINE_HEIGHT, "MAWB", 1, 0, "C");
			$this->Cell($cols[1], self::LINE_HEIGHT, "ACCOUNT", 1, 0, "C");
			$this->Cell($cols[2], self::LINE_HEIGHT, "COUNTRY", 1, 0, "C");
			$this->Cell($cols[3], self::LINE_HEIGHT, "WEIGHT", 1, 0, "C");
			$this->Cell($cols[4], self::LINE_HEIGHT, "TOTAL SHIPMENT", 1, 2, "C");		*/				
	
			$mwbno = "";
			$account = "";
			$service = "";
			$totalWeight = "0";
			$totalPieces = "0";
			$i = 0;
		
			foreach ($countrylist as $csummary)
			{
				
			
				$this->SetFont('times', '', 9, '', 'false');	
				
						
				
					
					$y += (1 * self::LINE_HEIGHT);
					if($y>=260)
					{
						$y = 25;
						$this->AddPage();
					}
					$this->setXY ($left, $y);
					$this->SetFont('times','B',9);
					
					
					
						
					if($i==0)
					{
						$this->Text(50, 40, "*************     $heading  - " . $date . "     *************");
						$y += (1 * self::LINE_HEIGHT);
						$this->setXY ($left, $y);	
					}
					else
					{
						if($csummary->getAccount() != $account)
						{
							$this->AddPage();
							$y = 25;
						}
						else if($csummary->getMawb() != $mwbno)
						{
							$this->AddPage();
							$y = 25;
						}
					}

					/*if($csummary->getMawb() != $mwbno)
					{	
						$this->Cell($cols[0], self::LINE_HEIGHT, $csummary->getMawb(), 1, 0, "L");
						$this->Cell($cols[1], self::LINE_HEIGHT, "", 1, 0, "L");
						$this->Cell($cols[2], self::LINE_HEIGHT, "", 1, 0, "L");
						$this->Cell($cols[3], self::LINE_HEIGHT, "", 1, 0, "L");
						$this->Cell($cols[4], self::LINE_HEIGHT, "", 1, 2, "L");
						//$account ="";
						
						$y += (1 * self::LINE_HEIGHT);
												
					}*/
					
					$this->setXY ($left, $y);	
					if($csummary->getMawb() != $mwbno || ($csummary->getMawb() == $mwbno && $csummary->getAccount() != $account))
					{
						$this->Cell($cols[0], self::LINE_HEIGHT, "MAWB", 1, 0, "C");
						$this->Cell($cols[1], self::LINE_HEIGHT, "ACCOUNT", 1, 0, "C");
						$this->Cell($cols[2], self::LINE_HEIGHT, "COUNTRY", 1, 0, "C");
						$this->Cell($cols[3], self::LINE_HEIGHT, "TOTAL SHIPMENT", 1, 0, "C");
						$this->Cell($cols[4], self::LINE_HEIGHT, "WEIGHT", 1, 2, "C");
						$y += (1 * self::LINE_HEIGHT);
						$this->setXY ($left, $y);	
						
						$this->Cell($cols[0], self::LINE_HEIGHT, $csummary->getMawb(), 1, 0, "L");
						$this->Cell($cols[1], self::LINE_HEIGHT, "", 1, 0, "L");
						$this->Cell($cols[2], self::LINE_HEIGHT, "", 1, 0, "L");
						$this->Cell($cols[3], self::LINE_HEIGHT, "", 1, 0, "L");
						$this->Cell($cols[4], self::LINE_HEIGHT, "", 1, 2, "L");
						$y += (1 * self::LINE_HEIGHT);
					}
					
					$this->setXY ($left, $y);
					if(($csummary->getAccount() != $account) || ($csummary->getAccount() == $account && $csummary->getMawb() != $mwbno ))
					{
						
						$this->setXY ($left, $y);
						
						$this->Cell($cols[0], self::LINE_HEIGHT, "", 1, 0, "L");
						$this->Cell($cols[1], self::LINE_HEIGHT, $csummary->getAccount(), 1, 0, "L");
						$this->Cell($cols[2], self::LINE_HEIGHT, "", 1, 0, "L");
						$this->Cell($cols[3], self::LINE_HEIGHT, "", 1, 0, "L");
						$this->Cell($cols[4], self::LINE_HEIGHT, "", 1, 2, "L");
						$y += (1 * self::LINE_HEIGHT);		
						$subweight = "0";
						$subpeice= "0";		
				 }
				
				
					if($csummary->getHandling() != $service || ($csummary->getAccount() != $account && $csummary->getMawb() == $mwbno ))
					{	
						$ServiceFilter = new ServiceFilter();
						
						if($csummary->getHandling() == "19")
						{
							$services = "19EURDPDDE";
							$ServiceFilter->addCodeExactFilter($services);
						}
						else
						{
							$ServiceFilter->addCodeExactFilter($csummary->getHandling());
						}
					
						
						
						$sname = $ServiceFilter->getList();
						$sname = $sname[0];
						
						if(count($sname) > 0)
						{
					
							$this->setXY ($left, $y);
							$this->Cell($cols[0], self::LINE_HEIGHT, "", 1, 0, "L");
							$this->Cell($cols[1], self::LINE_HEIGHT, $sname->getName(), 0, 0, "L");
							$this->Cell($cols[2], self::LINE_HEIGHT, "", 0, 0, "L");
							$this->Cell($cols[3], self::LINE_HEIGHT, "", 0, 0, "L");
							$this->Cell($cols[4], self::LINE_HEIGHT, "", 1, 2, "L");
							$y += (1 *self::LINE_HEIGHT);
						
						}
					}
					
						$this->SetFont('times','',8);
						
						$this->setXY ($left, $y);
						$this->Cell($cols1[0], self::LINE_HEIGHT, "", 1, 0, "C");
						$this->Cell($cols1[1], self::LINE_HEIGHT, strtoupper($csummary->getCountry()), 1, 0, "C");
						//display total shipment
						$this->Cell($cols1[2], self::LINE_HEIGHT, $csummary->getNumberPieces(), 1, 0, "C");
						$this->Cell($cols1[3], self::LINE_HEIGHT, $csummary->getWeight(), 1, 0, "C");
						
						
								
					

						$subweight += $csummary->getWeight();
						$subpeice += $csummary->getNumberPieces();					

						$totalWeight += $csummary->getWeight();
						$totalPieces += $csummary->getNumberPieces();
						$j =$i+1;
					
						if(count($countrylist)> $j && ($countrylist[$j]->getHandling() !=  $countrylist[$i]->getHandling() || $countrylist[$j]->getAccount() != $countrylist[$i]->getAccount()) )
						{
							$y += (1 *self::LINE_HEIGHT);
							$this->setXY ($left, $y);
							$this->SetFont('times','B',9);
							$this->Cell($cols1[0], self::LINE_HEIGHT, "", 1, 0, "C");
							$this->Cell($cols1[1], self::LINE_HEIGHT, "Sub Total", 1, 0, "C");
							$this->Cell($cols1[2], self::LINE_HEIGHT, $subpeice, 1, 0, "C");
							$this->Cell($cols1[3], self::LINE_HEIGHT, $subweight, 1, 0, "C");
							
							$subweight = "0";
							$subpeice = "0";
						}
						elseif(count($countrylist) == $j)
						{
							$y += (1 *self::LINE_HEIGHT);
							$this->setXY ($left, $y);
							$this->SetFont('times','B',9);
							$this->Cell($cols1[0], self::LINE_HEIGHT, "", 1, 0, "C");
							$this->Cell($cols1[1], self::LINE_HEIGHT, "Sub Total", 1, 0, "C"); 
							$this->Cell($cols1[2], self::LINE_HEIGHT, $subpeice, 1, 0, "C");
							$this->Cell($cols1[3], self::LINE_HEIGHT, $subweight, 1, 0, "C");
						}
						
						if(count($countrylist)> $j && $countrylist[$j]->getAccount() != $countrylist[$i]->getAccount())
						{
							//echo "mruga";
							$y += (1 *self::LINE_HEIGHT);
							$this->setXY ($left, $y);
							$this->SetFont('times','B',9);
							$this->Cell($cols1[0], self::LINE_HEIGHT, "", 1, 0, "C");
							$this->Cell($cols1[1], self::LINE_HEIGHT, "Total", 1, 0, "C");
							$this->Cell($cols1[2], self::LINE_HEIGHT, $totalPieces, 1, 0, "C");
							$this->Cell($cols1[3], self::LINE_HEIGHT, $totalWeight, 1, 0, "C");
							$totalWeight = "0";
							$totalPieces = "0";
						
						}
						elseif(count($countrylist) == $j)
						{
							$y += (1 *self::LINE_HEIGHT);
							$this->setXY ($left, $y);
							$this->SetFont('times','B',9);
							$this->Cell($cols1[0], self::LINE_HEIGHT, "", 1, 0, "C");
							$this->Cell($cols1[1], self::LINE_HEIGHT, "Total", 1, 0, "C");
							$this->Cell($cols1[2], self::LINE_HEIGHT, $totalPieces, 1, 0, "C");
							$this->Cell($cols1[3], self::LINE_HEIGHT, $totalWeight, 1, 0, "C");
							$totalWeight = "0";
							$totalPieces = "0";
						}
						
						$i++;
						
						
						$mwbno =  $csummary->getMawb();	
						$service = $csummary->getHandling();			
						$account =  $csummary->getAccount();
						
			}
			/*
							$y += (1 *self::LINE_HEIGHT);
							$this->setXY ($left, $y);
							$this->SetFont('times','B',9);
							$this->Cell($cols1[0], self::LINE_HEIGHT, "", 1, 0, "C");
							$this->Cell($cols1[1], self::LINE_HEIGHT, "", 1, 0, "C");
							$this->Cell($cols1[2], self::LINE_HEIGHT, $totalWeight, 1, 0, "C");
							$this->Cell($cols1[3], self::LINE_HEIGHT, $totalPieces, 1, 0, "C");
						*/
			
				$y += (1 *self::LINE_HEIGHT);
				$this->setXY ($left, $y);
		}
		
		// country report start
		
		else
		{
			
			$heading = $_GET["carrier"];
			
			if($heading != "")
			{
				if($heading == "RoyalMailIntTrackedAndSigned")
				{
					$heading = "RoyalMailT&S";
				}
				$this->Text(50, 40, "No data for $heading on - " . date("d-m-Y") );
			
			}
			else
			{
				$this->Text(50, 40, "No data on - " . date("d-m-Y") );
			}
			
			
			
		}

  		
			
		
		
	}
   
public function createBookingDocs($consignment_list, $image_folder)
    {
        // get basket       
        $this->consignment_list=$consignment_list;
        $this->image_folder = $image_folder;
        // Need to set any information required for header, before calling add page.
        // - header written by the add page method

        // Order page
        $this->AddPage();
        // show the billing details
       
        // Booking page
        $this->MultiCell(1,5,"\n\n",GlOrderPdf::BORDER_OFF);
        // build booking header
       // $this->setBookingHeader();
    }
}