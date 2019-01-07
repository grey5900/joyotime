<?php
namespace Utility\Storage;

require_once(VENDORS."qiniu/qiniu/rs.php");
require_once(VENDORS."qiniu/qiniu/io.php");

class QiNiu extends Storage {
	
	public $_name = '\Utility\Storage\QiNiu';
	
	/**
	 * @var string
	 */
	protected $token;
	
	public function __construct() {
		if(!$this->token) {
			throw new \CakeException('No found api token or api token is expired.');
		}
	}
	
/**
 * Write file into disk
 *
 * @param \Model\Data\Upload $model
 * @param string $rename
 * @throws \CakeException
 * @return string The hash key of file
 */
	function write(\Model\Data\Upload $model, $rename = null) {
		$putExtra = new \Qiniu_PutExtra();
		$putExtra->Crc32 = 1;
		list($ret, $err) = Qiniu_PutFile($this->token, $rename, $model->tmpName, $putExtra);
		if ($err !== null) {
			throw new \CakeException($err->Err, $err->Code);
		}
		return $ret['key'];
	}
}