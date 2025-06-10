<?php
/*
Name:卡密登录
*/
if (!isset($app_res) or !is_array($app_res)) out(100); //如果需要调用应用配置请先判断是否加载app配置
$kami = isset($data_arr['kami']) && !empty($data_arr['kami']) ? purge($data_arr['kami']) : out(148, $app_res); //卡密为空
$res_kami = Db::table('kami')->where('appid', $appid)->where('kami', $kami)->find(); //false
if (!$res_kami) out(149, $app_res); //卡密不存在
out(200, $res_kami, $app_res);
?>
