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

/**
 * The class is used to check and verify receipt data which is transaction data
 * between client and app store.
 *
 * @package app.Controller.Component
 */
class IosReceiptComponent extends Component {
    
/**
 * Verify a receipt and return receipt data
 *
 * @param   string  $receipt    Base-64 encoded data
 * @param   bool    $isSandbox  Optional. True if verifying a test receipt
 * @throws  Exception   If the receipt is invalid or cannot be verified
 * @return  array       Receipt info (including product ID and quantity)
 */
    public function getReceiptData($receipt, $isSandbox = false) {
    	// determine which endpoint to use for verifying the receipt
    	if ($isSandbox) {
    		$endpoint = 'https://sandbox.itunes.apple.com/verifyReceipt';
    	} else {
    		$endpoint = 'https://buy.itunes.apple.com/verifyReceipt';
    	}
    	
    	$this->log('The request address is...', Configure::read('Log.Ios'));
    	$this->log($endpoint, Configure::read('Log.Ios'));
    	$this->log('Starting to request...', Configure::read('Log.Ios'));
    
    	// build the post data
    	$postData = json_encode(
    		array('receipt-data' => $receipt)
    	);
    
    	// create the cURL request
    	$ch = curl_init($endpoint);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    	curl_setopt($ch, CURLOPT_POST, true);
    	curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    
    	// execute the cURL request and fetch response data
    	$response = curl_exec($ch);
    	$errno    = curl_errno($ch);
    	$errmsg   = curl_error($ch);
    	curl_close($ch);
    
    	// ensure the request succeeded
    	if ($errno != 0) {
    	    $this->log('Error status: '.$errno.' from Apple', Configure::read('Log.Ios'));
    		throw new Exception($errmsg, $errno);
    	}
    	
    	// Save source http response from Apple server...
    	$this->log($response, Configure::read('Log.Ios'));
    
    	// parse the response data
    	$data = json_decode($response);
    
    	// ensure response data was a valid JSON string
    	if(!is_object($data)) {
    	    $this->log('Can not resolve json data, invalid response data', Configure::read('Log.Ios'));
    		throw new Exception('Invalid response data');
    	}
    
    	// ensure the expected data is present
    	if (!isset($data->status) || $data->status != 0) {
    	    $this->log('Invalid status of response data is '.$data->status, Configure::read('Log.Ios'));
    		throw new Exception('Invalid receipt', $data->status);
    	}
    
    	// build the response array with the returned data
    	$result = array(
			'quantity'       =>  $data->receipt->quantity,
			'product_id'     =>  $data->receipt->product_id,
			'transaction_id' =>  $data->receipt->transaction_id,
			'purchase_date'  =>  $data->receipt->purchase_date,
			'item_id'        =>  $data->receipt->item_id,
			'bid'            =>  $data->receipt->bid,
			'bvrs'           =>  $data->receipt->bvrs
    	);
    	return $result;
    }
    
/**
 * Check whether the receipt is valid
 * 
 * @param Checkout $model
 * @param string $receipt The binary json string got from AppStore
 * @return boolean
 */
    public function exist(Checkout $model, $receipt) {
        return $model->find('count', array(
            'conditions' => array(
                'receipt.identify' => md5($receipt)
            )
        )) > 0;
    }
}