<?php
APP::uses('AppHelper', 'View/Helper');
class LocationHelper extends AppHelper {
    
/**
 * @var array
 */
    private $location = array();
    
/**
 * Other helpers used by LocationHelper
 *
 * @var array
 */
    public $helpers = array('Mayor', 'Product', 'Event', 'Tip', 'Collection', 'Property', 'Html');
    
/**
 * Initialize helper
 * @param array $location
 */
    public function initialize($location = array()) {
        $this->location = $location;
    }
    
    public function id() {
        if(isset($this->location['place']['id'])) {
            return $this->location['place']['id'];
        }
        return 0;
    }
    
    public function banner() {
        if(isset($this->location['place']['background_uri'])) {
            return $this->location['place']['background_uri'];
        }
        return Configure::read('DefaultImage.Cover.location');
    }
    
    public function icon($options = array()) {
        if(isset($this->location['place']['icon_uri'])) {
            return $this->Html->image($this->location['place']['icon_uri'], $options);
        }
        return $this->Html->image(Configure::read('DefaultImage.Icon.location'), $options);
    }
    
    public function address() {
        if(isset($this->location['place']['address'])) {
        	return $this->location['place']['address'];
        }
    }
    
    public function placename() {
        if(isset($this->location['place']['placename'])) {
        	return $this->location['place']['placename'];
        }
    }
    
/**
 * The starts for place
 * @return string
 */
    public function stars() {
        if(isset($this->location['place']['level'])) {
            return 'icon-star-'.$this->location['place']['level'];
        }
        return 'icon-star-0';
    }

/**
 * The number of point 
 * @return number
 */
    public function point() {
        if(isset($this->location['place']['point'])) {
            return intval($this->location['place']['point']);
        }
        return 0;
    }
    
/**
 * To determine whether show more bar or not.
 * @return number
 */
    public function more($total) {
        $displayed_number = 1;
        if($total > $displayed_number) {
            return $total - $displayed_number;
        }
        return 0;
    }
    
/**
 * @return string The phone number of place, 
 *     it returns empty string if found nothing.
 */
    public function phone() {
        if(isset($this->location['place']['tel'])) {
            return $this->location['place']['tel'];
        }
        return '';
    }
    
    public function repayPoint() {
        if(isset($this->location['place']['is_repay_point']) && $this->location['place']['is_repay_point'] > 0) {
        	return '<a href="#" class="icon-earn"></a>';
        }
        return '';
    }

/**
 * The number of rob.
 * @return number
 */
    public function robCount() {
    	if(isset($this->location['place']['rob_count'])) {
    		return (int) $this->location['place']['rob_count'];
    	}
    	return 0;
    }
    
/**
 * @return number It returns total of tip under the location.
 */
    public function tipCount() {
        if(isset($this->location['place']['tip_count'])) {
        	return intval($this->location['place']['tip_count']);
        }
        return 0;
    }
    
/**
 * @return number It returns total of event under the location.
 */
    public function eventCount() {
        if(isset($this->location['place']['event_count'])) {
        	return intval($this->location['place']['event_count']);
        }
        return 0;
    }
    
/**
 * @return number It returns total of collection under the location.
 */
    public function collectionCount() {
        if(isset($this->location['place']['at_collection_count'])) {
        	return intval($this->location['place']['at_collection_count']);
        }
        return 0;
    }
    
/**
 * @return number It returns total of product under the location.
 */
    public function productCount() {
        if(isset($this->location['place']['product_count'])) {
        	return intval($this->location['place']['product_count']);
        }
        return 0;
    }
    
/**
 * @return MayorHelper
 */
    public function mayor() {
    	if(isset($this->location['place']['mayor'])) {
    		$this->Mayor->initialize($this->location['place']['mayor']);
    	} else {
    		$this->Mayor->initialize();
    	}
    	return $this->Mayor;
    }
    
/**
 * @return ProductHelper
 */
    public function products() {
        if(isset($this->location['place']['products'])) {
        	$this->Product->initialize($this->location['place']['products']);
        } else {
        	$this->Product->initialize();
        }
        return $this->Product;
    }
    
/**
 * @return EventHelper
 */
    public function events() {
        if(isset($this->location['place']['events'])) {
        	$this->Event->initialize($this->location['place']['events']);
        } else {
        	$this->Event->initialize();
        }
        return $this->Event;
    }

/**
 * @return TipHelper
 */
    public function tips() {
        if(isset($this->location['place']['tips'])) {
        	$this->Tip->initialize($this->location['place']['tips']);
        } else {
        	$this->Tip->initialize();
        }
        return $this->Tip;
    }
    
/**
 * @return CollectionHelper
 */
    public function collections() {
        if(isset($this->location['place']['collections'])) {
        	$this->Collection->initialize($this->location['place']['collections']);
        } else {
        	$this->Collection->initialize();
        }
        return $this->Collection;
    }
    
/**
 * @return PropertyHelper
 */
    public function properties() {
        if(isset($this->location['place']['properties'])) {
        	$this->Property->initialize($this->location['place']['properties']);
        } else {
        	$this->Property->initialize();
        }
        return $this->Property;
    }
}