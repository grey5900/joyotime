<?php
/**
 * The project of FishSaying is a SNS platform which is
 * based on voice sharing for each other with journey.
 *
 * The RESTful style API is used to communicate with each client-side.
 *
 * PHP 5
 *
 * FishSaying(tm) : FishSaying (http://www.fishsaying.com)
 * Copyright (c) fishsaying.com. (http://fishsaying.com)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) fishsaying.com. (http://www.fishsaying.com)
 * @link          http://fishsaying.com FishSaying(tm) Project
 * @since         FishSaying(tm) v 1.0.0
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
require_once(VENDORS."qiniu/qiniu/rs.php");
APP::uses('Component', 'Controller/Component');
/**
 * The class is wrapped API of QiNiu which is a cloud file platform,
 * and provide a convinence way to invoke those APIs in OO style.
 *
 * @package app.Controller.Component
 */
class QiNiuComponent extends Component {
    
    const BUCKET_COVER = 'fs-cover';
    const BUCKET_VOICE = 'fs-voice';
    const BUCKET_AVATAR = 'fs-avatar';
    
    const DOMAIN = 'fishsaying.com';
    
/**
 * The token of upload
 * @param unknown $bucket
 * @return string
 */
    public function uploadToken($bucket) {
        $cacheKey = "cache:$bucket:upload:token";
        
        $upToken = Cache::read($cacheKey, 'QiNiu');
        
        if(!$upToken) {
            Qiniu_SetKeys(Configure::read('QINIU_ACCESS_KEY'), Configure::read('QINIU_SECRET_KEY'));
            $putPolicy = new Qiniu_RS_PutPolicy($bucket);
            $putPolicy->Expires = Configure::read('QINIU_UP_TOKEN_EXPIRE');    // upload token will be expired over 1 day.
            $upToken = $putPolicy->Token(null);
            Cache::write($cacheKey, $upToken, 'QiNiu');
        }
        return $upToken;
    }
    
/**
 * Get access url of private file by specified bucket and key.
 *  
 * @param string $bucket
 * @param string $key
 * @return string
 */
    public function getPrivateUrl($bucket, $key) {
        $domain = $this->getDomain($bucket, true);
        $privateUrl = Cache::read($this->getKeyForPrivateUrl($domain, $key), 'QiNiu');
        
        if(!$privateUrl) {
            Qiniu_SetKeys(Configure::read('QINIU_ACCESS_KEY'), Configure::read('QINIU_SECRET_KEY'));
            $baseUrl = Qiniu_RS_MakeBaseUrl($domain, $key);
            $getPolicy = new Qiniu_RS_GetPolicy();
            $getPolicy->Expires = Configure::read('QINIU_PRIVATE_URL_EXPIRE');    // 1 week
            $privateUrl = $getPolicy->MakeRequest($baseUrl, null);
            Cache::write($this->getKeyForPrivateUrl($domain, $key), $privateUrl, 'QiNiu');
        }
        return $privateUrl;
    }
    
    public function getTrialVoice($key) {
        $cacheKey = "cache:$key:voice:trial";
        $shareUrl = Cache::read($cacheKey, 'QiNiu');
        
        if(!$shareUrl) {
            $domain = $this->getDomain(self::BUCKET_VOICE, true);
            Qiniu_SetKeys(Configure::read('QINIU_ACCESS_KEY'), Configure::read('QINIU_SECRET_KEY'));
            $baseUrl = Qiniu_RS_MakeBaseUrl($domain, $key);
            $extension = 'mp3';
            $seekStart = 0;
            $duration = 30;
            $baseUrl .= "?avthumb/$extension/ss/$seekStart/t/$duration";
            $getPolicy = new Qiniu_RS_GetPolicy();
            $getPolicy->Expires = Configure::read('QINIU_TRIAL_URL_EXPIRE');    // 50 years
            $shareUrl = $getPolicy->MakeRequest($baseUrl, null);
            Cache::write($cacheKey, $shareUrl, 'QiNiu');
        }
        return $shareUrl;
    }
    
/**
 * Get domain
 * 
 * @param string $bucket
 * @return string
 */
    public function getDomain($bucket, $withoutHTTP = false) {
        $bucket = str_replace('fs-', '', $bucket);
        if($withoutHTTP) {
            return $bucket.'.'.self::DOMAIN;
        } else {
            return 'http://'.$bucket.'.'.self::DOMAIN;
        }
    }
    
/**
 * Get cache key for private url
 * 
 * @param string $domain
 * @param string $key
 * @return string
 */
    private function getKeyForPrivateUrl($domain, $key) {
        $cacheKey = "cache:$domain:private_url:$key";
        return $cacheKey;
    }
}