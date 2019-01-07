<?php
/**
 * The project of FishSaying is a SNS platform which is
 * based on voice sharing for each other with journey.
 *
 * The RESTful style API is used to communicate with each client-side.
 *
 * PHP 5
 *
 * FishSaying(tm) : FishSaying (http://www.fishsaying.com)
 * Copyright (c) fishsaying.com. (http://fishsaying.com)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) fishsaying.com. (http://www.fishsaying.com)
 * @link http://fishsaying.com FishSaying(tm) Project
 * @since FishSaying(tm) v 0.0.1
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::uses('AppController', 'Controller');
/**
 * FishSaying Controller
 *
 * Handle events posted from fishsaying server.
 *
 * @package app.Controller
 */
class VoicesController extends AppController {
	public $name = 'Voices';
	public $components = array(
			'VoiceApi'=>array(
					'className'=>'FishSayingApi.Voice' 
			)
	);
	public $uses = array(
			'Voice' 
	);
	/**
	 * 
	 * @abstract create locate img address from qi niu
	 * @param string $shortId
	 * @param string $img_size
	 * @throws NotFoundException
	 * @return mixed
	 */
	public function cover($shortId, $img_size='x80') {
		$this->autoLayout = false;
		$this->autoRender = false;
		if(!in_array($img_size,array('x80','x160','x640'))){
			throw new NotFoundException ();
		}
		$resp = $this->VoiceApi->view ( $shortId );
		if ($resp->isFail ()) {
			throw new NotFoundException ();
		}
		$data = $resp->getData ();
		$url = $data ['cover'] [$img_size];
		$imgdata = file_get_contents ( $url );
		$info = getimagesize ( $url );
		$im = imagecreatefromstring ( $imgdata );
		if ($im !== false) {
			header ( 'Content-Type: ' . $info ['mime'] );
			switch ($info ['mime']) {
				case 'image/jpeg' :
					imagejpeg ( $im );
					break;
				case 'image/png' :
					imagepng ( $im );
					break;
				case 'image/vnd.wap.wbmp' :
					imagewbmp ( $im );
					break;
				case 'image/imagegif' :
					imagegif ( $im );
					break;
				default :
			}
			imagedestroy ( $im );
		} else {
			throw new NotFoundException ();
		}
		
	}
}