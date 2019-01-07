<?php
namespace Utility\File\Info;

class Image extends Info {
	
	public $_name = '\Utility\File\Info\Image';
	
	public function __construct(\Model\Data\Upload $file) {
		parent::__construct($file->tmpName);
	}
	
	public function isImage() {
	    return preg_match('/image\\/*/i', $this->type);
	}
}