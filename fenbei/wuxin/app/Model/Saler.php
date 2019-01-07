<?php
class Saler extends AppModel {
    
    public $name = 'Saler';
    
    public $belongsTo = array(
		'Shop',
    );
    
    public $validate = array(
        'name' => array(
            'required' => array(
                'rule' => array(
                    'notEmpty' 
                ),
                'message' => '缺少收银员名字' 
            ),
        ),
        'shop_id' => array(
            'required' => array(
                'rule' => array(
                    'notEmpty' 
                ),
                'message' => '缺少店铺' 
            ) 
        ),
        'token' => array(
            'required' => array(
                'rule' => array(
                    'notEmpty' 
                ),
                'message' => '缺少微信绑定编码' 
            ) 
        ),
    );
    
/**
 * (non-PHPdoc)
 * @see Model::afterSave()
 */
    public function afterSave($created) {
        $saler = $this->read(null, $this->id);
        if(!$saler[$this->name]['token']) {
            $saler[$this->name]['token'] = $this->geneToken($this->id);
            $this->save($saler);
        }
    }
    
/**
 * Return a random token
 * 
 * @param int $saler_id
 * @return string
 */
    public function geneToken($saler_id) {
        $len = 8;
        return substr(md5(uniqid($saler_id, true)), 0, $len);
    }
}