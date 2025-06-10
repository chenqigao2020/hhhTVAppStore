<?php
/*
Name:APP管理API
Version:1.0
Author:易如意
Author QQ:51154393
Author Url:www.eruyi.cn
*/

// 此版本由《红火火工作室》开发 二开、源码请联系QQ：1282797911 闲鱼：红火火工作室

if (!isset($islogin)) header("Location: /"); //非法访问
if ($act == 'add') { // 添加
	$name = $_POST['name'];
	$res = Db::table('application')->where('name', $name)->find();
	if ($res) {
		json(201, '名称重复');
	}
	$res = Db::table('application')->where('only', $_POST['only'])->find();
	if ($res) {
		json(201, '包名重复');
	}
	$classify = $_POST['classify_id'];
	if ($classify == '') {
		json(201, '请先添加应用分类');
	}
	$add = [
		'name' => $_POST['name'],
		'only' => $_POST['only'],
		'icon' => $_POST['icon'],
		'download' => $_POST['download'],
		'version' => $_POST['version'],
		'size' => $_POST['size'],
		'details' => $_POST['details'],
		'classify_id' => $classify,
		'id'=> 0,
	];
	$res = Db::table('application')->add($add);
    if ($res) {
		json(200, '添加成功');
	} else {
		json(201, "添加失败");
	}
}

if ($act == 'del') { // 删除
	$id = $_POST['id'];
	if ($id) {
		$ids = '';
		foreach ($id as $value) {
			$ids .= intval($value) . ",";
		}
		$ids = rtrim($ids, ",");
		$res = Db::table('application')->where('id', 'in', '(' . $ids . ')')->del(); //false
		if ($res) {
			json(200, '删除成功');
		} else {
			json(201, '删除失败');
		}
	} else {
		json(201, '没有需要删除的数据');
	}
}

if ($act == 'edit') { // 编辑
	$id = $_POST['id'];
	$update['name'] = $_POST['name'];
	$update['only'] = $_POST['only'];
	$update['icon'] = $_POST['icon'];
	$update['download'] = $_POST['download'];
	$update['version'] = $_POST['version'];
	$update['size'] = $_POST['size'];
	$update['details'] = $_POST['details'];
	$update['classify_id'] = intval($_POST['classify_id']);
	$res = Db::table('application')->where('name', $_POST['name'])->find();
	if ($res && $res['id'] != $id) {
		json(201, '名称重复');
	}
	$res = Db::table('application')->where('only', $update['only'])->find();
	if ($res && $res['id'] != $id) {
		json(201, '包名重复');
	}
	$res = Db::table('application')->where('id', $id)->update($update);
	if ($res) {
		json(200, '编辑成功');
	} else {
		json(201, '编辑失败');
	}
}
?>