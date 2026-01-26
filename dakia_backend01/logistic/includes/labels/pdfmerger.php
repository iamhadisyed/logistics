<?php
/**
 *  PDFMerger created by Jarrod Nettles December 2009
 *  jarrod@squarecrow.com
 *
 *  v1.0
 *
 * Class for easily merging PDFs (or specific pages of PDFs) together into one. Output to a file, browser, download, or return as a string.
 * Unfortunately, this class does not preserve many of the enhancements your original PDF might contain. It treats
 * your PDF page as an image and then concatenates them all together.
 *
 * Note that your PDFs are merged in the order that you provide them using the addPDF function, same as the pages.
 * If you put pages 12-14 before 1-5 then 12-15 will be placed first in the output.
 *
 *
 * Uses FPDI 1.3.1 from Setasign
 * Uses FPDF 1.6 by Olivier Plathey with FPDF_TPL extension 1.1.3 by Setasign
 *
 * Both of these packages are free and open source software, bundled with this class for ease of use.
 * They are not modified in any way. PDFMerger has all the limitations of the FPDI package - essentially, it cannot import dynamic content
 * such as form fields, links or page annotations (anything not a part of the page content stream).
 *
 */

    class PDFMerger {

        private $_files; //['form.pdf']  ["1,2,4, 5-19"]
        private $_fpdi;

        /**
         * Merge PDFs.
         * @return void
         */
        public function __construct() {
            require_once(BASE_PATH . 'includes/3rdparty/fpdi/fpdi.php');
        }

        /**
         * Add a PDF for inclusion in the merge with a valid file path. Pages should be formatted: 1,3,6, 12-16.
         * @param $filepath
         * @param $pages
         * @return void
         */
        public function addPDF($filepath, $pages = 'all') {

            if (file_exists($filepath)) {
                if (strtolower($pages) != 'all') {
                    $pages = $this->_rewritepages($pages);
                }

                $this->_files[] = array($filepath, $pages);
            } else {
                throw new exception("Could not locate PDF on '$filepath'");
            }

            return $this;
        }

        /**
         * Merges your provided PDFs and outputs to specified location.
         * @param $outputmode
         * @param $outputname
         * @return PDF
         */
        public function merge($outputmode = 'browser', $outputpath = 'newfile.pdf',$watermarkText ="") {
            if (!isset($this->_files) || !is_array($this->_files)): throw new exception("No PDFs to merge.");
            endif;

            $fpdi = new FPDI();

            $fpdi->SetPrintHeader(false);
            $fpdi->SetPrintFooter(false);

            $fpdi->SetHeaderMargin(0);
            $fpdi->SetFooterMargin(0);
            $fpdi->SetAutoPageBreak(false, 0);


            //merger operations
            foreach ($this->_files as $file) {
                $filename = $file[0];
                $filepages = $file[1];

                $count = $fpdi->setSourceFile($filename);

                //add the pages
                if ($filepages == 'all') {
                    for ($i = 1; $i <= $count; $i++) {
                        $template = $fpdi->importPage($i);
                        $size = $fpdi->getTemplateSize($template);

                        // $fpdi->addPage('P', array(100,160));
                        //$fpdi->useTemplate($template,3, 3, 93);
                        $fpdi->AddPage((($size['h'] > $size['w'] ) ? 'P' : 'L'), array($size['w'], $size['h']));
                        $fpdi->useTemplate($template);
                        if(!empty($watermarkText)){
                            $fpdi->StartTransform();
                            $fpdi->Rotate(-45);
                            $img_file = "../images/onhold_watermark_red.png";
                            $fpdi->SetAlpha(0.5);
                            $fpdi->Image($img_file, 40, 0, 100, 70, '', '', '', false, 300, '', false, false, 0);
                            $fpdi->SetAlpha(1);
                            $fpdi->StopTransform();
                            /*$fpdi->SetFont ('helvetica', '', '25' , '', 'default', true );
                            
                            
                            $fpdi->SetTextColor(255, 0, 0);
                            $fpdi->Cell(40, 0, $watermarkText, 1, $ln=0, 'C', 0, '', 0, false, 'A', 'B');
                               */
                        }
                    }
                } else {
                    foreach ($filepages as $page) {
                        if (!$template = $fpdi->importPage($page)): throw new exception("Could not load page '$page' in PDF '$filename'. Check that the page exists.");
                        endif;
                        $size = $fpdi->getTemplateSize($template);

                        // $fpdi->addPage('P', array(100,160));
                        //$fpdi->useTemplate($template,3, 3, 93);

                        $fpdi->AddPage((($size['h'] > $size['w'] ) ? 'P' : 'L'), array($size['w'], $size['h']));
                        $fpdi->useTemplate($template);
                        if(!empty($watermarkText)){
                            $fpdi->StartTransform();
                            $fpdi->Rotate(-45);
                            $img_file = "../images/onhold_watermark_red.png";
                            $fpdi->SetAlpha(0.5);
                            $fpdi->Image($img_file, 40, 0, 100, 70, '', '', '', false, 300, '', false, false, 0);
                            $fpdi->SetAlpha(1);
                            $fpdi->StopTransform();
                        }
                    }
                }
            }

            //output operations
            $mode = $this->_switchmode($outputmode);



            if ($mode == 'S') {
                return $fpdi->Output($outputpath, 'S');
            } else {
                if ($fpdi->Output($outputpath, $mode)) {
                    return true;
                } else {
                    //throw new exception("Error outputting PDF to '$outputmode'.");
                    return false;
                }
            }
        }

        /*
         * MERGE DHL LABEL
         */

        public function mergeOrangeConnex($outputmode = 'browser', $outputpath = 'newfile.pdf', $service = '', $con) {


            if (!isset($this->_files) || !is_array($this->_files)): throw new exception("No PDFs to merge.");
            endif;

            $fpdi = new FPDI();



            $fpdi->SetPrintHeader(false);
            $fpdi->SetPrintFooter(false);

            $fpdi->SetHeaderMargin(0);
            $fpdi->SetFooterMargin(0);
            $fpdi->SetAutoPageBreak(false, 0);


            //merger operations
            foreach ($this->_files as $file) {
                $filename = $file[0];
                $filepages = $file[1];

                $count = $fpdi->setSourceFile($filename);

                //add the pages
                if ($filepages == 'all') {
                    for ($i = 1; $i <= $count; $i++) {
                        $template = $fpdi->importPage($i);
                        $size = $fpdi->getTemplateSize($template);

                        $fpdi->AddPage('P', array(100, 150));
                        $fpdi->useTemplate($template,1, 0, 96, 150, true);
                        $fpdi->IncludeJS("print();");

                        
                    }
                }
                else {

                    foreach ($filepages as $page) {
                        if (!$template = $fpdi->importPage($page)): throw new exception("Could not load page '$page' in PDF '$filename'. Check that the page exists.");
                        endif;
                        $size = $fpdi->getTemplateSize($template);

                        $fpdi->AddPage('P', array(100, 150));
                        $fpdi->useTemplate($template,1, 0, 96, 150, true);
                        $fpdi->IncludeJS("print();");

                        
                    }
                }
            }

            //output operations
            $mode = $this->_switchmode($outputmode);



            if ($mode == 'S') {
                return $fpdi->Output($outputpath, 'S');
            } else {
                if ($fpdi->Output($outputpath, $mode)) {
                    return true;
                } else {
                    //throw new exception("Error outputting PDF to '$outputmode'.");
                    return false;
                }
            }
        }

        public function mergeDhl($outputmode = 'browser', $outputpath = 'newfile.pdf', $service = '', $con) {


            if (!isset($this->_files) || !is_array($this->_files)): throw new exception("No PDFs to merge.");
            endif;

            $fpdi = new FPDI();



            $fpdi->SetPrintHeader(false);
            $fpdi->SetPrintFooter(false);

            $fpdi->SetHeaderMargin(0);
            $fpdi->SetFooterMargin(0);
            $fpdi->SetAutoPageBreak(false, 0);


            //merger operations
            foreach ($this->_files as $file) {
                $filename = $file[0];
                $filepages = $file[1];

                $count = $fpdi->setSourceFile($filename);

                //add the pages
                if ($filepages == 'all') {
                    for ($i = 1; $i <= $count; $i++) {
                        $template = $fpdi->importPage($i);
                        $size = $fpdi->getTemplateSize($template);

                        $fpdi->addPage('P', array(80, 150));
                        $fpdi->useTemplate($template, -20, -3, 230);

                        if ($count != $i && (strtolower($con->getItemType()) == "lithium ion battery" || strtolower($con->getItemType()) == "lithium metal battery"))
                            $fpdi->Image('../images/battery/battery.jpg', 6, 67, 30, 12);

                        /* $fpdi->AddPage('P', array($size['w'], $size['h']));
                          $fpdi->useTemplate($template); */
                    }
                }
                else {

                    foreach ($filepages as $page) {
                        if (!$template = $fpdi->importPage($page)): throw new exception("Could not load page '$page' in PDF '$filename'. Check that the page exists.");
                        endif;
                        $size = $fpdi->getTemplateSize($template);

                        $fpdi->addPage('P', array(140, 160));
                        $fpdi->useTemplate($template, 3, -10, 93);

                        /* $fpdi->AddPage('P', array($size['w'], $size['h']));
                          $fpdi->useTemplate($template); */
                    }
                }
            }

            //output operations
            $mode = $this->_switchmode($outputmode);



            if ($mode == 'S') {
                return $fpdi->Output($outputpath, 'S');
            } else {
                if ($fpdi->Output($outputpath, $mode)) {
                    return true;
                } else {
                    //throw new exception("Error outputting PDF to '$outputmode'.");
                    return false;
                }
            }
        }

    /*
         * MERGE HUXLOE HERMES
         */
        
        public function mergeHuxloe($outputmode = 'browser', $outputpath = 'newfile.pdf', $service = '', $con) {

            $country = new Country($con->getCountryId());
            if (!isset($this->_files) || !is_array($this->_files)): throw new exception("No PDFs to merge.");
            endif;

            $fpdi = new FPDI();

         

            $fpdi->SetPrintHeader(false);
            $fpdi->SetPrintFooter(false);

            $fpdi->SetHeaderMargin(0);
            $fpdi->SetFooterMargin(0);
            $fpdi->SetAutoPageBreak(false, 0);


            //merger operations
            foreach ($this->_files as $file) {
                $filename = $file[0];
                $filepages = $file[1];

                $count = $fpdi->setSourceFile($filename);

                //add the pages
                if ($filepages == 'all') {
                    for ($i = 1; $i <= $count; $i++) {
                        $template = $fpdi->importPage($i);
                        $size = $fpdi->getTemplateSize($template);

                        $fpdi->addPage('P', array(100, 150));
                        $fpdi->useTemplate($template, 1, 1, 98,148);

                        $fpdi->SetFont('Helvetica');
                        $fpdi->SetFillColor(255, 255, 255);
                        $fpdi->Rect(1, 1, 50, 5, 'F' );
                        if($country->getIso() == "DE" || $country->getIso() == "AT" || $country->getIso() == "IT" )
                            $fpdi->Rect(19, 60, 35, 4, 'F');

                        /* $fpdi->AddPage('P', array($size['w'], $size['h']));
                          $fpdi->useTemplate($template); */
                    }
                }
            }

            //output operations
            $mode = $this->_switchmode($outputmode);



            if ($mode == 'S') {
                return $fpdi->Output($outputpath, 'S');
            } else {
                if ($fpdi->Output($outputpath, $mode)) {
                    return true;
                } else {
                    //throw new exception("Error outputting PDF to '$outputmode'.");
                    return false;
                }
            }
        }
    
        /*
         * MERGE SFEXPRESS
         */

        public function mergeDhlSing($outputmode = 'browser', $outputpath = 'newfile.pdf', $service = '', $con) {


            if (!isset($this->_files) || !is_array($this->_files)): throw new exception("No PDFs to merge.");
            endif;

            //return "test";

            $fpdi = new FPDI();



            $fpdi->SetPrintHeader(false);
            $fpdi->SetPrintFooter(false);

            $fpdi->SetHeaderMargin(0);
            $fpdi->SetFooterMargin(0);
            $fpdi->SetAutoPageBreak(false, 0);


            //merger operations
            foreach ($this->_files as $file) {
                $filename = $file[0];
                $filepages = $file[1];

                $count = $fpdi->setSourceFile($filename);

                //add the pages
                if ($filepages == 'all') {
                    for ($i = 1; $i <= $count; $i++) {
                        $template = $fpdi->importPage($i);
                        $size = $fpdi->getTemplateSize($template);

                        // $fpdi->addPage('P', array(100,160));
                        //$fpdi->useTemplate($template,3, 3, 93);
                        //echo "kazim";
                        $fpdi->addPage('P', array(90, 150));
                        $fpdi->useTemplate($template, -10, 8, 220);

                        if ($count != $i && (strtolower($con->getItemType()) == "lithium ion battery" || strtolower($con->getItemType()) == "lithium metal battery"))
                            $fpdi->Image('../images/battery/battery.jpg', 6, 67, 30, 12);

                        /* $fpdi->AddPage('P', array($size['w'], $size['h']));
                          $fpdi->useTemplate($template); */
                    }
                }
                else {

                    foreach ($filepages as $page) {
                        if (!$template = $fpdi->importPage($page)): throw new exception("Could not load page '$page' in PDF '$filename'. Check that the page exists.");
                        endif;
                        $size = $fpdi->getTemplateSize($template);

                        $fpdi->addPage('P', array(140, 160));
                        $fpdi->useTemplate($template, 3, -10, 93);

                        /* $fpdi->AddPage('P', array($size['w'], $size['h']));
                          $fpdi->useTemplate($template); */
                    }
                }
            }

            //output operations
            $mode = $this->_switchmode($outputmode);



            if ($mode == 'S') {
                return $fpdi->Output($outputpath, 'S');
            } else {
                if ($fpdi->Output($outputpath, $mode)) {
                    return true;
                } else {
                    //throw new exception("Error outputting PDF to '$outputmode'.");
                    return false;
                }
            }
        }

        /**
         * FPDI uses single characters for specifying the output location. Change our more descriptive string into proper format.
         * @param $mode
         * @return Character
         */
        private function _switchmode($mode) {
            switch (strtolower($mode)) {
                case 'download':
                    return 'D';
                    break;
                case 'browser':
                    return 'I';
                    break;
                case 'file':
                    return 'F';
                    break;
                case 'string':
                    return 'S';
                    break;
                default:
                    return 'I';
                    break;
            }
        }

        /**
         * Takes our provided pages in the form of 1,3,4,16-50 and creates an array of all pages
         * @param $pages
         * @return unknown_type
         */
        private function _rewritepages($pages) {
            $pages = str_replace(' ', '', $pages);
            $part = explode(',', $pages);

            //parse hyphens
            foreach ($part as $i) {
                $ind = explode('-', $i);

                if (count($ind) == 2) {
                    $x = $ind[0]; //start page
                    $y = $ind[1]; //end page

                    if ($x > $y): throw new exception("Starting page, '$x' is greater than ending page '$y'.");
                        return false;
                    endif;

                    //add middle pages
                    while ($x <= $y): $newpages[] = (int) $x;
                        $x++;
                    endwhile;
                }
                else {
                    $newpages[] = (int) $ind[0];
                }
            }

            return $newpages;
        }

    }
