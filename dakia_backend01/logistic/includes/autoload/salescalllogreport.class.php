<?php
ob_start();
class SalesCallLogReport extends TFPDF
{
    const SERVICE_LIST_X = 10;
	const SERVICE_LIST_Y = 100;
	const LINE_HEIGHT = 5;
	const LEFT_MARGIN = 30;

	const ROWS_FIRST_PAGE = 1; //23;
	const ROWS_PAGE = 40;

	const START_Y = 50;
	const END_Y = 260;

	private $pdf;
	
    private $pageNumber=1;
	private $firstpager=0;


    private $show_main_header_flag = true;

    /**
     * Generate full pdf
     */
    public static function buildPDFDocuments($from_date, $to_date, $account)
    {
     
	 // $mawbno = '618-97316155';
	   
	   $PdfObj = new SalesCallLogReport('P','mm','A4');
       $PdfObj->createBookingDocs('', "../images/");
	   $PdfObj->addSummaryAccount(self::START_Y, true, $from_date, $to_date, $account);	
      
	
		return $PdfObj->Output("../_assets/sales_report/".time()."sale_log_report.pdf", "I");	  
	  
	  
    }

    private function addSummaryAccount($y, $new_page = true, $from_date, $to_date, $account) 
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
		 $sessionUser = SessionManager::getUser();
		$SalesCallLogFilter = new SalesCallLogFilter();
		$SalesCallLogFilter->addFieldFilter("userid", $sessionUser->getId());	
		if($account != '')
			$SalesCallLogFilter->addFieldFilter("customer_code", $account);	
		if($from_date != '' && $to_date != '')
			$SalesCallLogFilter->addFilter('date_call >= "'.date("Y-m-d",strtotime($from_date)).'" AND date_call <= "'. date("Y-m-d",strtotime($to_date)).'"');
		
		$SalesCallLogFilter->addgGroupBy("date_call");
		$list = $SalesCallLogFilter->getColumnList('count(*) as id, date_call ');	
	
		if(count($list) > 0)
		{
			$this->SetFont('times', 'BI', 10, '', 'false');
		
		
			$this->Text(50, 40, "*************     SALES CALL SUMMARY REPORT     *************");
			
			$this->SetFont('times', '', 8, '', 'false');
		
			
	
			$left = self::LEFT_MARGIN ;
			//
			$cols = array (50, 100);
			$cols1 = array(50, 100);
			$cols2 = array(50, 50, 50);
			$x_end = $left;
			for ($i=0; $i < sizeof($cols); $i++) $x_end += $cols[$i];
	
			
			$y = (10 * self::LINE_HEIGHT);
			
			
			$y += (1 * self::LINE_HEIGHT);
			
			$this->setXY ($left, $y);
			
			$this->SetFont('times', 'B', 8);
			//
			
			$this->Cell($cols[0], self::LINE_HEIGHT, "DATE", 1, 0, "C");
			$this->Cell($cols[1], self::LINE_HEIGHT, "TOTAL CALLS", 1, 2, "C");
			
			$total_call = 0;
			$count = 0;
			foreach ($list as $value) {
			
				$count++;
				$total_call += $value->getId();
				$y += (1 * self::LINE_HEIGHT);
				if($y>=260)
				{
					$y = 25;
					$this->AddPage();
				}
				$this->setXY ($left, $y);
				$this->SetFont('times', '', 8, '', 'false');	
				
			
				
				$this->SetFont('times','',9);
				$this->Cell($cols[0], self::LINE_HEIGHT, date("d-m-Y",$value->getDateCall()), 1, 0, "C");
				$this->Cell($cols[1], self::LINE_HEIGHT, $value->getId(), 1, 0, "C");
				
				if($count == 1)
				{
					$y += (1 *self::LINE_HEIGHT);
						
					$this->setXY ($left, $y);
					$this->Cell($cols2[0], self::LINE_HEIGHT, "", 1, 0, "C");
					$this->Cell($cols2[1], self::LINE_HEIGHT, "CUSTOMER NAME", 1, 0, "C");
					$this->Cell($cols2[2], self::LINE_HEIGHT, "CUSTOMER TOTAL CALL", 1, 0, "C");
				}
				
				$SalesCallLogFilter = new SalesCallLogFilter();
				$SalesCallLogFilter->addFieldFilter("userid", $sessionUser->getId());	
				if($account != '')
					$SalesCallLogFilter->addFieldFilter("customer_code", $account);	
				$SalesCallLogFilter->addFieldFilter("date_call", date("Y-m-d",$value->getDateCall()));	
				$SalesCallLogFilter->addgGroupBy("customer_code");
				$account_wise_list = $SalesCallLogFilter->getColumnList('count(*) as id, customer_code ');	
				
				if(count($account_wise_list) > 0)
				{
					foreach($account_wise_list as $loglist)
					{
						$y += (1 * self::LINE_HEIGHT);
						
						$this->setXY (self::LEFT_MARGIN, $y);
						
						$this->Cell($cols2[0], self::LINE_HEIGHT, "", 1, 0, "C");
						$this->Cell($cols2[1], self::LINE_HEIGHT, $loglist->getCustomerCode(), 1, 0, "C");	
						$this->Cell($cols2[2], self::LINE_HEIGHT, $loglist->getId(), 1, 2, "C");
						
					}
				}
							
		}
		$this->SetFont('times', 'B', 8, '', 'false');	
		$left = self::LEFT_MARGIN;
		$this->setXY ($left, $y+5);
		$this->Cell($cols1[0], self::LINE_HEIGHT, "Total Calls", 1, 0, "C");
		$this->Cell($cols1[1], self::LINE_HEIGHT, $total_call, 1, 2, "C");
		
		}
		else
		{
			$this->SetFont('times', 'BI', 10, '', 'false');
		
		
			$this->Text(50, 40, "NO RECORDS FOUND");
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