<?php
namespace Model\Exception;

class Upload extends \CakeException {
	
/**
 * Redirect url for controller
 * 
 * @var string
 */
	private $url = '';
	
	public function __construct($message, $url, $code = 500) {
		parent::__construct($message, $code);
		$this->url = url;
	} 
	
	public function getUrl() {
		return $this->url;
	}
}