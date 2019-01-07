<?php
namespace Controller\Response;

class Response {
    
    public $_name = '\Controller\Response\Response';

    public function message($result, $message) {
        return json_encode(array('result' => $result, 'message' => $message));
    }
}