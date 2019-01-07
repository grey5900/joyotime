<?php
namespace Model\Data\Upload;

define('TRANSCODE_DIR', WWW_ROOT.'transcode'.DS);

class Voice extends \Model\Data\Upload {
	
	public $_name = '\Model\Data\Upload\Voice';
	
	public $dest = TRANSCODE_DIR;
    
/**
 * Upload file to QiNiu
 * 
 * @return boolean
 */
    public function save() {
    	if($this->available()) {
    		$saver = new \Utility\Storage\Voice();
			$result = $saver->write($this);
			$this->remove();
			return $result;
    	}
    	return false;
    }
    
    public function move() {
    	$name = uniqid('transcode');
    	$dest = $this->dest.$name;
    	if(move_uploaded_file($this->tmpName, $dest)) {
    		$this->tmpName = $dest;
    		return true;
    	}
    	return false;
    }
    
    public function remove() {
    	return unlink($this->tmpName);
    }
    
    public function rename($filepath) {
    	return rename($filepath, $this->tmpName);
    }
    
    public function available() {
    	$audio = new \Utility\File\Info\Audio($this);
    	return $audio->isMP4();
    }
}