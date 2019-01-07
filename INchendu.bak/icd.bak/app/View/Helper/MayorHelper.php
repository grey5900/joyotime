<?php
APP::uses('AppHelper', 'View/Helper');
class MayorHelper extends AppHelper {
    
/**
 * @var array
 */
    private $mayor = array();
    
/**
 * Initialize helper
 * @param array $mayor
 */
    public function initialize($mayor = array()) {
        $this->mayor = $mayor;
    }
    
    public function avatar() {
        if(isset($this->mayor['avatar_uri'])) {
            return $this->mayor['avatar_uri'];
        }
        return Configure::read('DefaultImage.Icon.User');
    }
    
    public function nickname() {
        if(isset($this->mayor['nickname'])) {
            return $this->mayor['nickname'];
        }
        return '无主之地';
    }
}