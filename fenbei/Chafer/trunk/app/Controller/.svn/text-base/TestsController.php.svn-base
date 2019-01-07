<?php
class TestsController extends AppController {
    
    public $name = "Tests";
    
    public $autoRender = false;
    public $autoLayout = false;
    
    public function logs($action = '') {
    	$path = ROOT . '/app/tmp/logs/' . $action . '.log';
    	$size = filesize($path);
    
    	$MAX = 204800; // byte
    	if($size < $MAX)
    		$offset = 0;
    	else
    		$offset = $size - $MAX;
    	echo "<pre>";
    	echo nl2br(@file_get_contents($path, false, null, $offset));
    	echo "</pre>";
    }
}