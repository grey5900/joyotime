<?php
APP::uses('AppTestCase', 'Test/Case/Model');
APP::uses('Receipt', 'Model');
/**
 * The user model test class
 *
 * @package       app.Test.Case.Model
 */
class ReceiptTest extends AppTestCase {
    
    public $fixtures = array(
        'app.user',
        'app.receipt',
    );
    
    public function getModelName() {
        return 'Receipt';
    }
    
    public function setUp() {
        parent::setUp();
        
        $fixUser = new UserFixture();
        $this->fix['user1'] = $fixUser->records[0];
        $this->fix['user2'] = $fixUser->records[1];
        
        $this->id = '61fff30f6f159ddd6fad8ce3';
    }
    
    public function testAlipay() {
        $userId = (string)$this->fix['user1']['_id'];
        $seconds = 1000;
        $result = $this->model->alipay($userId, $seconds)[$this->model->name];
        $this->assertEqual($result['user_id'], $userId);
        $this->assertEqual($result['type'], Receipt::TYPE_ALIPAY);
        $this->assertEqual($result['amount']['time'], $seconds);
        $this->assertEqual($result['status'], Receipt::STATUS_PENDING);
    }
    
    public function testIos() {
    	$userId = (string)$this->fix['user1']['_id'];
    	$seconds = 100;
    	$receipt = 'ewoJInNpZ25hdHVyZSIgPSAiQWdTOFcxWGw5SDdsQTdXVjFpRlBYbFVKVXM4cTlnZTE5by9kTEdlTHpHQlhFNmZjQnFKamF5bU53bUhtYjlCNlIyYVRzbitKcWQ3eVJTbUdkZTFremxNSUZEZGxkNEVQMStuRVNUL0YzcG85VEtQOEc4S1NGSWx4ZFJsV3BmNnhBRDJvdlR0R2dXcFB4bkxJQlVjS3NBTEkvUkpLbGQ5WG1ZL3diTkduS2NtTEFBQURWekNDQTFNd2dnSTdvQU1DQVFJQ0NHVVVrVTNaV0FTMU1BMEdDU3FHU0liM0RRRUJCUVVBTUg4eEN6QUpCZ05WQkFZVEFsVlRNUk13RVFZRFZRUUtEQXBCY0hCc1pTQkpibU11TVNZd0pBWURWUVFMREIxQmNIQnNaU0JEWlhKMGFXWnBZMkYwYVc5dUlFRjFkR2h2Y21sMGVURXpNREVHQTFVRUF3d3FRWEJ3YkdVZ2FWUjFibVZ6SUZOMGIzSmxJRU5sY25ScFptbGpZWFJwYjI0Z1FYVjBhRzl5YVhSNU1CNFhEVEE1TURZeE5USXlNRFUxTmxvWERURTBNRFl4TkRJeU1EVTFObG93WkRFak1DRUdBMVVFQXd3YVVIVnlZMmhoYzJWU1pXTmxhWEIwUTJWeWRHbG1hV05oZEdVeEd6QVpCZ05WQkFzTUVrRndjR3hsSUdsVWRXNWxjeUJUZEc5eVpURVRNQkVHQTFVRUNnd0tRWEJ3YkdVZ1NXNWpMakVMTUFrR0ExVUVCaE1DVlZNd2daOHdEUVlKS29aSWh2Y05BUUVCQlFBRGdZMEFNSUdKQW9HQkFNclJqRjJjdDRJclNkaVRDaGFJMGc4cHd2L2NtSHM4cC9Sd1YvcnQvOTFYS1ZoTmw0WElCaW1LalFRTmZnSHNEczZ5anUrK0RyS0pFN3VLc3BoTWRkS1lmRkU1ckdYc0FkQkVqQndSSXhleFRldngzSExFRkdBdDFtb0t4NTA5ZGh4dGlJZERnSnYyWWFWczQ5QjB1SnZOZHk2U01xTk5MSHNETHpEUzlvWkhBZ01CQUFHamNqQndNQXdHQTFVZEV3RUIvd1FDTUFBd0h3WURWUjBqQkJnd0ZvQVVOaDNvNHAyQzBnRVl0VEpyRHRkREM1RllRem93RGdZRFZSMFBBUUgvQkFRREFnZUFNQjBHQTFVZERnUVdCQlNwZzRQeUdVakZQaEpYQ0JUTXphTittVjhrOVRBUUJnb3Foa2lHOTJOa0JnVUJCQUlGQURBTkJna3Foa2lHOXcwQkFRVUZBQU9DQVFFQUVhU2JQanRtTjRDL0lCM1FFcEszMlJ4YWNDRFhkVlhBZVZSZVM1RmFaeGMrdDg4cFFQOTNCaUF4dmRXLzNlVFNNR1k1RmJlQVlMM2V0cVA1Z204d3JGb2pYMGlreVZSU3RRKy9BUTBLRWp0cUIwN2tMczlRVWU4Y3pSOFVHZmRNMUV1bVYvVWd2RGQ0TndOWXhMUU1nNFdUUWZna1FRVnk4R1had1ZIZ2JFL1VDNlk3MDUzcEdYQms1MU5QTTN3b3hoZDNnU1JMdlhqK2xvSHNTdGNURXFlOXBCRHBtRzUrc2s0dHcrR0szR01lRU41LytlMVFUOW5wL0tsMW5qK2FCdzdDMHhzeTBiRm5hQWQxY1NTNnhkb3J5L0NVdk02Z3RLc21uT09kcVRlc2JwMGJzOHNuNldxczBDOWRnY3hSSHVPTVoydG04bnBMVW03YXJnT1N6UT09IjsKCSJwdXJjaGFzZS1pbmZvIiA9ICJld29KSW05eWFXZHBibUZzTFhCMWNtTm9ZWE5sTFdSaGRHVXRjSE4wSWlBOUlDSXlNREV6TFRBNUxURTJJREl4T2pBeU9qUTNJRUZ0WlhKcFkyRXZURzl6WDBGdVoyVnNaWE1pT3dvSkluVnVhWEYxWlMxcFpHVnVkR2xtYVdWeUlpQTlJQ0l4T1dJNFkyVm1PVFptTnpGaU4yWTJPREE1TVRZME5HWmpOalV4WlRFMk4yWTJZalkzTW1Fd0lqc0tDU0p2Y21sbmFXNWhiQzEwY21GdWMyRmpkR2x2YmkxcFpDSWdQU0FpTVRBd01EQXdNREE0TnpReU5ESTBNQ0k3Q2draVluWnljeUlnUFNBaU1TNHdMakFpT3dvSkluUnlZVzV6WVdOMGFXOXVMV2xrSWlBOUlDSXhNREF3TURBd01EZzNOREkwTWpRd0lqc0tDU0p4ZFdGdWRHbDBlU0lnUFNBaU1TSTdDZ2tpYjNKcFoybHVZV3d0Y0hWeVkyaGhjMlV0WkdGMFpTMXRjeUlnUFNBaU1UTTNPVE01TURVMk56STNNU0k3Q2draWRXNXBjWFZsTFhabGJtUnZjaTFwWkdWdWRHbG1hV1Z5SWlBOUlDSTRSRFJDT0VWQk1TMDFOVGRDTFRSRFJqY3RRVVEwTkMxRE9EVXlOa0U1T0RSRlF6RWlPd29KSW5CeWIyUjFZM1F0YVdRaUlEMGdJbU52YlM1cWIzbHZkR2x0WlM1bWN5NTBhV1Z5TlNJN0Nna2lhWFJsYlMxcFpDSWdQU0FpTnpBeU56WXpOVFF4SWpzS0NTSmlhV1FpSUQwZ0ltTnZiUzVxYjNsdmRHbHRaUzVtYVhOb2MyRjVhVzVuSWpzS0NTSndkWEpqYUdGelpTMWtZWFJsTFcxeklpQTlJQ0l4TXpjNU16a3dOVFkzTWpjeElqc0tDU0p3ZFhKamFHRnpaUzFrWVhSbElpQTlJQ0l5TURFekxUQTVMVEUzSURBME9qQXlPalEzSUVWMFl5OUhUVlFpT3dvSkluQjFjbU5vWVhObExXUmhkR1V0Y0hOMElpQTlJQ0l5TURFekxUQTVMVEUySURJeE9qQXlPalEzSUVGdFpYSnBZMkV2VEc5elgwRnVaMlZzWlhNaU93b0pJbTl5YVdkcGJtRnNMWEIxY21Ob1lYTmxMV1JoZEdVaUlEMGdJakl3TVRNdE1Ea3RNVGNnTURRNk1ESTZORGNnUlhSakwwZE5WQ0k3Q24wPSI7CgkiZW52aXJvbm1lbnQiID0gIlNhbmRib3giOwoJInBvZCIgPSAiMTAwIjsKCSJzaWduaW5nLXN0YXR1cyIgPSAiMCI7Cn0=';
    	$info = array(
			"quantity" => "1",
			"product_id" => "com.fishsaying.product.20",
			"transaction_id" => "1000000087424240",
			"purchase_date" => "2013-09-17 04:02:47 Etc\/GMT",
			"item_id" => "702763541",
			"bid" => "com.joyotime.fishsaying",
			"bvrs" => "1.0.0"
    	);
    	$result = $this->model->ios($userId, $seconds, $receipt, $info)[$this->model->name];
    	debug($this->model->validationErrors);
    	$this->assertEqual($result['user_id'], $userId);
    	$this->assertEqual($result['type'], Receipt::TYPE_IOS);
    	$this->assertEqual($result['amount']['time'], $seconds);
    	$this->assertEqual($result['receipt']['data'], $info);
    	$this->assertEqual($result['receipt']['raw'], $receipt);
    	$this->assertEqual($result['receipt']['identify'], md5($receipt));
    	
    	// It shouldn't save existing receipt...
    	$this->assertEqual($this->model->ios($userId, $seconds, $receipt, $info), false);
    }
    
    public function testPaid() {
        $this->assertEqual($this->model->paid(false), false);
        $this->assertEqual($this->model->paid(null), false);
        $this->assertEqual($this->model->paid($this->id), true);
        $row = $this->model->findById($this->id)[$this->model->name];
        $this->assertEqual($row['status'], Receipt::STATUS_PAID);
        $this->assertEqual($this->model->paid($this->id), false);
    }
}