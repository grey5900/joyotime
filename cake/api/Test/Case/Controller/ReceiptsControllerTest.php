<?php
APP::uses('AppControllerTestCase', 'Test/Case/Controller');
APP::uses('Checkout', 'Model');

class ReceiptsControllerTest extends AppControllerTestCase {
    
    public $fixtures = array(
    	'app.user',
    	'app.receipt',
    );
    
/**
 * (non-PHPdoc)
 * @see AppControllerTestCase::getModelName()
 */
    public function getModelName() {
    	return 'Receipt';
    }
    
/**
 * (non-PHPdoc)
 * @see AppControllerTestCase::getControllerName()
 */
    public function getControllerName() {
    	return 'Receipts';
    }
    
/**
 * (non-PHPdoc)
 * @see CakeTestCase::setUp()
 */
    public function setUp() {
    	parent::setUp();
    	
    	$fixUser = new UserFixture();
    	$this->fix['user1'] = $fixUser->records[0];
    	$this->fix['user2'] = $fixUser->records[1];
    	
    	$this->User = ClassRegistry::init('User');
    	$this->Checkout = ClassRegistry::init('Checkout');
    }
    
    public function testAlipay() {
        $proId = 'alipay_1';
        
        // Assert success...
        $result = $this->testAction("/receipts/alipay.json?auth_token={$this->userToken}&api_key={$this->apikey}", array(
            'method' => 'POST',
            'data' => array(
                'pro_id' => $proId,
            )
        ));
        $this->assertEqual($result, true);
        
        $co = $this->model->findById($this->vars['root']['result']['_id']);
        $co = $co[$this->model->name];
        $this->assertEqual($co['user_id'], $this->userId);
        $this->assertEqual($co['type'], Receipt::TYPE_ALIPAY);
        $this->assertEqual($co['amount']['time'], 1200);
        $this->assertEqual($co['status'], Receipt::STATUS_PENDING);
    }
    
    public function testIos() {
        $receiptData = 'ewoJInNpZ25hdHVyZSIgPSAiQW1qTko5VHFhMUdHR1hTSTlLUlQyekREbzYxaTYza1Nsa3VmT0dRUytFR1puVmpGZ05PU3JacTIvZUtvWHdZSUdleXByRks2SllvemhZcHZJM3hEaGovelBEejFGc1hyMWdlQnZuRGdWM01sVnhSSnJlcmlVd0lNWGtGb3pRUk1ySXdaWDg2L2VCYXMzZXBNM05tb09KS25VbHlZOVA5WWVXVG9iK3JIZFpSeUFBQURWekNDQTFNd2dnSTdvQU1DQVFJQ0NHVVVrVTNaV0FTMU1BMEdDU3FHU0liM0RRRUJCUVVBTUg4eEN6QUpCZ05WQkFZVEFsVlRNUk13RVFZRFZRUUtEQXBCY0hCc1pTQkpibU11TVNZd0pBWURWUVFMREIxQmNIQnNaU0JEWlhKMGFXWnBZMkYwYVc5dUlFRjFkR2h2Y21sMGVURXpNREVHQTFVRUF3d3FRWEJ3YkdVZ2FWUjFibVZ6SUZOMGIzSmxJRU5sY25ScFptbGpZWFJwYjI0Z1FYVjBhRzl5YVhSNU1CNFhEVEE1TURZeE5USXlNRFUxTmxvWERURTBNRFl4TkRJeU1EVTFObG93WkRFak1DRUdBMVVFQXd3YVVIVnlZMmhoYzJWU1pXTmxhWEIwUTJWeWRHbG1hV05oZEdVeEd6QVpCZ05WQkFzTUVrRndjR3hsSUdsVWRXNWxjeUJUZEc5eVpURVRNQkVHQTFVRUNnd0tRWEJ3YkdVZ1NXNWpMakVMTUFrR0ExVUVCaE1DVlZNd2daOHdEUVlKS29aSWh2Y05BUUVCQlFBRGdZMEFNSUdKQW9HQkFNclJqRjJjdDRJclNkaVRDaGFJMGc4cHd2L2NtSHM4cC9Sd1YvcnQvOTFYS1ZoTmw0WElCaW1LalFRTmZnSHNEczZ5anUrK0RyS0pFN3VLc3BoTWRkS1lmRkU1ckdYc0FkQkVqQndSSXhleFRldngzSExFRkdBdDFtb0t4NTA5ZGh4dGlJZERnSnYyWWFWczQ5QjB1SnZOZHk2U01xTk5MSHNETHpEUzlvWkhBZ01CQUFHamNqQndNQXdHQTFVZEV3RUIvd1FDTUFBd0h3WURWUjBqQkJnd0ZvQVVOaDNvNHAyQzBnRVl0VEpyRHRkREM1RllRem93RGdZRFZSMFBBUUgvQkFRREFnZUFNQjBHQTFVZERnUVdCQlNwZzRQeUdVakZQaEpYQ0JUTXphTittVjhrOVRBUUJnb3Foa2lHOTJOa0JnVUJCQUlGQURBTkJna3Foa2lHOXcwQkFRVUZBQU9DQVFFQUVhU2JQanRtTjRDL0lCM1FFcEszMlJ4YWNDRFhkVlhBZVZSZVM1RmFaeGMrdDg4cFFQOTNCaUF4dmRXLzNlVFNNR1k1RmJlQVlMM2V0cVA1Z204d3JGb2pYMGlreVZSU3RRKy9BUTBLRWp0cUIwN2tMczlRVWU4Y3pSOFVHZmRNMUV1bVYvVWd2RGQ0TndOWXhMUU1nNFdUUWZna1FRVnk4R1had1ZIZ2JFL1VDNlk3MDUzcEdYQms1MU5QTTN3b3hoZDNnU1JMdlhqK2xvSHNTdGNURXFlOXBCRHBtRzUrc2s0dHcrR0szR01lRU41LytlMVFUOW5wL0tsMW5qK2FCdzdDMHhzeTBiRm5hQWQxY1NTNnhkb3J5L0NVdk02Z3RLc21uT09kcVRlc2JwMGJzOHNuNldxczBDOWRnY3hSSHVPTVoydG04bnBMVW03YXJnT1N6UT09IjsKCSJwdXJjaGFzZS1pbmZvIiA9ICJld29KSW05eWFXZHBibUZzTFhCMWNtTm9ZWE5sTFdSaGRHVXRjSE4wSWlBOUlDSXlNREV6TFRFeExURTBJREl3T2pJNE9qQTVJRUZ0WlhKcFkyRXZURzl6WDBGdVoyVnNaWE1pT3dvSkluVnVhWEYxWlMxcFpHVnVkR2xtYVdWeUlpQTlJQ0l5TURSallUTXdORGhrWVRBM1pEZzBNemN3WlRRNVkyVmhNRFl4TVdKa05USmpNak14TnpabUlqc0tDU0p2Y21sbmFXNWhiQzEwY21GdWMyRmpkR2x2YmkxcFpDSWdQU0FpTVRBd01EQXdNREE1TXpRNE5EY3dNQ0k3Q2draVluWnljeUlnUFNBaU1DNHpMakFpT3dvSkluUnlZVzV6WVdOMGFXOXVMV2xrSWlBOUlDSXhNREF3TURBd01Ea3pORGcwTnpBd0lqc0tDU0p4ZFdGdWRHbDBlU0lnUFNBaU1TSTdDZ2tpYjNKcFoybHVZV3d0Y0hWeVkyaGhjMlV0WkdGMFpTMXRjeUlnUFNBaU1UTTRORFE0T1RZNE9UTXdPU0k3Q2draWRXNXBjWFZsTFhabGJtUnZjaTFwWkdWdWRHbG1hV1Z5SWlBOUlDSkVPRFJGT0RVeU9DMDJORFpHTFRSRU9UZ3RRa1pEUlMxQ016WTFPRGcyTkRkQk1FVWlPd29KSW5CeWIyUjFZM1F0YVdRaUlEMGdJbU52YlM1bWFYTm9jMkY1YVc1bkxuQnliMlIxWTNRdU5EQXdJanNLQ1NKcGRHVnRMV2xrSWlBOUlDSTNORGM0TmpBeE5UWWlPd29KSW1KcFpDSWdQU0FpWTI5dExtWnBjMmh6WVhscGJtY3VhWEJvYjI1bElqc0tDU0p3ZFhKamFHRnpaUzFrWVhSbExXMXpJaUE5SUNJeE16ZzBORGc1TmpnNU16QTVJanNLQ1NKd2RYSmphR0Z6WlMxa1lYUmxJaUE5SUNJeU1ERXpMVEV4TFRFMUlEQTBPakk0T2pBNUlFVjBZeTlIVFZRaU93b0pJbkIxY21Ob1lYTmxMV1JoZEdVdGNITjBJaUE5SUNJeU1ERXpMVEV4TFRFMElESXdPakk0T2pBNUlFRnRaWEpwWTJFdlRHOXpYMEZ1WjJWc1pYTWlPd29KSW05eWFXZHBibUZzTFhCMWNtTm9ZWE5sTFdSaGRHVWlJRDBnSWpJd01UTXRNVEV0TVRVZ01EUTZNamc2TURrZ1JYUmpMMGROVkNJN0NuMD0iOwoJImVudmlyb25tZW50IiA9ICJTYW5kYm94IjsKCSJwb2QiID0gIjEwMCI7Cgkic2lnbmluZy1zdGF0dXMiID0gIjAiOwp9';
        // Assert success...
        $result = $this->testAction("/receipts/ios.json?auth_token={$this->userToken}&api_key={$this->apikey}", array(
            'method' => 'POST',
            'data' => array(
                'receipt' => $receiptData,
            )
        ));
        $this->assertEqual($result, true);
        
        $receipt = $this->model->findById($this->vars['root']['result']['_id'])[$this->model->name];
        $this->assertEqual($receipt['user_id'], $this->userId);
        $this->assertEqual($receipt['type'], Receipt::TYPE_IOS);
        $this->assertEqual($receipt['amount']['time'], 24000);
        $this->assertEqual($receipt['status'], Receipt::STATUS_PAID);
        $this->assertEqual($receipt['receipt']['raw'], $receiptData);
        $this->assertEqual(is_array($receipt['receipt']['data']), true);
        $this->assertEqual($receipt['receipt']['identify'], md5($receiptData));
        
        $user = $this->User->findById($this->userId)['User'];
        $this->assertEqual($user['income'], $this->fix['user1']['income'] + $receipt['amount']['time']);
        $this->assertEqual($user['money'], $this->fix['user1']['money'] + $receipt['amount']['time']);
        $this->assertEqual($user['earn'], $this->fix['user1']['earn']);
        
        $co = $this->Checkout->findByUserId($this->userId)['Checkout'];
        $this->assertEqual($co['user_id'], $this->userId);
        $this->assertEqual($co['type'], Checkout::TYPE_PAYMENT);
        $this->assertEqual($co['amount']['time'], $receipt['amount']['time']);
        $this->assertEqual($co['amount']['gateway'], Receipt::TYPE_IOS);
    }
    
    public function testEdit() {
        $receiptId = '61fff30f6f159ddd6fad8ce3';
        
        $apikey = Configure::read('Payment.Access.Token');
        
        // Assert success...
        $result = $this->testAction(
                "/receipts/{$receiptId}.json?api_key={$apikey}", 
                array('method' => 'PUT'));
        $this->assertEqual($result, true);
        
        $receipt = $this->model->findById($receiptId)[$this->model->name];
        $this->assertEqual($receipt['user_id'], $this->userId);
        $this->assertEqual($receipt['type'], Receipt::TYPE_ALIPAY);
        $this->assertEqual($receipt['amount']['time'], 1200);
        $this->assertEqual($receipt['status'], Receipt::STATUS_PAID);
        
        $user = $this->User->findById($this->userId)['User'];
        $this->assertEqual($user['income'], $this->fix['user1']['income'] + $receipt['amount']['time']);
        $this->assertEqual($user['money'], $this->fix['user1']['money'] + $receipt['amount']['time']);
        $this->assertEqual($user['earn'], $this->fix['user1']['earn']);
        
        $co = $this->Checkout->findByUserId($this->userId)['Checkout'];
        $this->assertEqual($co['user_id'], $this->userId);
        $this->assertEqual($co['type'], Checkout::TYPE_PAYMENT);
        $this->assertEqual($co['amount']['time'], $receipt['amount']['time']);
        $this->assertEqual($co['amount']['gateway'], Receipt::TYPE_ALIPAY);
    }
    
    public function testView() {
        $receiptId = '61fff30f6f159ddd6fad8ce3';
        
        // Assert success...
        $result = $this->testAction(
                "/receipts/{$receiptId}.json?auth_token={$this->userToken}&api_key={$this->apikey}", 
                array('method' => 'GET'));
        $this->assertEqual($result, true);
    }
}