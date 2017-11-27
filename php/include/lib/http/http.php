<?php
    class HTTP
    {
        private $data = null;

        public function __construct($request = $_POST)
        {
            $this->data = $request;
        }
    }

    /* examples */
    $http = new HTTP($_POST);

?>