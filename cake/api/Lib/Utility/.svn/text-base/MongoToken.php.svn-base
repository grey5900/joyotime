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
 * @since         FishSaying(tm) v 0.0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class MongoToken {
    
    private $table = array();
    
    public function __construct() {
        $this->table = array(
            0 => '0',
            1 => '1',
            2 => '2',
            3 => '3',
            4 => '4',
            5 => '5',
            6 => '6',
            7 => '7',
            8 => '8',
            9 => '9',
            10 => 'a',
            11 => 'b',
            12 => 'c',
            13 => 'd',
            14 => 'e',
            15 => 'f',
            16 => 'g',
            17 => 'h',
            18 => 'i',
            19 => 'j',
            20 => 'k',
            // skip 'l'
            21 => 'm',
            22 => 'n',
            // skip 'o'
            23 => 'p',
            24 => 'q',
            25 => 'r',
            26 => 's',
            27 => 't',
            28 => 'u',
            29 => 'v',
            30 => 'w',
            31 => 'x',
            32 => 'y',
            33 => 'z',
            34 => 'A',
            35 => 'B',
            36 => 'C',
            37 => 'D',
            38 => 'E',
            39 => 'F',
            40 => 'G',
            41 => 'H',
            // skip 'I'
            42 => 'J',
            43 => 'K',
            44 => 'L',
            45 => 'M',
            46 => 'N',
            // skip 'O'
            47 => 'P',
            48 => 'Q',
            49 => 'R',
            50 => 'S',
            51 => 'T',
            52 => 'U',
            53 => 'V',
            54 => 'W',
            55 => 'X',
            56 => 'Y',
            57 => 'Z',
        );
    }
    
    private function dec2short($num) {
        $scale = 58;
        $result = array();
        while($num) {
            $mod = $num % $scale;
            $result[] = $mod;
            $num = floor($num / $scale);
        }
        
        $str = '';
        $len = count($result);
        // build string with reverse...
        for($i = $len - 1; $i >= 0; $i--) {
            $str .= $this->table[$result[$i]];
        }
        
        return $str;
    }
    
    public function generate($id) {
        $id = new MongoId($id);
        $time = $id->getTimestamp();
        $counter = intval(substr($id->getInc(), -3, 3));
        $str = $this->dec2short($time . $counter);
        $str = substr($str, 1, strlen($str));    // cut off first character
        return $str;
    }
}