<?php
namespace Controller\Response\Upload;

use Controller\Response\Response;

class Voice extends Response
{
    public $_name = '\Controller\Response\Upload\Voice';
    
    public function success($file, $duration) {
        return json_encode(array(
            'result' => TRUE,
            'file' => $file,
            'length' => $duration
        ));
    }
    
    public function fail($message) {
        return $this->message(FALSE, $message);
    }
}