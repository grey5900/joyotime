<?php
APP::uses('AppHelper', 'View/Helper');
class PropertyHelper extends AppHelper {
    
    const STYLE_TEXT_WRAPPING = 0;
    const STYLE_IMAGE_BANNER = 1;
    const STYLE_LIST_IMAGES = 2;
    
/**
 * @var array
 */
    private $data = array();
/**
 * Other helpers used by LocationHelper
 *
 * @var array
 */
    public $helpers = array(
        'Html'
    );
    
/**
 * Initialize helper
 * @param array $data
 */
    public function initialize($data = array()) {
        $this->data = $data;
    }
    
    /**
     * Get items
     * @param unknown $data
     * @return PropertyNullItem
     */
    public function item($data = array()) {
        switch($data['style']) {
            case self::STYLE_TEXT_WRAPPING:
                $item = new PropertyTextWrappingItem($data, $this->Html);
                break;
            case self::STYLE_IMAGE_BANNER:
                $item = new PropertyImageBannerItem($data, $this->Html);
                break;
            case self::STYLE_LIST_IMAGES:
                $item = new PropertyListImagesItem($data, $this->Html);
                break;
            default:
                $item = new PropertyNullItem($data, $this->Html);
        }
        return $item;
    }
    
/**
 * To check whether product has exist or not.
 * @return boolean It returns true if exist.
 */
    public function has() {
    	return count($this->data) > 0;
    }
    
/**
 * @return number
 */
    public function count() {
    	return count($this->data);
    }
    
/**
 * Returns all items
 * @return array
 */
    public function getAll() {
    	return $this->data;
    }
}

abstract class PropertyItem {
    
/**
 * @var array
 */
    protected $data;
/**
 * @var HtmlHelper
 */
    protected $html;
    
    public function __construct(array $data = array(), HtmlHelper $html) {
        $this->data = $data;
        $this->html = $html;
    }
    
/**
 * Output cover images Html
 * @return string
 */
    abstract function output();
    
/**
 * @return string The title of specified property
 */
    public function title() {
        if(isset($this->data['title'])) {
            return $this->data['title'];
        }
        return '';
    }
    
/**
 * @return string The content of specified property
 */
    public function content() {
        if(isset($this->data['content'])) {
        	return $this->data['content'];
        }
        return '';
    }
    
/**
 * @return string The redirectly link
 */
    public function link() {
        if(isset($this->data['hyper_link'])) {
        	return $this->data['hyper_link'];
        }
        return '';
    }
}

class PropertyTextWrappingItem extends PropertyItem {
    
    public function output() {
        if(isset($this->data['image_uris'][0])) {
            $piece = '<a class="pull-left">';
            $piece .= $this->html->image($this->data['image_uris'][0], array('class' => 'media-object60'));
            $piece .= '</a>';
            $piece .= '<div class="media-body">';
            if(isset($this->data['image_uris'][0])) {
                $piece .= '<h4 class="media-heading">'.$this->title().'</h4>';
                $piece .= '<p>'.$this->content().'</p>';
            }
            $piece .= '</div>';
        } else {
            $piece = '<div class="media-body">';
            $piece .= '<h4 class="media-heading">'.$this->title().'</h4>';
            $piece .= '</div>';
            $piece .= '<p>'.$this->content().'</p>';
        }
        return $piece;
    }
}

class PropertyImageBannerItem extends PropertyItem {
    
    public function output() {
        $piece = '<div class="media-body">';
        $piece .= '<h4 class="media-heading">'.$this->title().'</h4>';
        $piece .= '</div>';
        if(isset($this->data['image_uris'][0])) {
            $piece .= $this->html->image($this->data['image_uris'][0], array('class' => 'media-object300'));
        }
        $piece .= '<p>'.$this->content().'</p>';
        return $piece;
    }
}

class PropertyListImagesItem extends PropertyItem {
    
    public function output() {
        $piece = '<div class="media-body">';
        $piece .= '<h4 class="media-heading">'.$this->title().'</h4>';
        $piece .= '</div>';
        if(isset($this->data['image_uris']) && is_array($this->data['image_uris'])) {
            $piece .= '<ul class="media-list">';
            foreach($this->data['image_uris'] as $path) {
                $piece .= '<li>'.$this->html->image($path, array('class' => 'media-object60')).'</li>';
            }
            $piece .= '</ul>';
        }
        $piece .= '<p>'.$this->content().'</p>';
        return $piece;
    }
}

class PropertyNullItem extends PropertyItem {
    
    public function output() {
        $piece = '<div class="media-body">';
        $piece .= '<h4 class="media-heading">'.$this->title().'</h4>';
        $piece .= '</div>';
        $piece .= '<p>'.$this->content().'</p>';
        return $piece;
    }
}