<?php
namespace Model\Daily;

class SignIn extends \AppModel {
    
    public $name = '\Model\Daily\SignIn';
    
    public $useDbConfig = 'redis'; // Defined at app/Config/database.php
    
/**
 * @var Redis
 */
    protected $redis;
    
/**
 * notification:$userId:count
 * 
 * @var string
 */
    private $key = 'ds:';        // abbr. daily signin
    private $keyDC = 'dsdc:';    // abbr. daily signin device code
    
    public function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        $this->redis = $this->getDataSource()->getInstance();
    }
    
    public function exist($userId, $dc) {
        return $this->redis->exists($this->keyUser($userId)) 
            || $this->redis->exists($this->keyDeviceCode($dc));
    }
    
    public function add($userId, $dc, $gmt = 8) {
        if($gmt == -11) $gmt = 14;    // An exception... GMT-12 not used.
        if($gmt == -9)  $gmt = -8;    // An exception... GMT-9 is same with GMT-8.
        $now = time();
        $end = gmmktime(0, 0, 0, gmdate('n', $now), gmdate('j', $now) + 1, gmdate('Y', $now));
        $expire = $end - $now - $gmt * 3600;
        if($expire > 24 * 3600) $expire -= 24 * 3600;
        if($expire < 0) $expire += 24 * 3600;
        return $this->redis->set($this->keyUser($userId),      $expire)
            && $this->redis->set($this->keyDeviceCode($dc),    $expire) 
            && $this->redis->expire($this->keyDeviceCode($dc), $expire) 
            && $this->redis->expire($this->keyUser($userId),   $expire);
    }
    
/**
 * Calc rand award
 * 
 * @return number
 */
    public function award() {
        $award = 0;
        $rand = rand(0, 10000);
        if($rand <= 800)                  $award = rand(60, 120);
        if($rand > 800  && $rand <= 9689) $award = rand(121, 240);
        if($rand > 9689 && $rand <= 9889) $award = rand(241, 360);
        if($rand > 9889 && $rand <= 9989) $award = rand(361, 480);
        if($rand > 9989 && $rand <= 9999) $award = rand(481, 599);
        if($rand > 9999)                  $award = 600;
        return $award;
    }
    
    private function keyUser($userId) {
        return $this->key.$userId;
    }
    
    private function keyDeviceCode($dc) {
        return $this->keyDC.$dc;
    }
}