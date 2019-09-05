<?php
namespace phpspider\Spideritem;

abstract class SpiderItem{
	
	protected $values = array();
	
	protected $error;
	
	public function setValues(array $valuse){
		$this->values = $valuse;
		return $this;
	}
	
	public function setValue($key,$value){
		if(empty($key) || empty($value))
		{
			return $this;
		}
		
		$this->values[$key] = $value;
		
		return $this;
	}
	
	public function getError(){
		return $this->error;
	}
	
	abstract function proceed($spider);
}