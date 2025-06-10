<?php
/*
Name:APP接口拦截器
*/
$app_res = Db::table('app')->where('id', $appid)->find();
if (!$app_res) out(101); //应用不存在
if ($app_res['state'] == 'n') out(102, $app_res['notice'], $app_res); //应用关闭
$bmd = ['ini', 'pay']; //白名单接口
if ($app_res['mi_state'] == 'y' && !in_array($act, $bmd)) { //数据已加密
	$data_arr = $_REQUEST; //将post或GET数据移交给data_arr
	if ($app_res['mi_sign'] == 'y') { //数据签名
		if ($sign == '') out(104, $app_res); //签名为空
		$s = Arr_sign($data_arr, $app_res['appkey']); //生成签名
		if ($s != strtolower($sign)) out(106, $app_res); //签名有误
	}
	if ($app_res['mi_time'] > 0) {
		if (!isset($data_arr['t'])) out(108, $app_res); //没有时间变量
		$sign_t = time() - intval($data_arr['t']); //服务器时间-客户端时间，对比时间差
		if ($sign_t > $app_res['mi_time']) out(105, $app_res); //客户端时间小于服务器时间
	}
} else {
	$data_arr = $_REQUEST; //将post或GET数据移交给data_arr
}
?>