<?php
App::uses('AppHelper', 'View/Helper');

class LinkHelper extends AppHelper {
    
    public $helpers = array('Html');
    
/**
 * Generate link of location extend
 * 
 * @param string $title
 * @param id $locationId
 * @return string
 */
    public function locationExtend($title, $locationId) {
    	$link = $this->Html->link($title, 
    	        sprintf(Configure::read('Exlink.location.extend'), 
    	                $locationId));
    	return $link;
    }
    
/**
 * Generate link can go to news detail page.
 * 
 * @param string $title
 * @param id $messageId
 * @return string
 */
    public function newsDetail($title, $messageId, $options = array()) {
    	if($options['only_href']) {
    		$link = sprintf(Configure::read('Exlink.news.detail'), $messageId);
    	} else {
        	$link = $this->Html->link($title,
        		sprintf(Configure::read('Exlink.news.detail'),
        				$messageId), $options);
        }
        return $link;
    }
    
/**
 * 
 * return string the whole url of source image.
 */
    public function image($uri = '', $default = false) {
        if(!$default) {
            if($uri) {
            	return Configure::read('Domain.image').$uri;
            }
        } 
        return Configure::read('Domain.image').Configure::read('AutoReplyMessage.default_message_original');
    }
    
/**
 * 
 * return string the whole url of thumbnail image.
 */
    public function thumbnail($uri, $default = false) {
        if(!$default) {
        	if($uri) {
        		return Configure::read('Domain.image').$uri;
        	}
        }
        return Configure::read('Domain.image').Configure::read('AutoReplyMessage.default_message_thumbnail');
    }
    
    /**
     * <a data-id="<?php echo $item['AutoReplyMessage']['id']?>" data-url="/auto_reply_messages/delete/" 
            data-toggle="modal" role="button" href="#delete_msg" class="delete-link">删除</a>
     */
    public function remove($url, $id) {
        return $this->Html->link('<i class="icon-delete"></i>', '#delete_msg', array(
            'data-id' => $id,
            'data-url' => $url,
            'data-del' => 'tr',
            'class' => 'delete-link',
            'role' => 'button',
            'data-toggle' => 'modal',
            'escape' => false,
        ));
    }
}