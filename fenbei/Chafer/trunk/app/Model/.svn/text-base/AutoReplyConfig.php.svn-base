<?php
class AutoReplyConfig extends AppModel {
    
    public $name = 'AutoReplyConfig';
    
    const EVT_SUBSCRIBE = 'subscribe';
    const EVT_NOANSWER  = 'noanswer';
    
	public $hasAndBelongsToMany = array(
		'AutoReplyTag' => array(
			'className' => 'AutoReplyTag',
			'joinTable' => 'auto_reply_config_tags',
			'foreignKey' => 'auto_reply_config_id',
			'associationForeignKey' => 'auto_reply_tag_id',
			'with' => 'AutoReplyConfigTag',
			'unique' => true,
		)
	);
	
	public $hasMany = array(
		'AutoReplyConfigTag'
	);
	
	public function beforeSave($options = array()) {
		$this->data[$this->name]['user_id'] = CakeSession::read('Auth.User.id');
		if(isset($this->data[$this->name]['id'])) {
			ClassRegistry::init('AutoReplyConfigTag')->deleteAll(
				array('auto_reply_config_id' => $this->data[$this->name]['id']), false);
		}
	}
}