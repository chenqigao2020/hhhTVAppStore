<?php
/*
Name:获取启动配置
*/
if (!isset($app_res) or !is_array($app_res)) out(100); //如果需要调用应用配置请先判断是否加载app配置
$ret = [];
$app_exten_res = Db::table('app_exten')->where(['appid' => $appid, 'state' => 'y'])->order('id desc')->select(); //获取扩展配置
if (is_array($app_exten_res)) {
	foreach ($app_exten_res as $k => $v) {
		$rows = $app_exten_res[$k];
		if ($rows['data'] != null && $rows['data'] != "") {
			if (strpos($rows['data'], ",")) {
				$data = explode(",", $rows['data']);
				$flags["flag"] = $data;
			} else {
				$flags["flag"] = $rows['data'];
			}
		}
		if ($rows['header'] != null && $rows['header'] != "") {
			$flags["header"] = [
				"User-Agent" => $rows['header']
			];
		}
		$ret[] = [
			'name' => $rows['name'],
			'type' => $rows['type'],
			'url' => $rows['api'],
			'ext' => json_encode($flags, 320),
		];
	}
	out(200, $ret, $app_res);
}
out(201, '自定义接口失败', $app_res);
?>