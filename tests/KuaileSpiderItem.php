<?php
namespace phpspider\tests;

use phpspider\Spideritem\SpiderItem;

class KuaileSpiderItem extends SpiderItem
{
	/**
	 * 接收组合完成的数据，在这里可以进行数据入库
	 * @param \phpspider\Spider\BaseSpider $spider
	 * @return boolean 返回假 就放弃该条数据
	 */
	public function proceed($spider){
		$data = $this->values;
		//todo 进行数据入库...

		return $data;
	}
} 