<?php
APP::uses('AppHelper', 'View/Helper');
class CollectionHelper extends AppHelper {
    
/**
 * @var array
 */
    private $collections = array();
    
/**
 * @var CollectionItem
 */
    private $item;
    
    public $helpers = array(
        'Html'
    );
    
/**
 * Initialize helper
 * @param array $collections
 */
    public function initialize($collections = array()) {
        if(isset($collections['result_list']['page_data'])) {
        	$this->collections = $collections['result_list']['page_data'];
        } else {
        	$this->collections = $collections;
        }
        $this->item = new CollectionItem();
    }
    
    public function item($item = array()) {
        $this->item->initialize($item, $this->Html);
        return $this->item;
    }
    
/**
 * To check whether product has exist or not.
 * @return boolean It returns true if exist.
 */
    public function has() {
        return count($this->collections) > 0;
    }
    
/**
 * @return number
 */
    public function count() {
        return count($this->collections);
    }
    
/**
 * Returns all events
 * @return array
 */
    public function getAll() {
        return $this->collections;
    }
}

/**
 * The entity of collection data.
 *
 */
class CollectionItem {
    
    private $data = array();
/**
 * @var HtmlHelper
 */
    private $html;
/**
 * @var CollectionCreatorItem
 */
    private $creator;
    
    public function initialize($item = array(), HtmlHelper $html) {
        $this->data = $item;
        $this->html = $html;
        $this->creator = new CollectionCreatorItem();
    }
    
/**
 * The path of collection's icon.
 *  
 * @param array $options The $options likes HtmlHelper's.
 * @return string The url of image path, 
 *     it returns default path defined in Config/bootstrap.php 
 *     if found nothing.
 */
    public function icon() {
        if(isset($this->data['image_uri'])) {
            $this->data['image_uri'];
        }
        return Configure::read('DefaultImage.Icon.Collection');
    }
    
/**
 * Display essence icon
 * @return string
 */
    public function hot() {
    	if(isset($this->item['is_essence']) && $this->item['is_essence'] > 0) {
    		return '<i class="icon-hot"></i>';
    	}
    	return '';
    }
    
/**
 * @return string The name of collection, 
 *     it returns empty string if found nothing.
 */
    public function name() {
        if(isset($this->data['name'])) {
            return $this->data['name'];
        }
        return '';
    }
    
/**
 * @return number The total of awesomes belongs to the collection.
 */
    public function awesomes() {
        if(isset($this->data['praise_count'])) {
            return intval($this->data['praise_count']);
        }
        return 0;
    }
    
/**
 * @return CollectionCreatorItem
 */
    public function creator() {
        if(isset($this->data['user'])) {
            $this->creator->initialize($this->data['user'], $this->html);
        } else {
            $this->creator->initialize(array(), $this->html);
        }
        return $this->creator;
    }
}

/**
 * The entity of collection user data.
 *
 */
class CollectionCreatorItem {
    
/**
 * @var HtmlHelper
 */
    private $html;
/**
 * @var array
 */
    private $data;
    
/**
 * @param array $data
 * @param HtmlHelper $html
 */
    public function initialize(array $data = array(), HtmlHelper $html) {
        $this->data = $data;
        $this->html = $html;
    }
    
/**
 * @return string The nickname of creator who is created to the collection.
 */
    public function nickname() {
        if(isset($this->data['nickname'])) {
        	return $this->data['nickname'];
        }
        return '无主之地';
    }
}