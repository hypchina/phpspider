<?php

namespace Tests;

use phpspider\Spider\BaseSpider;
use QL\QueryList;
use phpspider\Spider\Request\SpiderRequest;

class KuaileSpider extends BaseSpider {
	
	public function __construct(){
		parent::__construct();
		//可以初始设置多个请求
		$this->start_url = array(
				new SpiderRequest('https://caipiao.ip138.com/kuaile10fen/xingyunnongchang/','https://caipiao.ip138.com/kuaile10fen/',"GET"),
		);
	}
	
	/**
	 * 返回SpiderRequestBean对象 将会在后续中继续爬取
	 * @param String $response
	 * @return \phpspider\Spideritem\SpiderItem | \phpspider\Spider\Request\SpiderRequest | mixed
	 */
	public function parse($response){
		$rules = [
			//发布日期
			'date' => ['td:eq(0)>span','text'],
			//期号
			'issue' => ['td:eq(1)>span','text'],
			//中奖号码
			'number' => ['.award','html'],
		];
		
		$data = QueryList::Query($response, $rules,'.panel>table>tbody tr')->getData(function($item){
			
			$item['number'] = QueryList::Query($item['number'],[
					'number' => ['span','data-value'],
					])->data;
			
			$item['number'] = array_column($item['number'],'number');
			
			return $item;
		});
		
		//测试一波，注意返回SpiderRequest类型的时候，不要陷入无限循环，最后加上特定判断再返回，确保代码能够结束
		//假设 这里爬取从当前$response中获取的url，再次进行深度爬取 这个请求的最终处理方法是$this->test()
		//yield (new SpiderRequest('https://caipiao.ip138.com/shishicai/chongqing/','https://caipiao.ip138.com/kuaile10fen/',"GET",array($this,"test")));
		
		foreach($data as $key=>$item){
			$item['number'] = array_map(function($item){
				return str_pad($item,2,0,STR_PAD_LEFT);
			}, $item['number']);
			$item['number'] = implode('', $item['number']);
			$spiderItem = new KuaileSpiderItem();
			$spiderItem->setValue('issue', $item['issue']);
			$spiderItem->setValue('luck', $item['number']);
			$spiderItem->setValue('date', $item['date']);
			$spiderItem->setValue('state', 'NORMAL');
			yield $spiderItem;
		}
	}
}
?>