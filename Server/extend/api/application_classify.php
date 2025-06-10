<?php
/*
Name:获取APP分类
*/

// 此版本由《红火火工作室》开发 二开、源码请联系QQ：1282797911 闲鱼：红火火工作室

if (!isset($app_res) or !is_array($app_res)) out(100); //如果需要调用应用配置请先判断是否加载app配置

$list = Db::table('classify')->order('id', 'desc')->select();

out(200, $list, $app_res);

?>