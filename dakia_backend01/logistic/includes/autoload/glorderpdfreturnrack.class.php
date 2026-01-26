<?php
error_reporting(1);
ini_set('display_errors', 1);
ob_start();
class GlOrderPdfReturnRack
{
   
   // private $pageNumber=1;
	private $pdf;
	private $firstpager;
	private $font;


    private $show_main_header_flag = true;
	
	public function __construct()
		{
			$pdf = new PdfBase(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
			$this->pdf = $pdf;
		}
		
		
	    public function buildPDFDocuments() {
        
		$this->pdf->SetPrintHeader(false);
		$this->pdf->SetPrintFooter(false);
        $this->pdf->SetFooterMargin(0);
        $this->pdf->SetAutoPageBreak(false, 0);
        
		
		$page_size = array(100.5, 150);
        $this->pdf->AddPage("P", $page_size);
        $this->AddInformation();
        // Output PDF
        $this->pdf->Output("../shelf_labels/".$this->file_name, "F");        
        return SETTING_MAIN_URL . "shelf_labels/".$this->file_name;
        //$this->pdf->Output("../_assets/pdf/".date("Y_m_d').'/'.$consignment->getId().".pdf", "F");
        //return "SUCCESS||"."../_assets/pdf/".date('Y_m_d').'/'.$consignment->getId().".pdf||".$awb . "||RETURN";	
        //$this->pdf->Output("../_assets/pdf/".date('Y_m_d').'/'.$id.".pdf", "F");
        //return "http://oneworldexpress.co.uk/remote/_assets/pdf/".date('Y_m_d').'/'.$id.".pdf";	
    }

    public function AddInformation() {
        
		$user = SessionManager::getUser();
        //make sure the label is not going to new page for no reason.
        $this->pdf->SetAutoPageBreak(false, 0);

        $yobjects = $this->pdf->getY();
        $xobjects = $this->pdf->getX();
        $this->pdf->SetXY($xobjects - 9.75, $yobjects - 9.75);

        //$this->pdf->Image('/var/www/vhosts/oneworldexpress.co.uk/httpdocs/images/logo.jpg', $xobjects - 10, $yobjects - 10, $w = 50, $h = 20);
		
		
		if(trim($user->getLogo() ) != '')
			$image1=realpath("../images/userlogo/".$user->getLogo());
		else
			$image1=realpath("../images/logo.jpg");
			
		
		 $this->pdf->Image($image1, $xobjects - 10, $yobjects - 10, $w = 50, $h = 20);
		
        
        $ft = str_replace(" ", "-",$this->labelInfo['Title']);
        
        // $this->pdf->write1DBarcode($consignment[0]->getAwb(), 'C128', $xobjects ,$yobjects + 15, 120, 20, 0.5	, $this->style, 'Y');
        $this->file_name = $ft."-".time().".pdf";
        
        $this->pdf->SetFont('', 'B', 14);
        $positionY = 20;
        foreach ($this->labelInfo as $key => $value){
            if($key == 'Barcode_Id')
                continue;
            if($key == 'Title'){
                $this->pdf->text($xobjects , $yobjects+10, $value);
            }else{
                $this->pdf->text($xobjects, $yobjects + $positionY, $key.': '.$value);
                $positionY += 10;
            }
        }        
        /*
        $this->pdf->text($xobjects + 40, $yobjects, 'RETURN YOUR PARCEL');
        $this->pdf->text($xobjects, $yobjects + 20, 'ACCOUNT: TESTACCOUNT');
        $this->pdf->text($xobjects, $yobjects + 30, "Location : TESTLOCATION");        
        $this->pdf->text($xobjects, $yobjects + 40, "Date : " . date("d-m-Y h:i"));
        $this->pdf->text($xobjects, $yobjects + 50, "Processed By : " . $user->getUserName());
        */
        $positionY += 10;   
		
		$this->pdf->write1DBarcode($this->labelInfo['TrackingNumber'], 'C128', $x1 + 10, $positionY, '', 15, 90, $this->style, 'N'); // Tracking Barcode	
        $this->pdf->text($xobjects + 10, $yobjects + 110, $this->labelInfo['TrackingNumber']);     
        //HAWB Barcode
        //$this->pdf->write1DBarcode($this->labelInfo['Barcode_Id'], 'C128', $x1 + 10, $positionY, '', 15, 90, $this->style, 'N'); // Tracking Barcode	
        //$this->pdf->text($xobjects + 10, $yobjects + 100, $this->labelInfo['Barcode_Id']);
    }
	
    public function AddLabelInfo($information){
        $this->labelInfo = $information;
    }

	

    /**
     * Generate full pdf
     */
	
	public function getFullPath($fileName)
	{
		$path = "../_assets/pdf/" . date("Y_m_d", time()) . "/";

		if (!file_exists($path)) @mkdir($path, 0777);

		return $path . $fileName;
	}
	

   

}