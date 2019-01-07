<?php
/*
 * 身份证验证
 *
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-2-9
 */

class Validator_idcard {

    var $_idNum;

    function __construct($idNum) {
        $this->_idNum = (string)$idNum;
    }

    function isIdNum() {
        $idNum = $this->_idNum;
        if (strlen($idNum) == 18) {
            return $this->idcardCheckSum18($idNum);
        } elseif ((strlen($idNum) == 15)) {
            $idNum = $this->idcard_15to18($idNum);
            return $this->idcardCheckSum18('"' . $idNum . '"');
        } else {
            return false;
        }
    }

    // 计算身份证校验码，根据国家标准GB 11643-1999
    function idcardVerifyNumber($idcardBase) {
        if (strlen($idcardBase) != 17) {
            return false;
        }

        //加权因子
        $factor = array(
                7,
                9,
                10,
                5,
                8,
                4,
                2,
                1,
                6,
                3,
                7,
                9,
                10,
                5,
                8,
                4,
                2
        );

        //校验码对应值
        $verify_number_list = array(
                '1',
                '0',
                'X',
                '9',
                '8',
                '7',
                '6',
                '5',
                '4',
                '3',
                '2'
        );
        $checksum = 0;
        for ($i = 0; $i < strlen($idcardBase); $i++) {
            $checksum += substr($idcardBase, $i, 1) * $factor[$i];
        }
        $mod = $checksum % 11;
        $verify_number = $verify_number_list[$mod];
        return $verify_number;
    }

    // 将15位身份证升级到18位
    function idcard_15to18($idcard) {
        if (strlen($idcard) != 15) {
            return false;
        } else {

            // 如果身份证顺序码是996 997 998 999，这些是为百岁以上老人的特殊编码
            if (array_search(substr($idcard, 12, 3), array(
                    '996',
                    '997',
                    '998',
                    '999'
            )) !== false) {
                $idcard = substr($idcard, 0, 6) . '18' . substr($idcard, 6, 9);
            } else {
                $idcard = substr($idcard, 0, 6) . '19' . substr($idcard, 6, 9);
            }
        }
        $idcard = $idcard . idcard_verify_number($idcard);
        return $idcard;
    }

    // 18位身份证校验码有效性检查
    function idcardCheckSum18($idcard) {
        if (strlen($idcard) != 18) {
            return false;
        }
        $idcard_base = substr($idcard, 0, 17);
        if ($this->idcardVerifyNumber($idcard_base) != strtoupper(substr($idcard, 17, 1))) {
            return false;
        } else {
            return true;
        }
    }

}
