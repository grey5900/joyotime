<?php
namespace Model\Data;

class Upload {
	
	public $_name = '\Model\Data\Upload';
    
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
    	$this->name = \Hash::get($data, 'name');
    	$this->type = \Hash::get($data, 'type');
    	$this->tmpName = \Hash::get($data, 'tmp_name');
    	$this->error = \Hash::get($data, 'error');
    	$this->size = \Hash::get($data, 'size');
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