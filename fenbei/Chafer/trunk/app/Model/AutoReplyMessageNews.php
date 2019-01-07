<?php
APP::uses('AppModel', 'Model');
class AutoReplyMessageNews extends AppModel {
    
    public $name = 'AutoReplyMessageNews';
    public $tableName = 'auto_reply_message_news';
    
    public $belongsTo = array(
        'AutoReplyCategory',
        'ImageAttachment',
        'AutoReplyMessage'
    );
    
/**
 * It's hybrid with image and text, also called news mode.
 * @var string
 */
    const CUSTOM = 'custom';
/**
 * Another hybird with image and link, the request 
 * would be redirected to the specify link.
 * @var string
 */
    const LINK = 'link';
/**
 * Another hybird with image and map.
 * Note: the type has not been implemented yet.
 * @var string
 */
    const MAP = 'map';
/**
 * The next type would return next result-set 
 * for any types defined in AutoReplyMessageNews.
 * @var string
 */
    const NEXT = 'next';

    public $validate = array(
        'title' => array(
            'required' => array(
                'rule' => array(
                    'notEmpty' 
                ),
                'message' => '标题不能为空' 
            ) 
        ),
    );
    
/**
 * (non-PHPdoc)
 * @see Model::beforeSave()
 */
    public function beforeSave($options = array()) {
    	// Updating counter of category while editing...
    	if(isset($this->data[$this->name]['auto_reply_message_id']) 
    	    && !empty($this->data[$this->name]['auto_reply_message_id'])) {
    		$AutoReplyCategory = ClassRegistry::init('AutoReplyCategory');
    		$AutoReplyCategory->decreaseByAutoReplyMessageId(
    		        $this->data[$this->name]['auto_reply_message_id']);
    	}
    }
    
/**
 * (non-PHPdoc)
 * @see Model::afterSave()
 */
    public function afterSave($created) {
    	if($this->data[$this->name]['auto_reply_category_id']) {
    	    $AutoReplyCategory = ClassRegistry::init('AutoReplyCategory');
    		$AutoReplyCategory->increase($this->data[$this->name]['auto_reply_category_id']);
    	}
    }

/**
 * Plus 1 on field of view_total
 * @param array $auto_reply_message_ids
 */
    public function increaseViewTotal($auto_reply_message_ids) {
    	return $this->updateAll(array(
    		$this->name.'.view_total' => $this->name.'.view_total + 1'
    	), array(
    		$this->name.'.auto_reply_message_id' => $auto_reply_message_ids
    	));
    }
}