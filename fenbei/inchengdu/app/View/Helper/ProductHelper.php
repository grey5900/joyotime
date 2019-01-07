<?php
APP::uses('AppHelper', 'View/Helper');
class ProductHelper extends AppHelper {
    
/**
 * @var array
 */
    private $products = array();
    
/**
 * @var ProductItem
 */
    private $item;
    
/**
 * Initialize helper
 * @param array $products
 */
    public function initialize($products = array()) {
        if(isset($products['result_list']['page_data'])) {
            $this->products = $products['result_list']['page_data'];
        } else {
            $this->products = $products;
        }
        $this->item = new ProductItem();
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
        return count($this->products) > 0;
    }
    
/**
 * @return number
 */
    public function count() {
        return count($this->products);
    }
    
/**
 * Returns all products
 * @return array
 */
    public function getAll() {
        return $this->products;
    }
}

class ProductItem {
    
    private $item = array();
    
    public function initialize($item = array()) {
        $this->item = $item;
    }
    
    public function cover() {
        if(isset($this->item['image_uri'])) {
            return $this->item['image_uri'];
        }
    }
    
    public function name() {
        if(isset($this->item['name'])) {
            return $this->item['name'];
        }
    }
    
    public function description() {
        if(isset($this->item['description'])) {
            return $this->item['description'];
        }
    }
    
    public function point() {
        if(isset($this->item['price']) && $this->item['price'] > 0) {
            return '<span class="icon-integral"><em>'.$this->item['price'].'</em>积分</span>';
        }
    }
}