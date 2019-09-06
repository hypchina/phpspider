<?php

namespace phpspider\Spider;

use QL\QueryList;
use phpspider\Spider\Request\SpiderRequest;
use phpspider\Spideritem\SpiderItem;

abstract class BaseSpider {
	
	protected $delay = 1;
	
	protected $start_url = array();
	/**
	 * 保存请求的队列
	 * @var Array[SpiderRequest]
	 */
	protected $queue = array();
	/**
	 * 保存SpiderItem的数组
	 * @var Array[SpiderItem]
	 */
	protected $items = array();
	/**
	 * 是否保留异常item
	 * @var unknown
	 */
	protected $keepErrorItem = true;
	
	public function __construct(){

	}
	
	public function start(){
		foreach ($this->start_url as $url){
			if (is_string($url)){
				$request = new SpiderRequest($url);
			}else if($url instanceof SpiderRequest){
				$request = $url;
			}
			
			//默认调用Spider的parse方法
			if(empty($request->getCallback())){
				$request->setCallback([$this,"parse"]);
			}
			
			yield $request;
		}
	}
	
	protected function pushQueue(SpiderRequest $request){
		array_push($this->queue, $request);
	}
	
	protected function dispatch(){
		while (!empty($this->queue)){
			yield array_shift($this->queue);
		}
	}
	
	public function run(){
		$gen = $this->start();
		
		if($gen instanceof \Generator){
			foreach ($gen as $item){
				if($item instanceof SpiderRequest)
				{
					$this->pushQueue($item);
				}
				//从队列里面取出请求，进行发送
				foreach($this->dispatch() as $queueItem){
					if($this->delay > 0){
						sleep($this->delay);
					}
					
					$result = QueryList::run("Request",$queueItem->getValues());
					
					$this->handleResult($result->html);
				}
			}
		}
		
		$this->close($this->items);
	}
	
	protected function handleResult($result){
		if(! ($result instanceof \Generator)){
			$result = array($result);
		}
		
		foreach($result as $rs){
			if($rs instanceof SpiderRequest){
				$this->pushQueue($rs);
			}
			else if($rs instanceof SpiderItem){
				try{
					if($rs->proceed($this)){
						array_push($this->items, $rs);
						unset($rs);
					}
				}catch (\Exception $e){

				}finally{
					if($this->keepErrorItem && isset($rs)){
						array_push($this->items, $rs);
					}
				}
			}
		}
	}
	
	/**
	 * 结束,可以在这里生成最终的json文件 或者 Excel
	 * @param array $items 一个SpiderItem的数组
	 */
	public function close(array $items){
		
	}
}

?>