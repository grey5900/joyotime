<?php
namespace Model\Data;

use \Utility\Arrays\Access;

class Upload {
    
    public $name = '';
    
    public $type = '';
    
    public $tmpName = '';
    
    public $error = 0;
    
    public $size = 0;
    
    /**
     * The destination path
     * 
     * @var string
     */
    public $dest = '';
    
    public function __construct($data) {
    	$this->name = Access::gets($data, 'name');
    	$this->type = Access::gets($data, 'type');
    	$this->tmpName = Access::gets($data, 'tmpName');
    	$this->error = Access::gets($data, 'error', 0);
    	$this->size = Access::gets($data, 'size', 0);
    }
    
    /**
     * Check whether uploading file has or not.
     * 
     * @return boolean
     */
    public function has() {
    	return !empty($this->name) 
    			&& !empty($this->type)
    			&& !empty($this->tmpName)
    			&& $this->size > 0
    			&& $this->error == 0;
    }
}