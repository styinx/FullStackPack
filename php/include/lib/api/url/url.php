<?php
    class URL
    {
        public function __construct($request = "")
        {
            $this->parseQuery(substr($request, strrpos('?', $request) + 1));
        }

        protected function parseQuery($query)
        {
            $args = explode('&', $query);
            $result = "";

            if($this->checkQuery($args))
            {
                switch($_SERVER['REQUEST_METHOD'])
                {
                    case "DELETE":
                    {
                        $result = $this->DELETE($args);
                        break;
                    }
                    case "HEAD":
                    {
                        break;
                    }
                    case "GET": default:
                    {
                        $result = $this->GET($args);
                        break;
                    }
                    case "OPTIONS":
                    {

                    }
                    case "POST":
                    {
                        break;
                    }
                    case "PUT":
                    {
                        break;
                    }
                }
                $this->output($result, $args[count($args) - 1]);
            }
            else
            {
                throw new Exception("Check query: " . $query);
            }
        }

        protected function checkQuery($args)
        {
            $valid_exp = '#[a-zA-Z0-9]{1,10}#';
            foreach($args as $arg)
            {
                if(!preg_match($valid_exp, $arg))
                {
                    return false;
                }
            }
        }

        private function DELETE($args)
        {

        }

        public function GET($args)
        {

        }

        public function output($result, $format)
        {
            if($format == "json")
            {
                echo json_encode($result);
            }
        }
    }

    $rest = new URL($_SERVER["REQUEST_URI"]);
?>