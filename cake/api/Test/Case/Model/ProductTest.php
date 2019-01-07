<?php
APP::uses('AppTestCase', 'Test/Case/Model');
/**
 * @package       app.Test.Case.Model
 */
class ProductTest extends AppTestCase {
    
    public $fixtures = array(
    );
    
    public function getModelName() {
        return 'Product';
    }
    
    public function setUp() {
        parent::setUp();
    }
    
    public function testAlipay() {
        $this->assertEqual(is_array($this->model->alipay()), true);
        $pro = $this->model->alipay('alipay_1');
        $this->assertEqual($pro['id'], 'alipay_1');
        $this->assertEqual($pro['seconds'], 1200);
        $pro = $this->model->alipay('alipay_3');
        $this->assertEqual($pro['id'], 'alipay_3');
        $this->assertEqual($pro['seconds'], 6000);
    }
    
    public function testIos() {
        $this->assertEqual($this->model->ios(), Configure::read('AppStore.Product'));
        $this->assertEqual($this->model->ios('com.fishsaying.product.20'), 1200);
    }
    
    public function dropData(Model $model) {
        return ;
    }
}