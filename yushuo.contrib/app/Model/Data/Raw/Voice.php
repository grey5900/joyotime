<?php
namespace Model\Data\Raw;

use \Utility\Arrays\Access;

class Voice {
    
    public $name = '\Model\Data\Raw\Voice';
    
    public $title = '';
    
    public $userId = '';
    
    public $length = 0;
    
    public $language = '';
    
    public $address = '';

    public $addressComponent = '';
    
    /**
     * @var \Model\Data\Crop
     */
    public $crop;
    
    /**
     * @var \Model\Data\Point
     */
    public $point;
    
    /**
     * @var \Model\Data\Upload\Voice
     */
    public $voice;
    
    /**
     * @var \Model\Data\Upload\Cover
     */
    public $cover;
    
    public function __construct(&$data = array()) {
        $this->title = Access::gets($data, 'title');
        $this->userId = Access::gets($data, 'user_id');
        $this->length = Access::gets($data, 'length', 0);
        $this->language = Access::gets($data, 'language', 'zh_CN');
        $this->address = Access::gets($data, 'address');
        $this->addressComponent = Access::gets($data, 'address_component');
        $this->crop = new \Model\Data\Crop(Access::gets($data, 'crop'));
        $this->point = new \Model\Data\Point(
            Access::gets($data, 'latitude'), Access::gets($data, 'longitude')
        );
        $this->voice = new \Model\Data\Upload\Voice(Access::gets($data, 'voice'));
        $this->cover = new \Model\Data\Upload\Cover(Access::gets($data, 'cover'));
    }
}