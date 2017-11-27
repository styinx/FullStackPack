<?php
	require_once(dirname(__FILE__) . "/../../fpdf/fpdf.php");

	class PDF extends FPDF
	{
		public 	$page_w			= 210;
		public 	$page_h			= 297;
		public	$unit			= 'mm';
		public	$orientation	= 'P';
		public	$alignment		= 'L';
		public	$font_family	= 'Arial';
		public	$font_size		= 10;
		public	$font_style		= '';
		public	$font_color		= '';
		public	$margin			= array(5, 5, 5, 5);
		private $header_enabled	= false;
		private $footer_enabled	= false;

		public	$dimension		= array("h_mm"	=> 	297,
										"w_mm"	=>	210,
										"w_pt"	=>	595,
										"h_pt"	=>	842,
										"w_px"	=>	2480,
										"h_px"	=>	3508);
		private $default		= array("unit"			=>	'mm',
										"orientation"	=>	'P',
										"alignment"		=>	'L',
										"font_family"	=> 	'Arial',
										"font_style"	=>	'',
										"font_size"		=>	10,
										"font_color"	=>	'000000',
										"margin"		=>	array(5, 5, 5, 5));

		private $header			= array("x" 		=> 5,
										"y" 		=> 5,
										"values" 	=> array(""),
										"line" 		=> false,
										"fontType" 	=> "Arial",
										"fontStyle" => '',
										"fontSize" 	=> 8);
		private $footer			= array("x" 		=> 5,
										"y" 		=> -5,
										"values" 	=> array(""),
										"line" 		=> false,
										"fontType" 	=> "Arial",
										"fontStyle" => '',
										"fontSize" 	=> 8);

		function __construct()
		{
			parent::__construct();
			$this->init();
		}

		public function init()
		{
			$this->aliasNbPages();
			$this->default();
		}

		public function default()
		{
			$margin = $this->default["margin"];

			$this->setUnit($this->default["unit"]);
			$this->setOrientation($this->default["orientation"]);
			$this->setAlignment($this->default["alignment"]);
			$this->setFontFamily($this->default["font_family"]);
			$this->setFontStyle($this->default["font_style"]);
			$this->setFontSize($this->default["font_size"]);
			$this->setFontColor($this->default["font_color"]);
			$this->setMargin($margin[0], $margin[1], $margin[2], $margin[3]);
		}

		public function setDefault($default)
		{
			//$this->default = $default;
		}

		public function checkDimension()
		{
			if($this->x >= $this->page_w - $this->margin[1])
			{
				$this->x = $this->margin[3];
				$this->y += $this->pt2mm($this->font_size);
			}
			if($this->y >= $this->page_h - $this->margin[2])
			{
				$this->addPage($this->orientation);
				$this->y = $this->margin[0];
			}
		}

		/***
		 ***	Top and Bottom decoration
		 ***/

		/**
		 *	@see setHeader
		 */
		public function Header()
		{
			if($this->header_enabled == true)
			{
				$x = $this->x;
				$y = $this->y;
				$font_family = $this->font_family;
				$font_style = $this->font_style;
				$font_size = $this->font_size;

				$this->setXY($this->header["x"], $this->header["y"]);
				$this->setFontFamily($this->header["fontType"]);
				$this->setFontStyle($this->header["fontStyle"]);
				$this->setFontSize($this->header["fontSize"]);

				$vals = $this->header["values"];

				if(count($vals) != 0)
				{
					$w = ($this->page_w - $this->header["x"]*2) / count($vals);
					$h = $this->pt2mm($this->font_size);
					foreach($vals as $val)
					{
						$this->cell($w, $h, $val, 0, 0, 'C');
					}
				}
				else
				{
					$w = $this->page_w - $this->header["x"]*2;
					$h = $this->pt2mm($this->font_size);
					$this->cell($w, $h, date("l d.m.Y", time()), 0, 0, 'R');
				}

				if($this->header["line"] == true)
				{
					$l_x = $this->header["x"];
					$l_y = $this->header["y"] + $this->pt2mm($this->font_size);
					$this->setLineWidth(0.25);
					$this->line($l_x, $l_y, $this->page_w - $l_x, $l_y);
				}
				$this->setFontFamily($font_family);
				$this->setFontStyle($font_style);
				$this->setFontSize($font_size);
				$this->setXY($x, $y);
			}
		}
		/**
		 *	@see setFooter
		 */
		public function Footer()
		{
			if($this->footer_enabled == true)
			{
				$x = $this->x;
				$y = $this->y;
				$font_family = $this->font_family;
				$font_style = $this->font_style;
				$font_size = $this->font_size;

				$this->setXY($this->footer["x"], $this->footer["y"]);
				$this->setFontFamily($this->footer["fontType"]);
				$this->setFontStyle($this->footer["fontStyle"]);
				$this->setFontSize($this->footer["fontSize"]);

				$vals = $this->footer["values"];

				if($this->footer["line"] == true)
				{
					$this->setLineWidth(0.25);
					$l_x = $this->x;
					$l_y = $this->y - $this->pt2mm($this->font_size);
					$this->line($l_x, $l_y, $this->page_w - $l_x, $l_y);
				}

				if(count($vals) > 0)
				{
					$w = ($this->page_w - $this->footer["x"]*2) / count($vals);
					$h = $this->pt2mm($this->font_size);
					foreach($vals as $val)
					{
						$this->cell($w, $h, $val, 0, 0, 'C');
					}
				}
				else
				{
					$w = $this->page_w - $this->footer["x"]*2;
					$h = $this->pt2mm($this->font_size);
					$this->cell($w, $h, $this->PageNo(), 0, 0, 'C');
					//$this->cell(0, 0, $this->PageNo() . "/{nb}", 0, 0, 'C');
				}
				$this->setFontFamily($font_family);
				$this->setFontStyle($font_style);
				$this->setFontSize($font_size);
				$this->setXY($x, $y);
			}
		}

		/**
		 *	Needs to be called first after constructor if header is desired
		 */
		public function setHeader($x = -1, $y = -1, $vals = array(), $line = false, $type = 'Arial', $style = '', $pt = 8)
		{
			if($x == -1)
				$this->header["x"] = $this->margin[3];
			else
				$this->header["x"] = $x;

			if($y == -1)
				$this->header["y"] = $this->margin[0];
			else
				$this->header["y"] = $y;

			$this->header["values"] = $vals;
			$this->header["line"] = $line;
			$this->header["fontType"] = $type;
			$this->header["fontSize"] = $pt;
			$this->header["fontStyle"] = $style;
			$this->header_enabled = true;
		}

		/**
		 *	Needs to be called first after constructor if footer is desired
		 */
		public function setFooter($x = -1, $y = -1, $vals = array(), $line = false, $type = 'Arial', $style = '', $pt = 8)
		{
			if($x == -1)
				$this->footer["x"] = $this->margin[3];
			else
				$this->footer["x"] = $x;

			if($y == -1)
				$this->footer["y"] = $this->page_h - $this->margin[2];
			else
				$this->footer["y"] = $y;

			$this->footer["values"] = $vals;
			$this->footer["line"] = $line;
			$this->footer["fontType"] = $type;
			$this->footer["fontSize"] = $pt;
			$this->footer["fontStyle"] = $style;
			$this->footer_enabled = true;
		}

		/***
		 ***	Getter and Setter
		 ***/

		public function setUnit($unit)
		{
			if(!in_array(strtolower($unit), array('mm', 'pt', 'px')))
				$unit = $this->default["unit"];

			$this->unit = $unit;
		}

		public function setOrientation($orientation)
		{
			if(!in_array(strtolower($orientation), array('L', 'P')))
				$orientation = $this->default["orientation"];

			$this->orientation = $orientation;
		}

		public function setAlignment($alignment)
		{
			if(!in_array(strtolower($alignment), array('L', 'C', 'R')))
				$alignment = $this->default["alignment"];

			$this->alignment = $alignment;
		}

		public function setFontFamily($name)
		{
			$this->font_family = $name;
			$this->setFont($name, $this->font_style, $this->font_size);
		}

		public function setFontStyle($style)
		{
			if(!in_array(strtolower($style), array('', 'b', 'i')))
				$style = $this->default["font_style"];

			$this->font_style = $style;
			$this->setFont($this->font_family, $style, $this->font_size);
		}

		public function setFontSize($pt)
		{
			if(!(is_numeric($pt) || $pt < 1))
				$pt = $this->default["font_size"];

			$this->font_size = $pt;
			$this->setFont($this->font_family, $this->font_style, $pt);
		}

		public function setFontColor($color)
		{
			if(!preg_match('#[0-9a-f]{6}#', strtolower($color)))
				$color = $this->default["font_color"];

			$this->font_color = $color;
			$rgb = $this->rgb($color);
			$this->setTextColor($rgb[0], $rgb[1], $rgb[2]);
		}

		public function setMargin($t, $r, $b, $l)
		{
			$this->margin = array($t, $r, $b, $l);
			$this->setMargins($l, $t, $r);
		}

		public function addX($val)
		{
			$this->x += $val;

			return $this->x;
		}

		public function addY($val)
		{
			$this->y += $val;

			return $this->y;
		}

		public function addXY($x, $y)
		{
			$this->addX($x);
			$this->addY($y);

			return array($this->x, $this->y);
		}

		/***
		 ***	Smart Functions
		 ***/
		function multiList($values)
		{
			$this->setXY(50, 50);

			foreach($values as $key => $value)
			{
				$this->cell(0, 6, $value, 0, 1);
			}
		}

		/***
		 ***	Converter Functions
		 ***/

		function rgb($hex)
		{
			$r = hexdec(substr($hex, 1, 2));
			$g = hexdec(substr($hex, 3, 2));
			$b = hexdec(substr($hex, 5, 2));
			return array($r, $g, $b);
		}

		public function mm2pt($val)
		{
			return ($val * 2.8346);
		}

		public function mm2px($val)
		{
			return ($val * 3.7795);
		}

		public function pt2mm($val)
		{
			return ($val * 0.3527);
		}

		public function pt2px($val)
		{
			return ($val * 1.3333);
		}

		public function px2mm($val)
		{
			return ($val * 0.2645);
		}

		public function px2pt($val)
		{
			return ($val * 0.75);
		}

		/**
		 *
		 */

	}
?>