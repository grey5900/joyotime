<?php
namespace Model\Data;

use \Utility\Arrays\Access;

class Crop {
    
    public $name = '\Model\Data\Crop';
    
    public $left = 0;
    
    public $top = 0;
    
    public $width = 0;
    
    public $height = 0;
    
    const MIN_WIDTH = 640;
    const MIN_HEIGHT = 640;
    
    public function __construct($data = array()) {
        $this->left = Access::gets($data, 'left', 0);
        $this->top = Access::gets($data, 'top', 0);
        $this->width = Access::gets($data, 'width', 0);
        $this->height = Access::gets($data, 'height', 0);
        
        if(!$this->validate()) {
            throw new \CakeException(__('切图高宽数值非法'));
        }
    }
    
    public function validate() {
        return $this->width >= self::MIN_WIDTH && $this->height >= self::MIN_HEIGHT;
    }
}