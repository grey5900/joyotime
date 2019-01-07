<?php
namespace Model\Data;

class Point {
    
    public $name = '\Model\Data\Point';
    
    public $lat = 0.0;
    
    public $lng = 0.0;
    
    public function __construct($lat, $lng) {
        $this->lat = $lat;
        $this->lng = $lng;
    }
}