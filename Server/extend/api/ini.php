<?php
/*
Name:获取配置
*/
if (!isset($app_res) or !is_array($app_res)) out(100); //如果需要调用应用配置请先判断是否加载app配置
if ($app_res['app_json'] == null || $app_res['app_json'] == "") {
	$app_res['app_json'] = "http://" . $_SERVER['SERVER_NAME'] . "/app/api.json";
}
$ini_data = [ //基本配置
	'app_bb' => $app_res['app_bb'],
	'app_nshow' => $app_res['app_nshow'],
	'app_nurl' => $app_res['app_nurl'],
	'mode' => $app_res['mode'],
	'ui_startad' => $app_res['ui_startad'],
	'kami_url' => $app_res['kami_url'],
	'app_json' => $app_res['app_json'],
	'app_jsonb' => $app_res['app_jsonb'],
	'logon_way' => $app_res['logon_way'],
	'ui_group' => $app_res['ui_group'],
	'ui_removersc' => $app_res['ui_removersc'],
	'ui_remove_parses' => $app_res['ui_remove_parses'],
	'ui_remove_class' => $app_res['ui_remove_class'],
	'ui_parse_name' => $app_res['ui_parse_name'],
	'app_about' => $app_res['app_about'],
	'app_share' => getServiceAddress() . '/app.apk',
	'app_watch_for_minutes' => $app_res['app_watch_for_minutes'],
	'ui_use_source' => $app_res['ui_use_source']
];
if (isset($_GET['pay'])) {
	$pay_ini = [
		'state' => $app_res['pay_state'],
		'url' => $app_res['pay_url'],
		'appid' => $app_res['pay_id'],
		'appkey' => $app_res['pay_key'],
		'ali' => $app_res['pay_ali_state'],
		'wx' => $app_res['pay_wx_state'],
		'qq' => $app_res['pay_qq_state']
	];
}
if (isset($pay_ini) && is_array($pay_ini)) {
	$ini_data = array_merge($ini_data, ['pay' => $pay_ini]);
}
out(200, $ini_data, $app_res);

function getServiceAddress() {
	$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? "https" : "http";
	$host = $_SERVER['HTTP_HOST'];
	$port = $_SERVER['SERVER_PORT'];
	$url = $protocol . '://' . $host;
	return $url;
}
?>