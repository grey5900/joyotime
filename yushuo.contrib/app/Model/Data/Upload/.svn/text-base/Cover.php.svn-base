<?php
namespace Model\Data\Upload;

require_once VENDORS.'wideimage/lib/WideImage.php';

class Cover extends \Model\Data\Upload {
    
	/**
	 * @throws \Model\Exception\Upload
	 */
    public function crop(\Model\Data\Crop $crop) {
    	try {
	    	$image = \WideImage::load($this->tmpName);
	    	$cropped = $image->crop(
	    			$crop->left,
	    			$crop->top,
	    			$crop->width,
	    			$crop->height);
	    	$cropped->saveToFile($this->cropPath($this->tmpName, 'jpg'));
    	} catch (\Exception $e) {
    		throw new \Model\Exception\Upload($e->getMessage(), '/voices');
    	}
    }
    
    /**
     * @throws \CakeException
     */
    public function upload() {
    	$storage = new \Utility\Storage\QiNiu(\CakeSession::read('Api.Token.uptoken.cover'));
    	$storage->path = $this->cropPath($this->tmpName, 'jpg');
    	$this->dest = $storage->write(); 
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