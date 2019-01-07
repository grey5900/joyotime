<?php
namespace Model\Data\Upload;

require_once VENDORS.'wideimage/lib/WideImage.php';

class Cover extends \Model\Data\Upload {
	
	public $_name = '\Model\Data\Upload\Cover';
    
	/**
	 * @throws \Model\Exception\Upload
	 */
    public function crop(\Model\Data\Crop $crop) {
    	$image = \WideImage::load($this->tmpName);
    	$cropped = $image->crop(
    			$crop->left,
    			$crop->top,
    			$crop->width,
    			$crop->height);
    	$this->tmpName .= '.jpg';
    	$cropped->saveToFile($this->tmpName);
    }
    
    public function has() {
        if(parent::has()) {
            $info = new \Utility\File\Info\Image($this);
            return $info->isImage();
        }
        return false;
    }
    
    /**
     * @throws \CakeException
     */
    public function upload() {
    	$storage = new \Utility\Storage\Cover();
    	return $storage->write($this); 
    }
    
    /**
     * Get cropped path
     * 
     * @param unknown $file
     * @param unknown $ext
     * @return string
     */
    private function cropPath($file, $ext) {
    	return $this->tmpName.'.'.$ext;
    }
}