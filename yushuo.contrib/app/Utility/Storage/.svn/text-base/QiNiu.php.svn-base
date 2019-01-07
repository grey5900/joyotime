<?php
namespace Utility\Storage;

require_once(VENDORS."qiniu/qiniu/rs.php");
require_once(VENDORS."qiniu/qiniu/io.php");

class QiNiu implements Storage {
	
	public $path = '';
	
	/**
	 * The name of file
	 * 
	 * @var string
	 */
	public $name = null;
	
	public function __construct($token) {
		$this->token = $token;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Utility\Storage\Storage::read()
	 */
	function read() {
		return true;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Utility\Storage\Storage::write()
	 */
	function write(&$data = array()) {
		$putExtra = new \Qiniu_PutExtra();
		$putExtra->Crc32 = 1;
		list($ret, $err) = Qiniu_PutFile($this->token, $this->name, $this->path, $putExtra);
		if ($err !== null) {
			throw new \CakeException($err->Err, $err->Code);
		} else {
			return $ret['key'];
		}
	}
}