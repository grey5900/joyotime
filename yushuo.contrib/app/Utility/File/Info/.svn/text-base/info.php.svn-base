<?php
namespace Utility\File\Info;

class Info {
	
	protected $type = '';

	public function __construct($filename) {
		$finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
		$this->type = finfo_file($finfo, $filename);
		finfo_close($finfo);
	}
}