<?php
//http://localhost/tshirtws/api.php?reqmethod=generatePDF&apikey=A610%5EGx%7B!%3D3l%23%23i*905Q&refid=10

//============================================================+
// File name   : example_058.php
// Begin       : 2010-04-22
// Last Update : 2013-05-14
//
// Description : Example 058 for TCPDF class
//               SVG Image
//
// Author: Nicola Asuni
//
// (c) Copyright:
//               Nicola Asuni
//               Tecnick.com LTD
//               www.tecnick.com
//               info@tecnick.com
//============================================================+

/**
 * Creates an example PDF TEST document using TCPDF
 * @package com.tecnick.tcpdf
 * @abstract TCPDF - Example: SVG Image
 * @author Nicola Asuni
 * @since 2010-05-02
 */

// Include the main TCPDF library (search for installation path).
require_once('tcpdf/tcpdf_include.php');

// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {

    //Page header
    public function Header() {
        // Logo
        //$image_file = K_PATH_IMAGES.'logo.png';
        //$this->Image($image_file, 10, 10, 15, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        // Set font
        $this->SetFont('helvetica', 'B', 18);
        // Title
        $this->Cell(0, 15, 'Print Order', 0, false, 'C', 0, '', 0, false, 'M', 'M');
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

class generatePDF{
    

    private $pdf = NULL;
    private $x=15;
    private $y=0;
    private $xGap=0;
    private $yGap=40;
    private $fileName='order.pdf';
    private $exportType='I'; //D
    private $pageNo=0;         
    public function __construct(){            
            //$this->initObject();
        // create new PDF document
        //$this->pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $this->pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    }
                
    public function initDPF(){

        // set document information
        $this->pdf->SetCreator(PDF_CREATOR);
        $this->pdf->SetAuthor('Designer Tool');
        $this->pdf->SetTitle('HTML5 Designer');
        $this->pdf->SetSubject('Print Order');
        $this->pdf->SetKeywords('HTML5, Designer, Product, Print, Order');
    }

    public function setupPDFHeader(){

        // set default header data
        $this->pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
    }

    public function setupConfig(){

        // set header and footer fonts
        $this->pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $this->pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $this->pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $this->pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $this->pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $this->pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        $this->pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $this->pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
                require_once(dirname(__FILE__).'/lang/eng.php');
                $this->pdf->setLanguageArray($l);
        }

        // ---------------------------------------------------------

        
    }

    public function setFont($fontPath){
        //$fontname = TCPDF_FONTS::addTTFfont('fonts/Vinsdojo.ttf', 'TrueTypeUnicode', '', 32);       
        if(file_exists($fontPath)){
            $fontname=TCPDF_FONTS::addTTFfont($fontPath, '', '', 32);
            $this->pdf->SetFont($fontname, '', 14, '', false);
        }
        else {
            //echo $fontPath;
        }
        
    }
    public function beginPDFPage(){
        // add a page
        $this->pdf->AddPage();
        
        $this->pdf->setPrintHeader(false);
    }

	public function setupFirstPage($order, $orderId, $itemId, $previewImageList, $printType)
	{
		$customerMiddleName = $order['customer_middlename'];
		if(!$customerMiddleName)
			$customerMiddleName='';
		else
			$customerMiddleName .= ' ';
		
		$customerName = $order['customer_firstname']. ' '.$customerMiddleName.$order['customer_lastname'];
		$email = $order['customer_email'];
		
		$telephone = $order['shipping_address']['telephone'];
		
		$shippingAddress = $order['shipping_address']['street'];
		$shippingAddress .= ','.$order['shipping_address']['postcode'];
		$shippingAddress .= '<br/> &nbsp; '.$order['shipping_address']['city'];
		$shippingAddress .= '<br/> &nbsp; '.$order['shipping_address']['country_id'];
		//$currency = $order['currency_code'];	
								
		foreach ($order['items'] as $key=>$items)
		{
			if($items['item_id']==$itemId)
			{      
				//$itemId = $items['item_id'];	
				$orderDate = $items['created_at'];	
				$productId = $items['product_id'];	
				$productOptions = $items['product_options'];	

				$productOptions = unserialize($productOptions);
				$qty = $productOptions['info_buyRequest']['qty'];	
				$customPrice = $productOptions['info_buyRequest']['custom_price'];	
				$refid = $productOptions['info_buyRequest']['custom_design'];	

				$attributesList = $productOptions['attributes_info'];	
				$productName = $productOptions['simple_name'];

				foreach ($attributesList as $key=>$value)
				{							
					if($value['label']=="Size")
						$size=$value['value'];
					if($value['label']=="Color" || $value['label']=="Colors")
						$color1=$value['value'];
					if($value['label']=="Colors2" || $value['label']=="Colors 2" || $value['label']=="Color2" || $value['label']=="Color 2")
						$color2=$value['value'];							
				}			
				break;
			}
			
		}
				
		// add a page
		//$this->pdf->AddPage();		
		$html = '<table width="100%" cellpadding="5">'
				. '<tr  bgcolor="#212122" color="#fff"><td colspan="3"> <b>'.$customerName.'</b> </td> <td> Order # '.$orderId.' </td> </tr>'
				. '</table>';
		$this->pdf->writeHTML($html, true, false, true, false, '');

		$html = '<table width="100%" cellpadding="5">'
				. '<tr><td colspan="3"> <b>Contact Details </b> </td> <td> Order date:</td><td rowspan="2"> '.$orderDate.' </td> </tr>'
				. '<tr><td colspan="3"> Tel: '.$telephone.' </td> <td> &nbsp; </td>  </tr>'
				. '<tr><td colspan="3"> Email: '.$email.' </td> <td> &nbsp;</td><td> &nbsp; </td> </tr>'
				. '<tr><td colspan="3" > <b>SHIPPING ADDRESS </b> </td> <td> &nbsp;</td><td> &nbsp; </td> </tr>'
				. '<tr><td colspan="3" rowspan="2"> '.$shippingAddress.' </td> <td> &nbsp;</td><td> &nbsp; </td> </tr>'
				. '<tr>  <td> &nbsp;</td><td> &nbsp; </td> </tr>'
				. '</table>';
		$this->pdf->writeHTML($html, true, false, true, false, '');
		// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

		$html="<b>PRODUCT INFORMATION</b>";
		$this->pdf->writeHTML($html, true, false, true, false, '');

		//Add Item Details	
		
		
		$imageTitleHtml = '<tr>';
		$imageHtml = '<tr>';
		foreach ($previewImageList as $k=>$v)
		{							
			$imageHtml .= '<td> <img src="'.$v['image'].'" alt="'.$v['side'].'" width="150" height="150" border="0" />  </td> ';
			$imageTitleHtml .= '<td align="center"> '.$v['side'].'  </td> ';
		}
		$imageHtml .= '</tr>';
		$imageTitleHtml .= '</tr>';
		
		$html =
				'<table width="100%" cellpadding="0" border="1">'
				. '<tr><td>'
					. '<table width="100%" cellpadding="5">'
					. '<tr  bgcolor="#797779" color="#fff"><td> <b>ITEM:</b> '.$itemId.' | <b> PRODUCT ID:</b>'.$productId.' | '.$productName.' </td> </tr>'
					. '</table>'
				. ' </td></tr>'
			 
				. '<tr><td>'
					. '<table width="100%" cellpadding="5" border="0">'
					. '<tr><td> <b>Size:</b> '.$size.' </td>  <td rowspan="3">'
								. ''.$color1.'<br/>'
							. '<table bgcolor="'.$color1.'" width="40" height="40" border="1"> <tr> <td> &nbsp; <br/> </td></tr> </table> '
						. '</td> <td rowspan="3"> '
								. ''.$color2.'<br/>'
							. '<table bgcolor="'.$color2.'" width="40" height="40" border="1"> <tr> <td> &nbsp; <br/> </td></tr> </table> '
						. ' </td> '
					. '</tr>'
					. '<tr><td> <b>Color:</b> '.$color1.' </td> </tr>'
					. '<tr><td> <b>Color 2:</b> '.$color2.' </td> </tr>'
					. '<tr><td> <b>Print Type:</b> '.$printType.' </td> </tr>'
					. '</table>'
				. ' </td></tr>'
				 . '<tr><td>'
					. '<table width="100%" cellpadding="5">'
					. $imageTitleHtml	
					. $imageHtml
					. '</table>'
				. ' </td></tr>'
				. '</table>';
		//  . '<tr><td> <b>Size:</b> '.$size.' </td>  <td rowspan="3"> <div style="background-color:'.$colors.';width:40px;height:40px"> &nbsp; </div> </td> <td> &nbsp; </td> </tr>'

		$this->pdf->writeHTML($html, true, false, true, false, '');


		// reset pointer to the last page
		$this->pdf->lastPage();
		$this->pdf->AddPage();
	}
    
    public function appendToPDF($svgdata, $side='', $x=15, $y=30, $xGap=0, $yGap=20){

       // $this->pdf->ImageSVG($file='images/test1.svg', $x=15, $y=30, $w='', $h='', $link='http://www.tcpdf.org', $align='', $palign='', $border=1, $fitonpage=false);

       // $this->pdf->ImageSVG($file=$svgdata, $x=15, $y=30, $w='', $h='', $link='http://www.tcpdf.org', $align='', $palign='', $border=1, $fitonpage=false);
       
        /*
        $this->x = $x;       
        if($this->y==0)
            $gap=0;
        
        $this->y = $this->y +  $y;
        //echo '<br/>'.$this->y;
         */
        
        $gap = $this->yGap;
        $this->x = 10;
        $this->y = 2;
        
        if($this->pdf->PageNo()==1){
            $this->y = 30;
        }
        $this->pageNo++;
        if($this->pageNo>1)
            $this->pdf->AddPage();
        else
            $this->pdf->SetY(40);
       
        //echo '$this->pdf->PageNo() = '.$this->pdf->PageNo();
        $this->pdf->SetFont('helvetica', 'B', 14);
        $text = ''.$side.':';
        $this->pdf->Write(0, $text, '', 0, 'L', true, 0, false, false, 0);
        $this->pdf->ImageSVG($file=$svgdata, $this->x, $this->y + $gap, $w='110%', $h='110%', $link='', $align='', $palign='', $border=0, $fitonpage=false);

        
        //$this->pdf->SetPrintHeader(false);
    }

    public function postProcessPDF(){

        // NOTE: Uncomment the following line to rasterize SVG image using the ImageMagick library.
        //$this->pdf->setRasterizeVectorImages(true);

        //$this->pdf->ImageSVG($file='images/tux.svg', $x=30, $y=100, $w='', $h=100, $link='', $align='', $palign='', $border=0, $fitonpage=false);

        $this->pdf->SetFont('helvetica', '', 8);
        //$this->pdf->SetY(10);
        $txt = '© HTML5 Product Designer, Print Order Page.';
       // $this->pdf->Write(0, $txt, '', 0, 'L', true, 0, false, false, 0);
    }

    public function closePDF(){
        //Close and output PDF document
        //$this->pdf->Output('example.pdf', 'D');
        $this->pdf->Output($this->fileName, $this->exportType);
    }


    public function startPDF($fileName='order.pdf', $exportType='D'){

        $this->fileName = $fileName;
        $this->exportType = $exportType;

        $this->initDPF();
        $this->setupPDFHeader();
        $this->setupConfig();    
        $this->beginPDFPage();
    }

    //function appendToPDF($..){}

    public function endPDF(){
        $this->postProcessPDF();
        $this->closePDF();
    }

}
// ---------------------------------------------------------


//============================================================+
// END OF FILE
//============================================================+
