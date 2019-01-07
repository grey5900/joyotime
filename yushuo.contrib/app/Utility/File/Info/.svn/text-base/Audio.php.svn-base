<?php
namespace Utility\File\Info;

class Audio extends Info {

	public function isMP4() {
		return (bool) preg_match('/mp4/i', $this->type);
	}

	public function isMP3() {
		return 'audio/mpeg' == $this->type;
	}
}