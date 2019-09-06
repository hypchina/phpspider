# phpspider简介
***
`phpspider`是一个基于`QueryList3`的数据爬虫，采集任何复杂的页面 ，可以简单的深度爬取无限级页面。
[QueryList3文档](http://v3.querylist.cc)
[QueryList最新版](https://github.com/jae-jae/QueryList)
# phpspider 安装
通过`composer`安装:
```
composer require hypchina/phpspider
```
# phpspider 使用
定义爬虫类KuaileSpider.php
```php
//自定义KuaileSpider类 需要继承BaseSpider
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
    //使用QueryList进行数据筛选
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
		
		//测试一波，注意返回SpiderRequest类型的时候，不要陷入无限循环，最好加上特定判断再返回，确保代码能够结束
		//假设 这里从当前$response中获取的某个url，再次进行深度爬取 这个请求的响应会传递到最终处理方法$this->test()
		//yield (new SpiderRequest('https://caipiao.ip138.com/shishicai/chongqing/','https://caipiao.ip138.com/kuaile10fen/',"GET",array($this,"test")));
		
		foreach($data as $key=>$item){
			$item['number'] = array_map(function($item){
				return str_pad($item,2,0,STR_PAD_LEFT);
			}, $item['number']);
			$item['number'] = implode('', $item['number']);
      //自定义数据处理类
			$spiderItem = new KuaileSpiderItem();
			$spiderItem->setValue('issue', $item['issue']);
			$spiderItem->setValue('luck', $item['number']);
			$spiderItem->setValue('date', $item['date']);
			$spiderItem->setValue('state', 'NORMAL');
      //这里可以直接返回的数据类
			yield $spiderItem;
		}
	}
}
```
定义数据处理类KuaileSpiderItem.php
```php
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
		var_dump($data);
		return $data;
	}
}
```
运行结果run.php
```php
include "vendor/autoload.php";
//--------运行对应的spider--------
(new \Tests\KuaileSpider())->run();
```

KuaileSpiderItem中的打印结果:
```
array(4) {
  ["issue"]=>
  string(10) "2019090615"
  ["luck"]=>
  string(16) "1713180410090807"
  ["date"]=>
  string(16) "2019-09-06 09:01"
  ["state"]=>
  string(6) "NORMAL"
}
array(4) {
  ["issue"]=>
  string(10) "2019090614"
  ["luck"]=>
  string(16) "1710191312030920"
  ["date"]=>
  string(16) "2019-09-06 08:41"
  ["state"]=>
  string(6) "NORMAL"
}
array(4) {
  ["issue"]=>
  string(10) "2019090613"
  ["luck"]=>
  string(16) "0419050113161220"
  ["date"]=>
  string(16) "2019-09-06 08:21"
  ["state"]=>
  string(6) "NORMAL"
}
//省略....
```
