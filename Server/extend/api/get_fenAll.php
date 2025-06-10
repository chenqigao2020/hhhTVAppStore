<?php
/*
Name:获取全部积分事件
*/
if (!isset($app_res) or !is_array($app_res)) out(100); //如果需要调用应用配置请先判断是否加载app配置
$ret = [];
$fen_res = Db::table('fen')->where(['appid' => $appid, 'state' => 'y'])->select(); //获取积分事件
if (is_array($fen_res)) {
	foreach ($fen_res as $k => $v) {
		$rows = $fen_res[$k];
		if ($rows['name'] != "签到") {
			$ret[] = [
				'id' =>  $rows['id'],
				'appid' =>  $rows['appid'],
				'name' =>  $rows['name'],
				'fen_num' =>  $rows['fen_num'],
				'vip_num' =>  $rows['vip_num'],
				'state' =>  $rows['state']
			];
		}
	}
	out(200, $ret, $app_res);
}
out(201, '商品读取失败', $app_res);
?>