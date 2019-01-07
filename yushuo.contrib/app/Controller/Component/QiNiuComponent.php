<?php
/**
 * The project of FishSaying is a SNS platform which is
 * based on voice sharing for each other with journey.
 *
 * The contributor platform is used to CP create/publish costomize content.
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
require_once(VENDORS."qiniu/qiniu/io.php");

/**
 * The class is wrapped API of QiNiu which is a cloud file platform,
 * and provide a convinence way to invoke those APIs in OO style.
 *
 * @package app.Controller.Component
 */
class QiNiuComponent extends Component {
    
    const BUCKET_COVER = 'fishsaying-cover';
    const BUCKET_VOICE = 'fishsaying-voice';
    const BUCKET_AVATAR = 'fishsaying-avatar';
    
    const DOMAIN = 'qiniudn.com';
    
/**
 * Get domain
 * 
 * @param string $bucket
 * @return string
 */
    public function getDomain($bucket) {
        return 'http://'.$bucket.'.'.self::DOMAIN;
    }
    
/**
 * Upload file to Qiniu server
 * 
 * @param string $upToken The token was generated by API server 
 * @param string $filepath The absolute path of file in local
 * @param string $filename
 * @throws QiNiuUploadExcepiton
 * @return string The filename created by QiNiu.com
 */
    public function upload($upToken, $filepath, $filename = null) {
        $putExtra = new Qiniu_PutExtra();
        $putExtra->Crc32 = 1;
        list($ret, $err) = Qiniu_PutFile($upToken, $filename, $filepath, $putExtra);
        if ($err !== null) {
        	throw new QiNiuUploadExcepiton($err->Err, $err->Code);
        } else {
        	return $ret['key'];
        }
    }
}

class QiNiuUploadExcepiton extends Exception {
    
}