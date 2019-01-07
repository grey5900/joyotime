<?php
APP::uses('Security', 'Utility');
APP::uses('Voice', 'Model');

class VoiceFixture extends CakeTestFixture {
	public $import = array('model' => 'Voice');
	
    public function init() {
        $this->records = array(
            array(
                '_id' => new MongoId('51f225e26f159afa43e76aff'),
                'user_id' => '51f0c30f6f159aec6fad8ce3',
                'title' => 'voice title first',
                'description' => 'mockup description',
                'tags' => array('title', 'good', 'yes'),
                'cover' => array(
                    'source' => '/data/tmp/source',
                    'x80' => '/data/tmp/80x80',
                    'x160' => '/data/tmp/160x160',
                    'x640' => '/data/tmp/640x640',
                ), 
                'length' => 180,                  // unit second
                'voice' => '/data/tmp/file',      // The path of voice itself.
                'status' => Voice::STATUS_APPROVED,            // The available values are including pending(等待审核)/approved(通过审核)/invalid(未通过)/下架
                'isfree' => false,                // 是否免费
                'location' => array('lat' => 38.186387, 'lng' => -96.617203),    // 语音的经纬度，便于查询
                'language' => 'zh_CN',            // which is language of voice?
                'checkout_total' => 0,            // 购买次数
                'earn_total' => 0,                // 被购买的时间总额
                'created' =>new MongoDate(),
                'modified'=>new MongoDate(),
                'score' => 0,
                'comment_total' => 0,
            ),
            array(
                '_id' => new MongoId('51f223956f159afa43a9681d'),
                'user_id' => '51f0c30f6f159aec6fad8ce3',
                'title' => 'voice title second',
                'cover' => array(
                    'source' => '/data/tmp/source',
                    'x80' => '/data/tmp/80x80',
                    'x160' => '/data/tmp/160x160',
                    'x640' => '/data/tmp/640x640',
                ), 
                'length' => 180,                  // unit second
                'voice' => '/data/tmp/file',      // The path of voice itself.
                'status' => Voice::STATUS_PENDING,            // The available values are including pending(等待审核)/approved(通过审核)/invalid(未通过)/下架
                'isfree' => false,                // 是否免费
                'location' => array('lat' => 38.186387, 'lng' => -96.617303),    // 语音的经纬度，便于查询
                'language' => 'zh_CN',            // which is language of voice?
                'checkout_total' => 0,            // 购买次数
                'earn_total' => 0,                // 被购买的时间总额
                'created' =>new MongoDate(),
                'modified'=>new MongoDate(),
                'score' => 0,
                'comment_total' => 0,
            ),
        );
        parent::init();
    } 
}