<?php
namespace Model\Data\Raw;

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
        $this->title = \Hash::gets($data, 'title');
        $this->userId = \Hash::gets($data, 'user_id');
        $this->length = \Hash::gets($data, 'length', 0);
        $this->language = \Hash::gets($data, 'language', 'zh_CN');
        $this->address = \Hash::gets($data, 'address');
        $this->addressComponent = \Hash::gets($data, 'address_component');
        $this->crop = new \Model\Data\Crop(\Hash::gets($data, 'crop'));
        $this->point = new \Model\Data\Point(
            \Hash::gets($data, 'latitude'), \Hash::gets($data, 'longitude')
        );
        $this->voice = new \Model\Data\Upload\Voice(\Hash::gets($data, 'voice'));
        $this->cover = new \Model\Data\Upload\Cover(\Hash::gets($data, 'cover'));
    }
}