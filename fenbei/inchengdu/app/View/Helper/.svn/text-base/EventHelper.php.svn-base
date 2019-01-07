<?php
APP::uses('AppHelper', 'View/Helper');
class EventHelper extends AppHelper {
    
/**
 * @var array
 */
    private $events = array();
    
/**
 * @var EventItem
 */
    private $item;
    
/**
 * Initialize helper
 * @param array $products
 */
    public function initialize($events = array()) {
        if(isset($events['result_list']['page_data'])) {
        	$this->events = $events['result_list']['page_data'];
        } else {
        	$this->events = $events;
        }
        $this->item = new EventItem();
    }
    
    public function item($item = array()) {
        $this->item->initialize($item);
        return $this->item;
    }
    
/**
 * To check whether product has exist or not.
 * @return boolean It returns true if exist.
 */
    public function has() {
        return count($this->events) > 0;
    }
    
/**
 * @return number
 */
    public function count() {
        return count($this->events);
    }
    
/**
 * Returns all events
 * @return array
 */
    public function getAll() {
        return $this->events;
    }
}

class EventItem {
    
    private $item = array();
    
    public function initialize($item = array()) {
        $this->item = $item;
    }
    
    public function cover() {
        if(isset($this->item['image_uri'])) {
            return $this->item['image_uri'];
        }
    }
    
    public function subject() {
        if(isset($this->item['subject'])) {
            return $this->item['subject'];
        }
    }
}