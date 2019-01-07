<?php
/**
 * The project of FishSaying is a SNS platform which is
 * based on voice sharing for each other with journey.
 *
 * The contributor platform is used to CP create/publish costomize content.
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
 * @copyright     Copyright (c) fishsaying.com. (http://www.fishsaying.com)
 * @link          http://fishsaying.com FishSaying(tm) Project
 * @since         FishSaying(tm) v 1.0.0
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
define('TRANSCODE_DIR', WWW_ROOT.'transcode'.DS);
/**
 * @package app.Controller.Component
 */
class TranscodeFileComponent extends Component {

	private $dest = TRANSCODE_DIR;

	public function __construct(ComponentCollection $collection, $settings = array()) {
		parent::__construct($collection, $settings);
	}

	public function move($filename) {
		$name = uniqid('transcode');
		$dest = $this->dest.$name;
		if(move_uploaded_file($filename, $dest)) {
			return $dest;
		}
		return false;
	}
}