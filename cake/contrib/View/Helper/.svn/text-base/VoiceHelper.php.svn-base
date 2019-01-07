<?php
App::uses('AppHelper', 'View/Helper');

class VoiceHelper extends AppHelper {
    
    const STATUS_PENDING = 0;
	const STATUS_APPROVED = 1;
	const STATUS_INVALID = 2;
    const STATUS_UNAVAILABLE = 3; 
    
    public $helpers = array('Html');
    
    public function status(&$row = array()) {
        $status = $this->gets($row, 'status', 0);
        switch(intval($status)) {
            case self::STATUS_PENDING:
                return '<span class="pending-color">'.__('待审核').'</span>';
            break;
            case self::STATUS_APPROVED:
                return '<span class="approved-color">'.__('已上架').'</span>';
            break;
            case self::STATUS_INVALID:
//                 return '<span class="invalid-color invalid-reason">'.__('已驳回').'</span>';
                $reason = isset($row['comment']) ? $row['comment'] : '';
                return '<a class="invalid-color invalid-reason" data-id="tooltip" data-toggle="tooltip" data-placement="top" title="" data-original-title="'.$reason.'">'.__('已驳回').'</a>';
                
            break;
            case self::STATUS_UNAVAILABLE:
                return '<span class="unavailable-color">'.__('已下架').'</span>';
            break;
        }
        return '<span class="pending-color">'.__('待审核').'</span>';
    }
    
    public function edit(&$row = array()) {
        $status = $this->gets($row, 'status', 0);
        $id = $this->gets($row, '_id');
        if($id && $status != self::STATUS_APPROVED) {
            return $this->Html->link(__(''), "/voices/edit/{$id}", array('class' => 'icon-edit'));
        }
    }
    
    public function remove(&$row = array()) {
        $status = $this->gets($row, 'status', 0);
        $id = $this->gets($row, '_id');
        if($status == self::STATUS_INVALID && $id) {
            return $this->Html->link(__(''), "#remove", array('class' => 'icon-delete remove-link','data-toggle' => 'modal','data-remove' => '/voices/remove/'.$id));
        }
    }
    
    public function file($row = array()) {
        $voice = explode('/', $this->gets($row, 'voice'));
        return array_pop($voice);
    }
    
/**
 * Get cover with different size
 *  
 * @param array $row
 * @param string $demension The $demension available values including,
 * x80, x160, x640, source
 * @return string
 */
    public function cover(&$row = array(), $demension = 'x80') {
        $cover = $this->gets($row, 'cover', array());
        $title = $this->gets($row, 'title');
        switch($demension) {
            case 'x80':
                $width = $height = 80;
                break;
            case 'x160':
                $width = $height = 160;
                break;
            case 'x640':
                $width = $height = 640;
                break;
            case 'source':
                $width = $height = 640;
                break;
            default:
                $width = $height = 80;
        }
        if($cover && isset($cover[$demension])) {
            return $cover[$demension];
        }
        return '';
    }
    
/**
 * Get cover url as preview with different size
 *  
 * @param string $key
 * @param string $domain
 * @param string $demension The $demension available values including,
 * x80, x160, x640, source
 * @return string The url of cover thumbnail
 */
    public function coverPreview($key, $domain, $demension = 'x80') {
        switch($demension) {
        	case 'x80':
        		$url = $domain.'/'.$key.'?imageView/2/w/80/h/80';
        		break;
        	case 'x160':
        		$url = $domain.'/'.$key.'?imageView/2/w/160/h/160';
        		break;
        	case 'x640':
        		$url = $domain.'/'.$key.'?imageView/2/w/640/h/640';
        		break;
        	default:
        		$url = $domain.'/'.$key;
        }
        return $url;
    }
    
    public function isfree(&$row = array()) {
    	$isfree = $this->gets($row, 'isfree');
    	if($isfree) {
    		return $this->Html->link(__('免费'), 'javascript:;',array('class' => 'isfree'));
    	}
    	return '';
    }
    public function address(&$row = array()) {
        $voice = $this->gets($row, 'voice');
       
        if($voice) {
            return $voice;
        }
        return '';
    }
    
    public function point(&$row = array()) {
        $lat = $this->gets($row['location'], 'lat');
        $lng = $this->gets($row['location'], 'lng');
        if($lat && $lng) {
            return new Point($lat, $lng);
        }
        return new Point();
    }
    
    public function created(&$row = array()) {
    	return strftime('%Y-%m-%d %H:%M:%S', $row['created']['sec']);
    }
    
    public function modified(&$row = array()) {
    	return strftime('%Y-%m-%d %H:%M:%S', $row['modified']['sec']);
    }
}

/**
 * The model of coordinate
 *
 */
class Point {
    
    private $lat = 0;
    private $lng = 0;
    
    public function __construct($lat = '', $lng = '') {
        $this->lat = $lat;
        $this->lng = $lng;
    }
    
    public function latitude() {
        return $this->lat;
    }
    
    public function longitude() {
        return $this->lng;
    }
}
