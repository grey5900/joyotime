<?php
namespace Utility\File\Info;

class Audio extends Info {
	
	public $_name = '\Utility\File\Info\Audio';
	
	public function __construct(\Model\Data\Upload\Voice $voice) {
		parent::__construct($voice->tmpName);
	}

	public function isMP4() {
		return preg_match('/(mp4|m4a)+/i', $this->type);
	}

	public function isMP3() {
		return preg_match('/audio\\/(mp3|mpeg)+/i', $this->type);
	}

	public function isWav() {
		return preg_match('/wav/i', $this->type);
	}
	
	public function unknown() {
        return 'application/octet-stream' == strtolower($this->type);
	}
}