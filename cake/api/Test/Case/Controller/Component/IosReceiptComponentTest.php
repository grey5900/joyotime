<?php
APP::uses('ReceiptComponent', 'Controller/Component');
APP::uses('Controller', 'Controller');
APP::uses('AppComponentTestCase', 'Test/Case/Controller/Component');

class IosReceiptComponentTest extends AppComponentTestCase {
    
/**
 * @var IosReceiptComponent
 */
    private $receipt;
/**
 * @var string JSON string come from appstore
 */
    private $raw;
    
/**
 * (non-PHPdoc)
 * @see CakeTestCase::setUp()
 */
    public function setUp() {
    	parent::setUp();
    	 
    	$this->receipt = $this->getComponent();
    	$this->raw = 'ewoJInNpZ25hdHVyZSIgPSAiQWdTOFcxWGw5SDdsQTdXVjFpRlBYbFVKVXM4cTlnZTE5by9kTEdlTHpHQlhFNmZjQnFKamF5bU53bUhtYjlCNlIyYVRzbitKcWQ3eVJTbUdkZTFremxNSUZEZGxkNEVQMStuRVNUL0YzcG85VEtQOEc4S1NGSWx4ZFJsV3BmNnhBRDJvdlR0R2dXcFB4bkxJQlVjS3NBTEkvUkpLbGQ5WG1ZL3diTkduS2NtTEFBQURWekNDQTFNd2dnSTdvQU1DQVFJQ0NHVVVrVTNaV0FTMU1BMEdDU3FHU0liM0RRRUJCUVVBTUg4eEN6QUpCZ05WQkFZVEFsVlRNUk13RVFZRFZRUUtEQXBCY0hCc1pTQkpibU11TVNZd0pBWURWUVFMREIxQmNIQnNaU0JEWlhKMGFXWnBZMkYwYVc5dUlFRjFkR2h2Y21sMGVURXpNREVHQTFVRUF3d3FRWEJ3YkdVZ2FWUjFibVZ6SUZOMGIzSmxJRU5sY25ScFptbGpZWFJwYjI0Z1FYVjBhRzl5YVhSNU1CNFhEVEE1TURZeE5USXlNRFUxTmxvWERURTBNRFl4TkRJeU1EVTFObG93WkRFak1DRUdBMVVFQXd3YVVIVnlZMmhoYzJWU1pXTmxhWEIwUTJWeWRHbG1hV05oZEdVeEd6QVpCZ05WQkFzTUVrRndjR3hsSUdsVWRXNWxjeUJUZEc5eVpURVRNQkVHQTFVRUNnd0tRWEJ3YkdVZ1NXNWpMakVMTUFrR0ExVUVCaE1DVlZNd2daOHdEUVlKS29aSWh2Y05BUUVCQlFBRGdZMEFNSUdKQW9HQkFNclJqRjJjdDRJclNkaVRDaGFJMGc4cHd2L2NtSHM4cC9Sd1YvcnQvOTFYS1ZoTmw0WElCaW1LalFRTmZnSHNEczZ5anUrK0RyS0pFN3VLc3BoTWRkS1lmRkU1ckdYc0FkQkVqQndSSXhleFRldngzSExFRkdBdDFtb0t4NTA5ZGh4dGlJZERnSnYyWWFWczQ5QjB1SnZOZHk2U01xTk5MSHNETHpEUzlvWkhBZ01CQUFHamNqQndNQXdHQTFVZEV3RUIvd1FDTUFBd0h3WURWUjBqQkJnd0ZvQVVOaDNvNHAyQzBnRVl0VEpyRHRkREM1RllRem93RGdZRFZSMFBBUUgvQkFRREFnZUFNQjBHQTFVZERnUVdCQlNwZzRQeUdVakZQaEpYQ0JUTXphTittVjhrOVRBUUJnb3Foa2lHOTJOa0JnVUJCQUlGQURBTkJna3Foa2lHOXcwQkFRVUZBQU9DQVFFQUVhU2JQanRtTjRDL0lCM1FFcEszMlJ4YWNDRFhkVlhBZVZSZVM1RmFaeGMrdDg4cFFQOTNCaUF4dmRXLzNlVFNNR1k1RmJlQVlMM2V0cVA1Z204d3JGb2pYMGlreVZSU3RRKy9BUTBLRWp0cUIwN2tMczlRVWU4Y3pSOFVHZmRNMUV1bVYvVWd2RGQ0TndOWXhMUU1nNFdUUWZna1FRVnk4R1had1ZIZ2JFL1VDNlk3MDUzcEdYQms1MU5QTTN3b3hoZDNnU1JMdlhqK2xvSHNTdGNURXFlOXBCRHBtRzUrc2s0dHcrR0szR01lRU41LytlMVFUOW5wL0tsMW5qK2FCdzdDMHhzeTBiRm5hQWQxY1NTNnhkb3J5L0NVdk02Z3RLc21uT09kcVRlc2JwMGJzOHNuNldxczBDOWRnY3hSSHVPTVoydG04bnBMVW03YXJnT1N6UT09IjsKCSJwdXJjaGFzZS1pbmZvIiA9ICJld29KSW05eWFXZHBibUZzTFhCMWNtTm9ZWE5sTFdSaGRHVXRjSE4wSWlBOUlDSXlNREV6TFRBNUxURTJJREl4T2pBeU9qUTNJRUZ0WlhKcFkyRXZURzl6WDBGdVoyVnNaWE1pT3dvSkluVnVhWEYxWlMxcFpHVnVkR2xtYVdWeUlpQTlJQ0l4T1dJNFkyVm1PVFptTnpGaU4yWTJPREE1TVRZME5HWmpOalV4WlRFMk4yWTJZalkzTW1Fd0lqc0tDU0p2Y21sbmFXNWhiQzEwY21GdWMyRmpkR2x2YmkxcFpDSWdQU0FpTVRBd01EQXdNREE0TnpReU5ESTBNQ0k3Q2draVluWnljeUlnUFNBaU1TNHdMakFpT3dvSkluUnlZVzV6WVdOMGFXOXVMV2xrSWlBOUlDSXhNREF3TURBd01EZzNOREkwTWpRd0lqc0tDU0p4ZFdGdWRHbDBlU0lnUFNBaU1TSTdDZ2tpYjNKcFoybHVZV3d0Y0hWeVkyaGhjMlV0WkdGMFpTMXRjeUlnUFNBaU1UTTNPVE01TURVMk56STNNU0k3Q2draWRXNXBjWFZsTFhabGJtUnZjaTFwWkdWdWRHbG1hV1Z5SWlBOUlDSTRSRFJDT0VWQk1TMDFOVGRDTFRSRFJqY3RRVVEwTkMxRE9EVXlOa0U1T0RSRlF6RWlPd29KSW5CeWIyUjFZM1F0YVdRaUlEMGdJbU52YlM1cWIzbHZkR2x0WlM1bWN5NTBhV1Z5TlNJN0Nna2lhWFJsYlMxcFpDSWdQU0FpTnpBeU56WXpOVFF4SWpzS0NTSmlhV1FpSUQwZ0ltTnZiUzVxYjNsdmRHbHRaUzVtYVhOb2MyRjVhVzVuSWpzS0NTSndkWEpqYUdGelpTMWtZWFJsTFcxeklpQTlJQ0l4TXpjNU16a3dOVFkzTWpjeElqc0tDU0p3ZFhKamFHRnpaUzFrWVhSbElpQTlJQ0l5TURFekxUQTVMVEUzSURBME9qQXlPalEzSUVWMFl5OUhUVlFpT3dvSkluQjFjbU5vWVhObExXUmhkR1V0Y0hOMElpQTlJQ0l5TURFekxUQTVMVEUySURJeE9qQXlPalEzSUVGdFpYSnBZMkV2VEc5elgwRnVaMlZzWlhNaU93b0pJbTl5YVdkcGJtRnNMWEIxY21Ob1lYTmxMV1JoZEdVaUlEMGdJakl3TVRNdE1Ea3RNVGNnTURRNk1ESTZORGNnUlhSakwwZE5WQ0k3Q24wPSI7CgkiZW52aXJvbm1lbnQiID0gIlNhbmRib3giOwoJInBvZCIgPSAiMTAwIjsKCSJzaWduaW5nLXN0YXR1cyIgPSAiMCI7Cn0=';
    }
    
/**
 * (non-PHPdoc)
 * @see AppComponentTestCase::getComponentName()
 */
    public function getComponentName() {
    	return 'IosReceiptComponent';
    }
    
    public function testGetReceiptData() {
        $result = $this->receipt->getReceiptData($this->raw, true);
        $this->assertEqual($result, array(
        	'quantity' => '1',
        	'product_id' => 'com.joyotime.fs.tier5',
        	'transaction_id' => '1000000087424240',
        	'purchase_date' => '2013-09-17 04:02:47 Etc/GMT',
        	'item_id' => '702763541',
        	'bid' => 'com.joyotime.fishsaying',
        	'bvrs' => '1.0.0'
        ));
    }
    
    public function testExist() {
        $this->assertEqual($this->receipt->exist(
                ClassRegistry::init('Checkout'), $this->raw), false);
    }
}