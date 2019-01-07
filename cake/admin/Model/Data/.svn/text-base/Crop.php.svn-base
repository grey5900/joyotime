<?php
namespace Model\Data;

class Crop {
    
    public $name = '\Model\Data\Crop';
    
    public $left = 0;
    
    public $top = 0;
    
    public $width = 0;
    
    public $height = 0;
    
    const MIN_WIDTH = 640;
    const MIN_HEIGHT = 640;
    
    public function __construct($data = array()) {
        $this->left = \Hash::get($data, 'left');
        $this->top = \Hash::get($data, 'top');
        $this->width = \Hash::get($data, 'width');
        $this->height = \Hash::get($data, 'height');
        
        if(!$this->validate()) {
            throw new \CakeException(__('切图高宽数值非法'));
        }
    }
    
    public function validate() {
        return $this->width >= self::MIN_WIDTH 
            && $this->height >= self::MIN_HEIGHT
            && is_numeric($this->left)
            && is_numeric($this->top)
            && $this->left >= 0
            && $this->top >= 0;
    }
}