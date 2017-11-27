<?php
    class Email
    {
        private $sender      = "";
        private $receiver    = "";

        public function __construct($sender = "", $receiver = "")
        {
            if($sender != "")
                $this->sender = $sender;
            if($receiver != "")
                $this->receiver = $receiver;
        }

        function public mailTo($text, $receiver, $sender = "")
        {

        }
    }
?>