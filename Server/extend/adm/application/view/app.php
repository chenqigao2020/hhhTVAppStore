<?php
/*
Sort:3
Hidden:false
Name:APP管理
Url:application_app
Right:application
Version:1.0
Author:易如意
Author QQ:51154393
Author Url:www.eruyi.cn
*/

// 此版本由《红火火工作室》开发 二开、源码请联系QQ：1282797911 闲鱼：红火火工作室

if (!isset($islogin)) header("Location: /"); //非法访问
$nums = Db::table('app_exten')->count();
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$url = "./?app_exten&page=";
$bnums = ($page - 1) * $ENUMS;
?>

<!-- start page title -->
<div class="row">
	<div class="col-12">
		<div class="page-title-box">
			<div class="page-title-right">
				<ol class="breadcrumb m-0">
					<li class="breadcrumb-item"><a href="javascript: void(0);">首页</a></li>
					<li class="breadcrumb-item active">APP</li>
				</ol>
			</div>
			<h4 class="page-title"><?php echo $title; ?></h4>
		</div> <!-- end page-title-box -->
	</div> <!-- end col-->
</div>
<!-- end page title -->

<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-body">
				<div class="row mb-2">
					<div class="col-lg-8">
						<button type="button" onclick="modal_cut('add',0,0,0,0)" class="btn btn-danger mb-2 mr-2" data-toggle="modal" data-target="#modal"><i class="mdi mdi-briefcase-plus-outline mr-1"></i>添加APP</button>
					</div>
					<div class="col-lg-4">
						<div class="text-lg-right">
							<form action="" method="post">
								<div class="input-group">
									<input type="text" class="form-control" name="so" placeholder="搜索APP" value='<?php echo $so; ?>'>
									<span class="mdi mdi-magnify"></span>
									<div class="input-group-append">
										<button class="btn btn-primary" type="submit">搜索</button>
									</div>
								</div>
							</form>
						</div>
					</div><!-- end col-->
				</div>
				<form action="" method="post" name="form_log" id="form_log">
					<div class="table-responsive">
						<table class="table table-centered table-striped dt-responsive nowrap w-100">
							<thead>
								<tr>
									<th style="width: 20px;">
										<div class="custom-control custom-checkbox">
											<input type="checkbox" class="custom-control-input" id="all" onclick="checkAll();">
											<label class="custom-control-label" for="all">&nbsp;</label>
										</div>
									</th>

									<th style="width: 20px;">
										<center><span class="badge badge-light-lighten">名称</span></center>
									</th>

									<th>
										<center><span class="badge badge-light-lighten">分类</span></center>
									</th>

									<th>
										<center><span class="badge badge-light-lighten">编辑</span></center>
									</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$app = Db::table('application', 'as K')->field('K.*,A.name as classify_name')->JOIN('classify', 'as A', 'K.classify_id=A.id');
								if ($so) {
									$app = $app->where('K.name', 'like', "%{$so}%")->order('id desc');
								} else {
									$app = $app->order('id desc')->limit($bnums, $ENUMS);
								}
								$res = $app->select();
								foreach ($res as $k => $v) {
									$rows = $res[$k];
								?>
								<tr>
									<td>
										<div class="custom-control custom-checkbox">
											<input type="checkbox" class="custom-control-input" name="ids[]" value="<?php echo $rows['id']; ?>" id="<?php echo 'check_' . $rows['id']; ?>">
											<label class="custom-control-label" for="<?php echo 'check_' . $rows['id']; ?>"></label>
										</div>
									</td>

									<td>
										<center><?php echo $rows['name']; ?></center>
									</td>

									<td>
										<center><?php echo $rows['classify_name']; ?></center>
									</td>

									<td>
										<center><a href="javascript:void(0);" onclick="modal_cut('edit',
										<?php echo $rows['id']; ?>,
										'<?php echo $rows['name']; ?>',
										'<?php echo $rows['only']; ?>',
										'<?php echo $rows['icon']; ?>',
										'<?php echo $rows['download']; ?>',
										'<?php echo $rows['version']; ?>',
										'<?php echo $rows['size']; ?>',
										'<?php echo $rows['details']; ?>',
										<?php echo $rows['classify_id']; ?>,
										)" class="action-icon"> <i class="mdi mdi-border-color" data-toggle="modal" data-target="#modal"></i></a></center>
									</td>

								</tr>
								<?php } ?>
							</tbody>
						</table>
					</div>
					<div class="progress-w-percent-s"></div>
					<div class="form-row">
						<div class="form-group col-md-6 mt-2">
							<div class="col-sm-4">
								<div class="list_footer">
									选中项：<a href="javascript:void(0);" onclick="delsubmit()" id="delsubmit">删除</a>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<nav aria-label="Page navigation example">
								<ul class="pagination justify-content-end">
									<?php if (!$so) {
										echo pagination($nums, $ENUMS, $page, $url);
									} ?>
								</ul>
							</nav>
						</div>
					</div>
				</form>
			</div> <!-- end card-body-->
		</div> <!-- end card-->
	</div> <!-- end col -->
</div>
<!-- end row -->

<div id="modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="modal_title">添加APP</h4>
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			</div>
			<div class="modal-body">
				<?php if ($app_num > 0) : ?>
					<form class="pl-6 pr-6" method="post">
						<input class="form-control" type="number" id="id" name="id" value="" placeholder="ID" hidden>

						<div class="form-group">
							<label>名称</label>
							<input type="text" id="name" name="name" class="form-control" placeholder="名称" value="" required>
						</div>
						
						<div class="form-group">
							<label>包名</label>
							<input type="text" id="only" name="only" class="form-control" placeholder="包名" value="" required>
						</div>

						<div class="form-group">
							<label>图标链接</label>
							<input type="text" id="icon" name="icon" class="form-control" placeholder="图标链接" value="" required>
						</div>

						<div class="form-group">
							<label>安装包链接</label>
							<input type="text" id="download" name="download" class="form-control" placeholder="安装包链接" value="" required>
						</div>

						<div class="form-group">
							<label>版本</label>
							<input type="text" id="version" name="version" class="form-control" placeholder="版本" value="" required>
						</div>

						<div class="form-group">
							<label>安装包大小</label>
							<input type="text" id="size" name="size" class="form-control" placeholder="安装包大小" value="" required>
						</div>

						<div class="form-group">
							<label>描述</label>
							<input type="text" id="details" name="details" class="form-control" placeholder="描述" value="" required>
						</div>

						<div class="form-group">
							<label>绑定分类</label>
							<select class="form-control" name="classify" id="classify">
								<?php
								$res = Db::table('classify')->order('id desc')->select();
								foreach ($res as $k => $v) {
									$rows = $res[$k];
								?>
									<option value="<?php echo $rows['id']; ?>"><?php echo $rows['name']; ?></option>
								<?php } ?>
							</select>
						</div>

						<div id="add" class="form-group text-center">
							<button class="btn btn-primary" type="submit" name="add_submit" id="add_submit" value="确定">确认添加</button>
						</div>

						<div id="edit" class="form-group text-center" hidden>
							<button class="btn btn-primary" type="submit" name="edit_submit" id="edit_submit" value="确定">确认编辑</button>
						</div>
					</form>
				<?php else : ?>
					<div class="text-center" style="margin-top:4rem!important;margin-bottom:6rem!important">
						<img src="../assets/images/no-app.svg" height="120" alt="File not found Image">
						<h4 class="text-uppercase mt-3 mb-3">需要添加应用后开启该功能</h4>
						<a href="./?app_adm"><button type="button" class="btn btn-dark">添加应用</button></a>
					</div>
				<?php endif; ?>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
	$('#add_submit').click(function() {
		let t = window.jQuery;
		var name = $("input[name='name']").val();
		var only = $("input[name='only']").val();
		var icon = $("input[name='icon']").val();
		var download = $("input[name='download']").val();
		var version = $("input[name='version']").val();
		var size = $("input[name='size']").val();
		var details = $("input[name='details']").val();
		var classify = $("select[name='classify']").val();
		document.getElementById('add_submit').innerHTML = "<span class=\"spinner-border spinner-border-sm mr-1\" role=\"status\" aria-hidden=\"true\"></span>正在添加";
		document.getElementById('add_submit').disabled = true;
		$.ajax({
			cache: false,
			type: "POST", //请求的方式
			url: "ajax.php?act=application_app_add", //请求的文件名
			data: {
				name: name,
				only: only,
				icon: icon,
				download: download,
				version: version,
				size: size,
				details: details,
				classify_id: classify
			},
			dataType: 'json',
			success: function(data) {
				console.log(data);
				document.getElementById('add_submit').disabled = false;
				document.getElementById('add_submit').innerHTML = "确认添加";
				if (data.code == 200) {
					t.NotificationApp.send("成功", data.msg, "top-center", "rgba(0,0,0,0.2)", "success")
					window.setTimeout("window.location='" + window.location.href + "'", 1000);
				} else {
					t.NotificationApp.send("失败", data.msg, "top-center", "rgba(0,0,0,0.2)", "error")
				}
			}
		});
		return false; //重要语句：如果是像a链接那种有href属性注册的点击事件，可以阻止它跳转。
	});

	$('#edit_submit').click(function() {
		let t = window.jQuery;
		var id = $("input[name='id']").val();
		var name = $("input[name='name']").val();
		var only = $("input[name='only']").val();
		var icon = $("input[name='icon']").val();
		var download = $("input[name='download']").val();
		var version = $("input[name='version']").val();
		var size = $("input[name='size']").val();
		var details = $("input[name='details']").val();
		var classify = $("select[name='classify']").val();
		document.getElementById('edit_submit').innerHTML = "<span class=\"spinner-border spinner-border-sm mr-1\" role=\"status\" aria-hidden=\"true\"></span>正在编辑";
		document.getElementById('edit_submit').disabled = true;

		$.ajax({
			cache: false,
			type: "POST", //请求的方式
			url: "ajax.php?act=application_app_edit", //请求的文件名
			data: {
				id: id,
				name: name,
				only: only,
				icon: icon,
				download: download,
				version: version,
				size: size,
				details: details,
				classify_id: classify
			},
			dataType: 'json',
			success: function(data) {
				console.log(data);
				document.getElementById('edit_submit').disabled = false;
				document.getElementById('edit_submit').innerHTML = "确认编辑";
				if (data.code == 200) {
					t.NotificationApp.send("成功", data.msg, "top-center", "rgba(0,0,0,0.2)", "success")
					window.setTimeout("window.location='" + window.location.href + "'", 1000);
				} else {
					t.NotificationApp.send("失败", data.msg, "top-center", "rgba(0,0,0,0.2)", "error")
				}
			}
		});
		return false; //重要语句：如果是像a链接那种有href属性注册的点击事件，可以阻止它跳转。
	});

	function modal_cut(type, id, name, only, icon, download, version, size, details, classifyId) {
		if (type == 'add') {
			document.getElementById("modal_title").innerHTML = "添加APP";
			document.getElementById("add").removeAttribute("hidden");
			document.getElementById("edit").setAttribute("hidden", true);
			document.getElementById("name").value = '';
			document.getElementById("only").value = '';
			document.getElementById("icon").value = '';
			document.getElementById("download").value = '';
			document.getElementById("version").value = '';
			document.getElementById("size").value = '';
			document.getElementById("details").value = '';
		} else {
			document.getElementById("modal_title").innerHTML = "编辑APP";
			document.getElementById("edit").removeAttribute("hidden");
			document.getElementById("add").setAttribute("hidden", true);
			document.getElementById("id").value = id;
			document.getElementById("name").value = name;
			document.getElementById("only").value = only;
			document.getElementById("icon").value = icon;
			document.getElementById("download").value = download;
			document.getElementById("version").value = version;
			document.getElementById("size").value = size;
			document.getElementById("details").value = details;
			document.getElementById("classify").value = classifyId;
		}
	}

	function checkAll() {
		var code_Values = document.getElementsByTagName("input");
		var all = document.getElementById("all");
		if (code_Values.length) {
			for (i = 0; i < code_Values.length; i++) {
				if (code_Values[i].type == "checkbox") {
					code_Values[i].checked = all.checked;
				}
			}
		} else {
			if (code_Values.type == "checkbox") {
				code_Values.checked = all.checked;
			}
		}
	}

	function delsubmit() {
		var id_array = new Array();
		$("input[name='ids[]']:checked").each(function() {
			id_array.push($(this).val()); //向数组中添加元素 
		}); //获取界面复选框的所有值
		//ar chapterstr = id_array.join(',');//把复选框的值以数组形式存放
		var url = window.location.href;
		let t = window.jQuery;
		if (id_array.length <= 0) {
			t.NotificationApp.send("提示", "请选择要删除的项目", "top-center", "rgba(0,0,0,0.2)", "warning")
			return false;
		}
		document.getElementById("delsubmit").innerHTML = "<div class=\"spinner-border spinner-border-sm mr-1\" style=\"margin-bottom:2px!important\" role=\"status\"></div>删除中";
		document.getElementById("delsubmit").className = "text-title";
		$("#delsubmit").attr("disabled", true).css("pointer-events", "none");

		console.log(id_array);
		$.ajax({
			cache: false,
			type: "POST", //请求的方式
			url: "ajax.php?act=application_app_del", //请求的文件名
			data: {
				id: id_array
			},
			dataType: 'json',
			success: function(data) {
				if (data.code == 200) {
					t.NotificationApp.send("成功", data.msg, "top-center", "rgba(0,0,0,0.2)", "success")
				} else {
					t.NotificationApp.send("失败", data.msg, "top-center", "rgba(0,0,0,0.2)", "error")
				}
				//console.log(data);
				window.setTimeout("window.location='" + url + "'", 1000);
			}
		});
		return false; //重要语句：如果是像a链接那种有href属性注册的点击事件，可以阻止它跳转。
	}
</script>