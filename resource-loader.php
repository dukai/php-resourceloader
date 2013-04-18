<?php 
class ResourceLoader{
	
	private $register = array();
	private $absPath = '';
	public function __construct(){}
	
	public function add($key, $path){
		$this->register[$key] = $path;
	}
	
	public function getSrc($key){
		$path = $this->absPath . $this->register[$key];
		
		if(function_exists('realpath')){
			$path = realpath($path);
		}
		
		if(!$path || !@is_file($path)){
			return '';
		}

		return @file_get_contents($path);
	}
	
	public function setAbsPath($path){
		$this->absPath = $path;
	}
	
}