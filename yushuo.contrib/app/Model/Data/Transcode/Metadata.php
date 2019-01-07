<?php
namespace Model\Data\Transcode;

class Metadata {
    
    public $name = '\Model\Data\Transcode\Metadata';
    
    public $uniqid = '';
    
    public $userId = '';
    
    public $authToken = '';
    
    public $status = '';
    
    public $created = '';
    
    public $modified = '';
    
    const PREFIX = 'transcode';
    
    const STATUS_PENDING = 'pending';
    const STATUS_ENCODE_FAIL = 'encode_fail';
    const STATUS_POST_FAIL = 'post_fail';
    
    public function __construct($userId, $auth) {
        $this->uniqid = uniqid(self::PREFIX);
        $this->userId = $userId;
        $this->authToken = $auth;
        $this->status = self::STATUS_PENDING;
        $this->created = time();
        $this->modified = time();
    }
}