<?php
/*
Sort:4
Hidden:true
Name:首页广告
Url:app_homead
Right:app_homead
Version:1.0
Author:易如意
Author QQ:51154393
Author Url:www.eruyi.cn
*/

if (!isset($islogin)) header("Location: /"); //非法访问
$nums = Db::table('app_homead')->count();
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$url = "./?app_homead&page=";
$bnums = ($page - 1) * $ENUMS;
?>

<!-- start page title -->
<div class="row">
	<div class="col-12">
		<div class="page-title-box">
			<div class="page-title-right">
				<ol class="breadcrumb m-0">
					<li class="breadcrumb-item"><a href="javascript: void(0);">首页</a></li>
					<li class="breadcrumb-item active">应用</li>
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
						<button type="button" onclick="modal_cut('add',0,0,0,0)" class="btn btn-danger mb-2 mr-2" data-toggle="modal" data-target="#modal"><i class="mdi mdi-briefcase-plus-outline mr-1"></i>添加扩展配置</button>
					</div>
					<div class="col-lg-4">
						<div class="text-lg-right">
							<form action="" method="post">
								<div class="input-group">
									<input type="text" class="form-control" name="so" placeholder="搜索配置" value='<?php echo $so; ?>'>
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
									<th style="width: 100px;">
										<center><span class="badge badge-light-lighten">名称</span></center>
									</th>
									<th style="width: 150px;">
										<center><span class="badge badge-light-lighten">可否搜索</span></center>
									</th>
									<th style="width: 150px;">
										<center><span class="badge badge-light-lighten">应用名称</span></center>
									</th>
									<th style="width: 75px;">
										<center><span class="badge badge-light-lighten">编辑</span></center>
									</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$app = Db::table('app_homead', 'as K')->field('K.*,A.name as appname')->JOIN('app', 'as A', 'K.appid=A.id');
								if ($so) {
									$app = $app->where('K.name', 'like', "%{$so}%")->whereOr('K.data', 'like', "%{$so}%")->order('id desc');
								} else {
									$app = $app->order('id desc')->limit($bnums, $ENUMS);
								}
								$res = $app->select(); //false
								//die($res);
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
											<center><?php if ($rows['searchable'] == '0') : ?><span class="badge badge-danger">关闭<?php else : ?><span class="badge badge-success">开启<?php endif; ?></span></center>
										</td>
										<td>
											<center>
												<?php if (empty($rows['appname'])) : ?><span class="badge badge-danger"><i class="mdi mdi-close-circle"></i> 应用不存在
													<?php else : ?><span class="badge badge-primary"><i class="mdi mdi-cube-outline"></i> <?php echo $rows['appname']; ?>
														<?php endif; ?>
														</span></center>
										</td>
										<td>
											<center><a href="javascript:void(0);" onclick="modal_cut('edit',<?php echo $rows['id']; ?>,'<?php echo $rows['name']; ?>','<?php echo $rows['data']; ?>',<?php echo $rows['appid']; ?>,'<?php echo $rows['searchable']; ?>')" class="action-icon"> <i class="mdi mdi-border-color" data-toggle="modal" data-target="#modal"></i></a></center>
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
				<h4 class="modal-title" id="modal_title">添加应用扩展配置</h4>
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			</div>
			<div class="modal-body">
				<?php if ($app_num > 0) : ?>
					<form class="pl-6 pr-6" method="post">
						<input class="form-control" type="number" id="homead_id" name="homead_id" value="" placeholder="配置ID" hidden>
						<!--style="display:none" -->
						<div class="form-group">
							<label class="col-form-label">扩展变量</label>
							<input class="form-control" type="text" id="homead_name" name="homead_name" placeholder="变量名称仅限字母加数字" required>
						</div>

						<div class="form-group">
							<label>扩展内容</label>
							<textarea id="homead_data" name="homead_data" class="form-control" maxlength="255" rows="4" placeholder="扩展内容"><?php echo $res['homead_data']; ?></textarea>
						</div>

						<div class="form-group">
							<label>绑定应用</label>
							<select class="form-control" name="homead_appid" id="homead_appid">
								<?php
								$res = Db::table('app')->order('id desc')->select();
								foreach ($res as $k => $v) {
									$rows = $res[$k];
								?>
									<option value="<?php echo $rows['id']; ?>"><?php echo $rows['name']; ?></option>
								<?php } ?>
							</select>
						</div>

						<div class="eruyi-checkbox">
							<label class="eruyi-label">按OK键搜索</label>&nbsp;&nbsp;
							<input type="checkbox" id="searchable" <?php if ($rows['searchable'] == 'y') : ?>checked<?php endif; ?> data-switch="success" onchange="" />
							<label for="searchable" data-on-label="开启" data-off-label="关闭"></label>
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
		var searchable = document.getElementById("searchable").checked;
		if (searchable) {
			searchable = 1;
		} else {
			searchable = 0;
		}
		let t = window.jQuery;
		var homead_name = $("input[name='homead_name']").val();
		var homead_data = $("textarea[name='homead_data']").val();
		var homead_appid = $("select[name='homead_appid']").val();
		document.getElementById('add_submit').innerHTML = "<span class=\"spinner-border spinner-border-sm mr-1\" role=\"status\" aria-hidden=\"true\"></span>正在添加";
		document.getElementById('add_submit').disabled = true;
		$.ajax({
			cache: false,
			type: "POST", //请求的方式
			url: "ajax.php?act=app_homead_add", //请求的文件名
			data: {
				name: homead_name,
				data: homead_data,
				appid: homead_appid,
				searchable: searchable
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
		var searchable = document.getElementById("searchable").checked;
		if (searchable) {
			searchable = 1;
		} else {
			searchable = 0;
		}
		let t = window.jQuery;
		var homead_id = $("input[name='homead_id']").val();
		var homead_name = $("input[name='homead_name']").val();
		var homead_data = $("textarea[name='homead_data']").val();
		var homead_appid = $("select[name='homead_appid']").val();
		document.getElementById('edit_submit').innerHTML = "<span class=\"spinner-border spinner-border-sm mr-1\" role=\"status\" aria-hidden=\"true\"></span>正在编辑";
		document.getElementById('edit_submit').disabled = true;

		$.ajax({
			cache: false,
			type: "POST", //请求的方式
			url: "ajax.php?act=app_homead_edit", //请求的文件名
			data: {
				id: homead_id,
				name: homead_name,
				data: homead_data,
				appid: homead_appid,
				searchable: searchable
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

	function modal_cut(type, id, name, data, appid, searchable) {
		if (type == 'add') {
			document.getElementById("modal_title").innerHTML = "添加应用扩展配置";
			document.getElementById("add").removeAttribute("hidden");
			document.getElementById("edit").setAttribute("hidden", true);
			document.getElementById("homead_id").value = '';
			document.getElementById("homead_name").value = '';
			document.getElementById("homead_data").value = '';
		} else {
			document.getElementById("modal_title").innerHTML = "编辑应用扩展配置";
			document.getElementById("edit").removeAttribute("hidden");
			document.getElementById("add").setAttribute("hidden", true);
			document.getElementById("homead_id").value = id;
			document.getElementById("homead_name").value = name;
			document.getElementById("homead_data").value = data;
			document.getElementById("homead_appid").value = appid;
			if (searchable == 1) {
				document.getElementById("searchable").checked = true;
			} else {
				document.getElementById("searchable").checked = false;
			}
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
			url: "ajax.php?act=app_homead_del", //请求的文件名
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