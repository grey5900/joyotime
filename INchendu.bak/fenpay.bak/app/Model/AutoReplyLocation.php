<?php
class AutoReplyLocation extends AppModel {
    
    public $name = 'AutoReplyLocation';
    
    public $actsAs = array('Containable');
    
    public $belongsTo = array(
        'ImageAttachment'
    );
    
    public $validate = array(
        'user_id' => array(
            'required' => array(
                'rule' => array(
                    'notEmpty' 
                ),
                'message' => '需要用户ID' 
            ) 
        ),
        'title' => array(
            'required' => array(
                'rule' => array(
                    'notEmpty' 
                ),
                'message' => '请填写标题' 
            ) 
        ),
        'latitude' => array(
            'required' => array(
                'rule' => array(
                    'notEmpty' 
                ),
                'message' => '请搜索地图获取经纬度' 
            ) 
        ),
        'longitude' => array(
            'required' => array(
                'rule' => array(
                    'notEmpty' 
                ),
                'message' => '请搜索地图获取经纬度' 
            ) 
        )
    );
    
    public $hasMany = array(
        'AutoReplyLocationMessage' 
    );

    public $hasAndBelongsToMany = array(
        'AutoReplyMessage' => array(
            'className' => 'AutoReplyMessage',
            'joinTable' => 'auto_reply_location_messages',
            'foreignKey' => 'auto_reply_location_id',
            'associationForeignKey' => 'auto_reply_message_id',
            'with' => 'AutoReplyLocationMessage',
            'unique' => true 
        ) 
    );
    
/**
 * (non-PHPdoc)
 * @see Model::beforeSave()
 */
    public function beforeSave($options = array()) {
        $this->data[$this->name]['user_id'] = CakeSession::read('Auth.User.id');
    }
    
/**
 * Get the closest locations according user's current location.
 * 
 * @param double $lat
 * @param double $lon
 * @param number $limit
 * @return array
 */
    public function getLocations($lat, $lon, $userId, $limit = 9) {
        $order = 'ACOS(
    		SIN(
    			(' . $lat . ' * 3.1415) / 180
    		) * SIN((AutoReplyLocation.latitude * 3.1415) / 180) + COS(
    			(' . $lat . ' * 3.1415) / 180
    		) * COS((AutoReplyLocation.latitude * 3.1415) / 180) * COS(
    			(' . $lon . ' * 3.1415) / 180 - (AutoReplyLocation.longitude * 3.1415) / 180
    		)
    	) * 6380 ASC';
        
        return $this->find('all', array(
            'conditions' => array(
                'AutoReplyLocation.latitude >' => $lat - 1,
                'AutoReplyLocation.latitude <' => $lat + 1,
                'AutoReplyLocation.longitude >' => $lon - 1,
                'AutoReplyLocation.longitude <' => $lon + 1,
                'AutoReplyLocation.user_id' => $userId,
            ),
            'order' => array(
                $order 
            ),
            'limit' => $limit 
        ));
    }
    
/**
 * Get the closest locations nearby 111km limited by $limit.
 *
 * @param double $lat
 * @param double $lon
 * @param int $userId
 * @param number $limit
 * @return array
 */
    public function getLocationExtends($lat, $lon, $userId, $limit = 9) {
    	$order = 'ACOS(
    		SIN(
    			('.$lat.' * 3.1415) / 180
    		) * SIN((AutoReplyLocation.latitude * 3.1415) / 180) + COS(
    			('.$lat.' * 3.1415) / 180
    		) * COS((AutoReplyLocation.latitude * 3.1415) / 180) * COS(
    			('.$lon.' * 3.1415) / 180 - (AutoReplyLocation.longitude * 3.1415) / 180
    		)
    	) * 6380 ASC';
    
    	$data = $this->find('first', array(
            'contain' => array(
                'ImageAttachment',
                'AutoReplyMessage' => array(
                    'order' => array(
                        'AutoReplyMessage.id' => 'desc'
                    )
                ),
                'AutoReplyMessage.AutoReplyMessageNews',
                'AutoReplyMessage.AutoReplyMessageNews.ImageAttachment' 
            ),
            'conditions' => array(
                'AutoReplyLocation.latitude >' => $lat - 1,
                'AutoReplyLocation.latitude <' => $lat + 1,
                'AutoReplyLocation.longitude >' => $lon - 1,
                'AutoReplyLocation.longitude <' => $lon + 1,
                'AutoReplyLocation.user_id' => $userId 
            ),
            'order' => array(
                $order,
            ),
    	    'limit' => $limit
        ));
    	$chunks = array_chunk($data['AutoReplyMessage'], $limit, true);
    	$data['AutoReplyMessage'] = $chunks[0];
    	return $data;
    }
    
/**
 * Plus 1 on field of request_total
 *
 * @param array $ids
 */
    public function increaseRequestTotal($ids) {
        return $this->updateAll(array(
            'AutoReplyLocation.request_total' => 'AutoReplyLocation.request_total + 1' 
        ), array(
            'AutoReplyLocation.id' => $ids 
        ));
    }
}