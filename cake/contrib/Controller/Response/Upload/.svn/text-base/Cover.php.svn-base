<?php
namespace Controller\Response\Upload;

use Controller\Response\Response;

class Cover extends Response
{
    public $_name = '\Controller\Response\Upload\Cover';
    
    public function success($file) {
        return json_encode(array(
            'result' => TRUE,
            'file' => $file,
            'url' => 'http://cover.fishsaying.com/'.$file.'?imageView/1/w/160/h/160/quality/90'
        ));
    }
    
    public function fail($message) {
        return $this->message(FALSE, $message);
    }
}