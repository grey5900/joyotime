<?php
APP::uses('BaseComponent', 'QiNiu.Controller/Component');

class PatchComponent extends Component {
    
    public $components = array('QiNiu.Path', 'Price', 'QiNiu');
  
    /**
     * @var Controller
     */
    public $controller;
    
/**
 * (non-PHPdoc)
 * @see Component::initialize()
 */
    public function initialize(Controller $controller) {
        $this->controller = $controller;
        $this->User = ClassRegistry::init('SlaveUser');
        
        $agent   = ClassRegistry::init('UserAgent');
        $version = ClassRegistry::init('Version');
        /**
        $agent->extract($this->controller->getUserAgent());
        if(strtolower($agent->getPlatform()) == 'android') {
        	$version->extract($agent->getFishsayingVersion());
        	if($version->compareTo('1.3.0') == -1) {
                Configure::write('Origin.Voice.URL', 1);
        	} else {
        	    Configure::write('Origin.Voice.URL', 0);
        	}
        }
       **/ 
    }
    
    public function patchFollow(array &$data, $follows) {
        $data['is_followed'] = (int)in_array($data['_id'], $follows);
    }
    
/**
 * Patch domain for each url of file.
 *
 * @param array $data
 */
    public function patchPath(array &$data) {
    	if(isset($data['avatar']) && is_array($data['avatar'])) {
    		foreach($data['avatar'] as $dimension => &$item) {
    			$this->getAvatarPath($item, $dimension);
    		}
    	}
    	if(isset($data['cover']) && is_array($data['cover'])) {
    		foreach($data['cover'] as $dimension => &$item) {
    		    $this->getCoverPath($item, $dimension);
    		}
    	}
    	if(Configure::read('Origin.Voice.URL')) {
    	    if(isset($data['voice']) && !empty($data['voice'])) {
    	    	// This is
    	    	if(!isset($data['isfree']) || empty($data['isfree'])) {
    	    		$data['trial_voice'] = $this->QiNiu->getTrialVoice($data['voice']);
    	    		$data['voice'] = $this->QiNiu->getPrivateUrl(QiNiuComponent::BUCKET_VOICE, $data['voice']);
    	    	} else {
    	    		$data['voice'] = $this->QiNiu->getPrivateUrl(QiNiuComponent::BUCKET_VOICE, $data['voice']);
    	    		$data['trial_voice'] = $data['voice'];
    	    	}
    	    }
    	} else {
    	    if(isset($data['voice']) && !empty($data['voice'])) {
    	    	// This is
    	    	if(!isset($data['isfree']) || empty($data['isfree'])) {
    	    		$data['trial_voice'] = $this->getTrialVoicePath($data['voice']);
    	    		$data['voice'] = $this->getVoicePath($data['voice']);
    	    	} else {
    	    		$data['voice'] = $this->getVoicePath($data['voice']);
    	    		$data['trial_voice'] = $data['voice'];
    	    	}
    	    }
    	}
    }
    
    private function getVoicePath($key) {
        $url = $this->Path->Voice($key);
        return 'http://'.Configure::read('URL.Website.Domain').'/r/play/'.$key.'?url='.urlencode($url);
    }
    
    private function getTrialVoicePath($key) {
        $url = $this->Path->trial($key);
        return 'http://'.Configure::read('URL.Website.Domain').'/r/play/'.$key.'?url='.urlencode($url);
    }
    
/**
 * Format dimension string to integer
 * 
 * @param string $dimension
 * @return number
 */
    private function formatDimension($dimension) {
        return intval(substr($dimension, 1, strlen($dimension)));
    }
    
/**
 * Get completed path of voice's cover
 * 
 * @param string $item The key of voice cover
 * @param string $dimension
 */
    private function getCoverPath(&$item, $dimension) {
        $item = $this->Path->cover($item, $dimension);
    }
    
/**
 * Get completed path of user's avatar
 * 
 * @param string $item The key of user avatar
 * @param string $dimension
 */
    private function getAvatarPath(&$item, $dimension) {
        $item = $this->Path->avatar($item, $dimension);
    }
    
/**
 * According user available time to calc cash
 *
 * @param array $data
 */
    public function patchCash(array &$data) {
    	if(isset($data['earn']) && $data['earn'] > 0) {
    	    $lang = $this->controller->request->query('language');
    	    if($lang == 'zh_CN') {
    	        $cash = $this->Price->toCash('CNY');
    	    } else {
    		    $cash = $this->Price->toCash('USD');
    	    }
    		$data['cash'] = $cash->currency().$cash->calc($data['earn']);
    	}
    }
    
/**
 * Patch user for each item which needs join user.
 *
 * @param array $data
 */
    public function patchUser(array &$data) {
    	
    	$userId = false;
    	if(isset($data['user_id']) && !empty($data['user_id'])) {
    		$userId = $data['user_id'];
    	} else if(isset($data['follower_id']) && !empty($data['follower_id'])) {
    		$userId = $data['follower_id'];
    	}
    	
    	if(!$userId) {
    		return false;
    	}
    	
    	$row = $this->User->getById($userId);
    	
    	if($row) {
    		$row = $row;
    		$this->patchPath($row);
    		$this->patchCash($row);
    		$data['user'] = $row;
    	}
    }
    
/**
 * Patch voice for each item which needs join user.
 *
 * @param array $data
 */
    public function patchVoice(array &$data) {
    	$id = false;
    	if(isset($data['voice_id']) && !empty($data['voice_id'])) {
    		$id = $data['voice_id'];
    	} 
    	
    	if(!$id) {
    		return false;
    	}
    	$this->Voice = ClassRegistry::init('Voice');
    	$row = $this->Voice->findById($id);
    	if(isset($row['Voice'])) {
    		$data['voice'] = $row['Voice'];
    	}
    }
    
/**
 * The final price of voice
 *
 * @param array $voice
 * @return number The minutes as voice's price
 */
    public function patchPrice(array &$voice) {
    	if(isset($voice['length'])) {
    		$voice['price'] = $this->Price->calc($voice['length']);
    	}
    }
    
/**
 * Patch the field called `bought` to voice model
 * @param array $data The resultset of voice
 * @param array $bought
 * @param number $owner
 */
    public function patchBought(&$data = array(), &$bought = array(), $owner = 0) {
    	if($bought && in_array($data['_id'], $bought)) {
    		$data['bought'] = 1;
    	} elseif($owner == $data['user_id']) {
    		$data['bought'] = 1;
    	} else {
    		$data['bought'] = 0;
    	}
    }
    
/**
 * Patch thumbnail path for favorite cover
 * 
 * @param array $data
 */
    public function patchThumbnailPath(array &$data) {
    	if(isset($data['thumbnail']) && is_array($data['thumbnail'])) {
    		foreach($data['thumbnail'] as &$covers) {
    			if(is_array($covers)) {
    				foreach($covers as $dimension => &$item) {
    					$item = $this->Path->thumbnail($item, $dimension);
    				}
    			}
    		}
    	}
    }
    
/**
 * Calculate distance for each voice
 * 
 * @param array $data
 * @param \Model\Data\Point $cp
 */
    public function patchDistance(array &$data, \Model\Data\Point $cp) {
        if(isset($data['location']['lng']) && isset($data['location']['lat'])) {
            $pot = new \Model\Data\Point($data['location']['lng'], $data['location']['lat']);
            $data['distance'] = $cp->distanceFlat($pot);
        }
    }
}