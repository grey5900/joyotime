<?php
namespace Utility\Encode;

class Audio {
	
	public $_name = '\Utility\Encode\Audio';
	
/**
 * @var \Model\Data\Upload\Voice
 */
	private $voice;
	
/**
 * The path of bin which is file used to encoding audio/video
 * 
 * @var string
 */
	private $bin;
    
    public function __construct(\Model\Data\Upload\Voice $voice, $binPath) {
    	$this->voice = $voice;
    	$this->bin = $binPath;
    }
    
    /**
     * @return boolean
     */
    public function need() {
    	$audio = new \Utility\File\Info\Audio($this->voice);
    	return $audio->isWav() || $audio->isMP3() || $audio->unknown();
    }
    
    public function encode() {
    	$file = $this->voice->tmpName;
    	/**
    	 * ffmpeg -i input.wav -c:a libfdk_aac -b:a 128k output.m4a
    	 * ffmpeg -i input.wav -strict experimental -c:a aac -b:a 240k output.m4a (Native mode)
    	 * ffmpeg -i input.wav -c:a libfdk_aac -profile:a aac_he_v2 -b:a 32k output.m4a (High Effient)
    	 * ffmpeg -i input.wav -c:a libvo_aacenc -b:a 128k output.m4a
    	 * 
    	 * -vn: ignore video part.
    	 */
//     	$command = $this->bin." -i $file -c:a libfdk_aac -b:a 32k -ar 16000 -ac 2 -vn ".$this->newPath($file);
//     	$command = $this->bin." -i $file -c:a libvo_aacenc -b:a 32k -ar 16000 -ac 2 -vn ".$this->newPath($file);
//     	$command = $this->bin." -i $file -c:a libfdk_aac -vn ".$this->newPath($file);
    	$command = $this->bin." -i $file -strict experimental -c:a aac -b:a 32k -ar 16000 -ac 2 -vn ".$this->newPath($file);
    	exec($command, $output, $result);
    	
    	if($result == 0) {
    		$this->voice->remove();
    		$this->voice->rename($this->newPath($file));
    		$this->voice->type = 'audio/x-m4a';	
    	} else {
    		throw new \CakeException($output);
    	}
    }
    
    public function duration() {
        $file = $this->voice->tmpName;
        
    	ob_start();
    	passthru($this->bin." -i \"". $file . "\" 2>&1");
    	$duration = ob_get_contents();
    	ob_end_clean();
    
    	preg_match('/Duration: (.*?),/', $duration, $matches);
    	if(!$matches) throw new \Exception(__('Can not read duration for this audio'));
    	$duration = $matches[1];
    	$duration_array = split(':', $duration);
    	$duration = $duration_array[0] * 3600 + $duration_array[1] * 60 + $duration_array[2];
    	return $duration;
    }
    
    private function newPath($path) {
    	return $path.'.m4a';
    }
}