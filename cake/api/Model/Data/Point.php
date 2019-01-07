<?php
/**
 * The project of FishSaying is a SNS platform which is
 * based on voice sharing for each other with journey.
 *
 * The RESTful style API is used to communicate with each client-side.
 *
 * PHP 5
 *
 * FishSaying(tm) : FishSaying (http://www.fishsaying.com)
 * Copyright (c) fishsaying.com. (http://fishsaying.com)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) fishsaying.com. (http://www.fishsaying.com)
 * @link          http://fishsaying.com FishSaying(tm) Project
 * @since         FishSaying(tm) v 0.0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
namespace Model\Data;

class Point {
    
    public $name = '\Model\Data\Point';
    
    const PI = 3.1415927;
    
    const EARTH_RADIUS = 6378137;    // Unit: meter
    
    private $longitude = 0.0;
    
    private $latitude = 0.0;
    
	public function __construct($longitude, $latitude) {
        $this->longitude = (double)$longitude;
        $this->latitude  = (double)$latitude;
    }
    
/**
 * @return the $longitude
 */
    public function getLongitude() {
    	return $this->longitude;
    }
    
/**
 * @return the $latitude
 */
    public function getLatitude() {
    	return $this->latitude;
    }
    
/**
 * Get radian of latitude
 * @return number
 */
    public function getRadianOfLatitude() {
        return $this->radian($this->getLatitude());
    }
    
/**
 * Get radian of longitude
 * @return number
 */
    public function getRadianOfLongitude() {
        return $this->radian($this->getLongitude());
    }
    
/**
 * Calculate distance between two points
 * 
 * @param Point $p2
 * @return number
 */
    public function distanceFlat(\Model\Data\Point $p2) {
        $ax = $this->longitude;
        $ay = $this->latitude;
        $bx = $p2->getLongitude();
        $by = $p2->getLatitude();
        $s = sqrt(pow($by - $ay, 2) + pow($bx - $ax, 2));
        $s *= 111.1;    // long and lat to km
        $s *= 1000;     // to meters
        return $s;
    }
    
    public function distance(\Model\Data\Point $p2) {
    	$radLat1 = $this->getRadianOfLatitude();
    	$radLat2 = $p2->getRadianOfLatitude();
    	$a = $radLat1 - $radLat2;
    	$b = $this->getRadianOfLongitude() - $p2->getRadianOfLongitude();
    	$s = 2 * asin(sqrt(pow(sin($a/2),2) +
    			cos($radLat1)*cos($radLat2)*pow(sin($b/2),2)));
    	$s = $s * self::EARTH_RADIUS;
    	$s = round($s * 10000) / 10000;
    	return $s;
    }
    
/**
 * Calculate radian
 * 
 * @param float $d
 * @return float
 */
    private function radian($d)
    {
    	return $d * self::PI / 180.0;
    }
}