<?php
class AppControllerTestCase extends ControllerTestCase {
    
/**
 * Helper method, get mock object by controller name.
 * 
 * @param string $controller_name
 * @return Controller
 */
    protected function getMockController($controller_name) {
    	$contrller = $this->generate($controller_name);
    	$contrller->constructClasses();
    	$contrller->Components->init($contrller);
    	$contrller->Session->write('Auth.User', array(
    		'id' => 1,
    	));
    	return $contrller;
    }
}