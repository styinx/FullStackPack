<?php
	class Captcha
	{
		private $font 			= "../fonts/arial.ttf";
		private $chars 			= "ABCDEFGHJKLMNPRSTUVWXYZabcdefghjkmnprstuvwxyz123456789?!";
		private $fontColors		= array("r" => array("min" => 100, "max" => 255),
										"g" => array("min" => 100, "max" => 255),
										"b" => array("min" => 100, "max" => 255));
		private $bgColors		= array("r" => array("min" =>   0, "max" => 100),
										"g" => array("min" =>   0, "max" => 100),
										"b" => array("min" =>   0, "max" => 100));
		private $stats			= array("fontSize"		=> array("min" =>  16, "max" => 24),
										"characters" 	=> array("min" =>   5, "max" =>  6),
										"width"			=> array("min" => 300, "max" => 500),
										"height"		=> array("min" =>  50, "max" => 150),
										"angle"			=> array("min" => -60, "max" =>  60),
										"blur"			=> array("min" => 10, "max" =>  20));
		private $image;
		private $string 		= "";

		private $fontSize		= 15;

		public function __construct()
		{

		}

		private function getRand($dict, $key)
		{
			return rand($dict[$key]["min"], $dict[$key]["max"]);
		}

		private function setRand($dict, $key, $name, $val)
		{
			$dict[$key][$name] = $val;
		}

		public function makeCaptchaDefault()
		{
			$font = "../fonts/arial.ttf";

			$chars = $this->getRand($this->stats, "characters");
			$randW = $this->getRand($this->stats, "width");
			$randH = $this->getRand($this->stats, "height");
			$randBlur = $this->getRand($this->stats, "blur");
			$randR = $this->getRand($this->bgColors, "r");
			$randG = $this->getRand($this->bgColors, "g");
			$randB = $this->getRand($this->bgColors, "b");

			$image = imagecreate($randW, $randH);
			$color = imagecolorallocate($image, $randR, $randG, $randB);

			imagefill($image, 0, 0, $color);

			for($i = 0; $i < $chars; $i++)
			{
				$randChar 	= rand(0, strlen($this->chars) - 1);
				$this->string .= $this->chars[$randChar];

				$randPt		= $this->getRand($this->stats, "fontSize");
				$randAngle 	= $this->getRand($this->stats, "angle");
				$fontR 		= $this->getRand($this->fontColors, "r");
				$fontG 		= $this->getRand($this->fontColors, "g");
				$fontB 		= $this->getRand($this->fontColors, "b");

				$rgb = imagecolorallocate($image, $fontR, $fontG, $fontB);
				imagettftext($image, $randPt, $randAngle, $i*($randW/$chars)+$randPt, $randH/2 + $randPt/2,
							$rgb, $font, $this->chars[$randChar]);
			}

			for($i = 0; $i < $randBlur; $i++)
			{
				$randX1 = rand(0, $randW);
				$randX2 = rand(0, $randW);
				$randY1 = rand(0, $randH);
				$randY2 = rand(0, $randH);
				$lineR = $this->getRand($this->bgColors, "r");
				$lineG = $this->getRand($this->bgColors, "g");
				$lineB = $this->getRand($this->bgColors, "b");
				$lineCol = imagecolorallocate($image, $lineR, $lineG, $lineB);
				imageline($image, $randX1, $randY1, $randX2, $randY2, $lineCol);
			}

			ob_start();

			imagepng($image);
			$captcha = ob_get_contents();
			imagedestroy($image);

			ob_end_clean();

			return array(base64_encode($captcha), $this->string);
		}
	}
?>