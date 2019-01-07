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

/**
 * @package app.Controller.Component
 */
class FileInfoComponent extends Component {
    
    public function info($filename) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
        $type = finfo_file($finfo, $filename);
        finfo_close($finfo);
        return $type;
    }
    
    public function isMP4($filename) {
        return (bool) preg_match('/mp4/i', $this->info($filename));
    }
    
    public function isMP3($filename) {
        return 'audio/mpeg' == $this->info($filename);
    }
}