<?php
namespace phpspider\Spider\Request;

class SpiderRequest{
	
	protected $http = null;
	protected $callback = null;
	protected $args = null;

	/**
	 * SpiderRequest constructor.
	 * @param $target 指定要爬取的页面的url
	 * @param null $referrer 请求$target时，携带的referrer
	 * @param string $method 请求方式 目前只支持get,post
	 * @param null $callback 接收响应的回调方法 回调为对象的方法时，请传入数组[对象,方法名]
	 * @param null $callbackParams 传递给上面回调的其它参数
	 */
	public function __construct($target,$referrer=null,$method = 'get',$callback=null,$callbackParams=null){
		
		if(empty($target)){
			throw \Exception('目标请求地址不能为空');
		}
		
		$http = array_filter(array(
				'target'=>$target,
				'referrer'=>$referrer,
				'method'=>strtoupper($method),
				'use_cookie'=>true,
				'user_agent'=>'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.132 Safari/537.36',
		));
		
		$this->http = $http;
		
		if(empty($callback) === false){
			$this->callback = $callback;
		}
		
		if(empty($callbackParams)){
			$this->args = $callbackParams;
		}
	}
	
	public function getHttp(){
		
		return $this->http;
	}
	
	public function setHttp($key,$value){
		$this->http[$key] = $value;
		return $this;
	}
	
	public function setCallback($callback){
		$this->callback = $callback;
		return $this;
	}
	
	public function getCallback(){
		return $this->callback;
	}
	
	public function setCallbackParams($callbackParams){
		$this->args = $callbackParams;
		return $this;
	}
	
	public function getCallbackParams(){
		return $this->args;
	}
	
	public function getValues(){
		$array = array(
				'http'=>$this->http,
				'callback'=>$this->callback,
				'args'=>$this->args,
		);
		
		return array_filter($array);
	}
}