<?php

	class WikiHighlighter
	{
		private $tags 		= array	(
										"header1"       => "h1",
										"header2"       => "h2",
										"header3"       => "h3",
										"bold"			=> "b",
										"italic"		=> "i",
										"underlined"	=> "u",
										"crossed"		=> "s",
										"tab"			=> "tab",
										"quote"	        => "quote",
										"control"       => "control",
										"reference"	    => "reference",
										"link"          => "link",
										"image"         => "image",
										"table"         => "table"
									);

		private $types 	= array	(
                                    "DEFAULT"   => 	array	(
                                                                "header1"       => "#(^|\s)\+[^\+]+\+($|\s)#",
                                                                "header2"       => "#(^|\s)\+{2}[^\+]+\+{2}($|\s)#",
                                                                "header3"       => "#(^|\s)\+{3}[^\+]+\+{3}($|\s)#",
                                                                "bold" 		    => "#\*.+\*#",
                                                                "italic"        => "#'.+'#",
                                                                "underlined"    => "#_.+_#",
                                                                "crossed"       => "#-.+-#",
                                                                "tab"       	=> "#(^|\s)>[^\n<>]+[^\n<>]#",
                                                                "quote"         => "#(^|\s)>.+(:.+)?<($|\s)#",
                                                                "control"       => "%#.+(:.+)?#%",
                                                                "reference"     => "#\s%.+:.+%\s#",
                                                                "link"          => "#\s[(.+)?:.+]\s#",
                                                                "image"         => "#\s{.+:?.+:?.+}\s#",
                                                                "table"         => "##"
                                                            ),
                                );
		private $timer = null;
		private $exec_time = 0;

		public function __construct($file = "")
		{
			if($file != "")
			{
				$this->filename = $file;
				$this->filetype = substr($file, strrpos($file, '.') + 1);
				$this->handle = fopen($this->filename, "r");

				fseek($this->handle, 0, SEEK_END);
				$this->size = ftell($this->handle);
				fseek($this->handle, 0, SEEK_SET);

				$this->buffer = fread($this->handle, $this->size);
				$this->buffer = htmlspecialchars($this->buffer, ENT_QUOTES);
				fclose($this->handle);
			}
		}

		public function highlightText($wiki_type = "DEFAULT", $parse_text = "")
		{
			$type       = $this->types[$wiki_type];
			$text       = "";
			$matches    = array();

			if(@$parse_text != "")
			{
				$text = $parse_text;
			}
			else
			{
			    $text = $this->buffer;
			}

            if($this->multiMatch($type["quote"], $text, $matches))
			{
                $text = $this->tagLineMatches($text, $matches, "quote");
			}
			if($this->multiMatch($type["tab"], $text, $matches))
			{
                $text = $this->tagLineMatches($text, $matches, "tab");
			}
            if($this->multiMatch($type["control"], $text, $matches))
			{
                $text = $this->tagLineMatches($text, $matches, "control");
			}
			if($this->multiMatch($type["header3"], $text, $matches))
			{
                $text = $this->tagLineMatches($text, $matches, "header3");
			}
			if($this->multiMatch($type["header2"], $text, $matches))
			{
                $text = $this->tagLineMatches($text, $matches, "header2");
			}
			if($this->multiMatch($type["header1"], $text, $matches))
			{
                $text = $this->tagLineMatches($text, $matches, "header1");
			}
			if($this->multiMatch($type["bold"], $text, $matches))
			{
                $text = $this->tagLineMatches($text, $matches, "bold");
			}
			if($this->multiMatch($type["italic"], $text, $matches))
			{
                $text = $this->tagLineMatches($text, $matches, "italic");
			}
			if($this->multiMatch($type["underlined"], $text, $matches))
			{
                $text = $this->tagLineMatches($text, $matches, "underlined");
			}
			if($this->multiMatch($type["crossed"], $text, $matches))
			{
                $text = $this->tagLineMatches($text, $matches, "crossed");
			}

			return $text;
		}

		private function singleMatch($pattern, $subject, & $matches)
		{
			if($pattern != "" && $subject != "")
			{
				if(preg_match($pattern, $subject, $matches, PREG_OFFSET_CAPTURE))
				{
					return true;
				}
			}
			return false;
		}

		private function multiMatch($pattern, $subject, & $matches)
		{
			if($pattern != "" && $subject != "")
			{
				if(preg_match_all($pattern, $subject, $matches, PREG_OFFSET_CAPTURE))
				{
					return true;
				}
			}
			return false;
		}

		public function tagLine($line, $tag)
		{
			return "<" . $this->tags[$tag] . ">" . $line . "</" . $this->tags[$tag] . ">";
		}

		public function tagLineMatches($line, $matches, $tag)
		{
			$parsed_line = "";
			$prefix_pos = 0;
			foreach($matches[0] as $match => $pos)
			{
				if($pos[1] != 0)
				{
					$parsed_line .= substr($line, $prefix_pos, $pos[1] - $prefix_pos);
				}
				$parsed_line .= "<" . $this->tags[$tag] . ">" . substr($line, $pos[1], strlen($pos[0])) . "</" . $this->tags[$tag] . ">";
				$prefix_pos = $pos[1] + strlen($pos[0]);
			}
			$parsed_line .= substr($line, $prefix_pos);
			return $parsed_line;
		}

		public function tagWord($word, $tag, $keys = null, $values = null)
		{
			$string = "";

			if(!empty($tag) && !empty($word))
			{
				$string .= "<" . $tag;

				if(!empty($values) && !empty($keys) && (count($values) == count($keys)))
				{
					for($i = 0; $i < count($values); $i++)
					{
						$string .= " " . $keys[$i] . "='" . $values[$i] . "'";
                        if($i != count($values) - 1)
							$string .= " ";
					}
				}
				$string .= ">" . $word . "</" . $tag . ">";
			}
			return $string;
		}
	}
?>