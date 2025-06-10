<?php
/*
Name:获取商品
*/
if (!isset($app_res) or !is_array($app_res)) out(100); //如果需要调用应用配置请先判断是否加载app配置
if ($app_res['logon_way'] == 1) out(164, $app_res); //不是账号登录方式不允许使用当前操作
$ret = [];
$site_res = Db::table('goods')->where(['appid' => $appid, 'state' => 'y'])->select(); //获取商品列表
if (is_array($site_res)) {
	foreach ($site_res as $k => $v) {
		$rows = $site_res[$k];
		$ret[] = [
			'gid' => $rows['id'],
			'gname' => $rows['name'],
			'gmoney' => $rows['money'],
			'gtype' => $rows['type'],
			'obtain' => $rows['amount'],
			'cv' => $rows['jie']
		];
	}
	out(200, $ret, $app_res);
}
out(201, '商品读取失败', $app_res);
?>