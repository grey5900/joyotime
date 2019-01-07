<?php
namespace Model\Data\Upload;

define('TRANSCODE_DIR', WWW_ROOT.'transcode'.DS);

class Voice extends \Model\Data\Upload {
	
	public $dest = TRANSCODE_DIR;
    
    public function available() {
    	$audio = new \Utility\File\Info\Audio($this->tmpName);
    	return $audio->isMP4() || $audio->isMP3();
    }
    
    public function encoding() {
    	$meta = new \Model\Data\Transcode\Metadata(
    		\CakeSession::read('Auth.User._id'), 
    		\CakeSession::read('auth_token')
		);
    	$this->tmpName = $this->move($this->tmpName);
    	
//     	$this->Transcode->save($meta);
    	\CakeResque::enqueue('encodings', 'VoiceShell',
    		array('encode', $data)
    	);
    }
    
    private function move($filename) {
    	$name = uniqid('transcode');
    	$dest = $this->dest.$name;
    	if(move_uploaded_file($filename, $dest)) {
    		return $dest;
    	}
    	return false;
    }
}