<?php

/**
 * Custom PDF implementation
 *
 * @author Gerd Riesselmann
 */

$fpdf_dir = dirname(__FILE__) . '/../../3rdparty/fpdf/';
$fpdi_dir = dirname(__FILE__) . '/../../3rdparty/fpdi/src/';
if (!defined('FPDF_FONTPATH')) {
	define('FPDF_FONTPATH', $fpdf_dir . 'font/');
} 
require_once $fpdf_dir . 'fpdf.php';
require_once $fpdi_dir . 'autoload.php';

use setasign\Fpdi;
 
class PDFMaker extends Fpdi\Fpdi
{
	private $text;
	private $template;
	private $template_pagecount = 0;
	private $filename;
	
	private $style = array();
	private $cell = false;
	private $skipFirstLF = false;

	private $small_font_size = 6;
	private $default_font_size = 9;

	/* @var Status */
	private $error;

	public function __construct($text, $filename, $template = "") {
		$this->error = new Status();
		$this->text = $text;
		$this->filename = $filename;
		$this->template = $template;
		if (!empty($this->template)) {
			$this->template_pagecount = $this->setSourceFile($this->template);
		}
				
		parent::__construct("P", "mm", "A4");
	}
	
	/**
	 * Creates the PDF and stores it under given filename
	 *
	 * @param Integer Text start top in mm
	 * @param Integer Text left in mm
	 * @param Integer Text bottom in mm
	 *
	 * @return Status
	 */
	public function create($top = 10, $left = 10, $bottom = 20) {
		$this->AddPage();
		$this->SetMargins($left, $top, $left);
		$this->SetAutoPageBreak(true, $bottom);
		$this->SetFont("helvetica", "", $this->default_font_size);
		$this->SetXY($left, $top);
		$this->writeFormatted(5, $this->text);
		$this->Output($this->filename, "F");

		return $this->error;
	}

	public function setDefaultFontSize($size) {
		$this->default_font_size = $size;
	}
	public function setSmallFontSize($size) {
		$this->small_font_size = $size;
	}

	public function Error($msg) {
		$this->error->append($msg);
	}

	public function Footer() {
		$page_no = $this->PageNo();
		$tpl_page = 0;
		if ($page_no <= $this->template_pagecount) {
			$tpl_page = $page_no;
		}
		else if ($this->template_pagecount > 0) {
			$tpl_page = $this->template_pagecount;
		}
		if ($tpl_page) {
			$tplidx = $this->ImportPage($tpl_page);
			$this->useTemplate($tplidx);
		}
	}
	
	private function writeFormatted($lineHeight, $text) {
		$arrText = preg_split('/\<(.*)\>/U', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
		foreach($arrText as $index => $content) {
			if($index % 2 == 0) {
				if ($this->cell !== false) {
					$arrCell = array(
						"w" => 0,
						"h" => 5,
						"border" => 0,
						"align" => "L",
						"fill" => 0
					);
				                 
					Arr::clean($arrCell, $this->cell);
					
					//Text
					$this->Cell(
						$arrCell["w"],
						$arrCell["h"],
						$content,
					 	 $arrCell["border"],
					  	0,
					  	$arrCell["align"],
					  	$arrCell["fill"]
					);
				}
				else {
					if (false && $this->skipFirstLF === true && strlen($content) > 0) {
						if (strpos($content, "\r\n") === 0) {
							$content = substr($content, 2);
						}
						else if(strpos($content, "\n") === 0) {
							$content = substr($content, 1);
						}
						$this->skipFirstLF = false;
					}

					$this->Write(5, $content);
				}
      		}
    		else {
				//Tags
				if($content{0} == '/') {
					$this->closeTag(strtoupper(substr($content,1)));
				}
				else {
					//Extract attributes
					$arrAttrs = explode(' ', $content);
					$tag = strtoupper(array_shift($arrAttrs));
					$arrAttrValues = array();
					foreach($arrAttrs as $attrExpression) {
						// Extracts a=b from either a=b or a="b" or a='b'
						if ( preg_match('|^([^=]*)=["\']?([^"\']*)["\']?$|', $attrExpression, $temp)) {
							$arrAttrValues[strtoupper($temp[1])] = $temp[2];
						}
					}
					$this->openTag($tag, $arrAttrValues);
				}
			}
		}
	}
	
	private function openTag($tag, &$arrAttributes) {
		switch ($tag) {
			case "B":
			case "I":
			case "U":
				$this->setStyle($tag, 1);
				break;
			case "SMALL":
				$this->SetFontSize($this->small_font_size);
				break;
			case "CELL":
				$this->cell = array();
				foreach($arrAttributes as $name => $value) {
					$this->cell[strtolower($name)] = strtoupper($value);
				}
				break;
		}
	}
	
	private function closeTag($tag) {
		switch ($tag) {
			case "B":
			case "I":
			case "U":
				$this->setStyle($tag, 0);
				break;
			case "SMALL":
				$this->SetFontSize($this->default_font_size);
				break;
			case "CELL":
				$this->cell = false;
				$this->skipFirstLF = true;
				break;
		}
	}
	
	private function setStyle($tag, $enable) {
		//Modify style and select corresponding font
		if (array_key_exists($tag, $this->style) == false) {
			$this->style[$tag] = 0;
		}
			
		$this->style[$tag] += ($enable ? 1 : -1);
		$style='';
		foreach($this->style as $s => $count) {
			if($count > 0) {
				$style.=$s;
			}
		}
		$this->SetFont('',$style);
	}
}