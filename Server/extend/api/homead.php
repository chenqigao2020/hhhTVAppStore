<?php
/*
Name:获取首页广告配置
*/
if (!isset($app_res) or !is_array($app_res)) out(100); //如果需要调用应用配置请先判断是否加载app配置
$app_homead = [];
$app_homead_res = Db::table('app_homead')->where('appid', $appid)->order('id desc')->select(); //获取首页广告配置
if (is_array($app_homead_res)) {
	foreach ($app_homead_res as $k => $v) {
		$rows = $app_homead_res[$k];
		$app_homead[] = [
			'name' => $rows['name'],
			'extend' => $rows['data'],
			'searchable' => $rows['searchable']
		];
	}
	out(200, $app_homead, $app_res);
}
out(201, '自定义接口获取失败', $app_res);
?>