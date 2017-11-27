<?php
    class FileParser
    {
        private $content;

        public function __construct($filename)
        {
            $this->content = file_get_contents($filename);
        }

        public function show()
        {
            return $this->content;
        }
    }
?>