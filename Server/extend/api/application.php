<?php
/*
Name:获取APP列表
*/

// 此版本由《红火火工作室》开发 二开、源码请联系QQ：1282797911 闲鱼：红火火工作室

if (!isset($app_res) or !is_array($app_res)) out(100); //如果需要调用应用配置请先判断是否加载app配置

$db = Db::table('application');

if ($data_arr['classify_id'] != null) {
    $db->where('classify_id', $data_arr['classify_id']);
}

$list = $db->order('id', 'desc')->select();

out(200, $list, $app_res);

?>