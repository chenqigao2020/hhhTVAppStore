<?php
/*
Sort:2
Hidden:true
Name:添加站点
Url:app_site
Right:site
Version:1.0
Author:易如意
Author QQ:51154393
Author Url:www.eruyi.cn
*/

if (!isset($islogin)) header("Location: /"); //非法访问
$nums = Db::table('site')->count(); //获取商品总数
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$url = "./?app_site&page=";
$bnums = ($page - 1) * $ENUMS;
?>

<!-- start page title -->
<div class="row">
	<div class="col-12">
		<div class="page-title-box">
			<div class="page-title-right">
				<ol class="breadcrumb m-0">
					<li class="breadcrumb-item"><a href="javascript: void(0);">首页</a></li>
					<li class="breadcrumb-item active">商品</li>
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
						<button type="button" class="btn btn-danger mb-2 mr-2" data-toggle="modal" data-target="#add"><i class="mdi mdi-cart-plus mr-1"></i>添加站点</button>
					</div>
					<div class="col-lg-4">
						<div class="text-lg-right">
							<form action="" method="post">
								<div class="input-group">
									<input type="text" class="form-control" name="so" placeholder="搜索商品" value='<?php echo $so; ?>'>
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
										<center><span class="badge badge-light-lighten">ID</span></center>
									</th>
									<th><span class="badge badge-light-lighten">站点名称</span></th>
									<th style="width: 150px;">
										<center><span class="badge badge-light-lighten">api</span></center>
									</th>
									<th style="width: 150px;">
										<center><span class="badge badge-light-lighten">普通搜索</span></center>
									</th>
									<th style="width: 150px;">
										<center><span class="badge badge-light-lighten">快速搜索</span></center>
									</th>
									<th style="width: 150px;">
										<center><span class="badge badge-light-lighten">参与筛选</span></center>
									</th>
									<th style="width: 150px;">
										<center><span class="badge badge-light-lighten">状态</span></center>
									</th>
									<th style="width: 150px;">
										<center><span class="badge badge-light-lighten">应用名称</span></center>
									</th>
									<th style="width: 75px;">
										<center><span class="badge badge-light-lighten">管理</span></center>
									</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$site = Db::table('site', 'as G')->field('G.*,A.name as appname,IFNULL(D.ds,0) as dnum,IFNULL(C.sc,0) as cnum')->JOIN('app', 'as A', 'G.appid=A.id')->JOIN("(SELECT gid,COUNT(*) AS sc FROM {$DP}site_order where `state` = 2 GROUP BY gid) AS C", 'G.id=C.gid')->JOIN("(SELECT gid,COUNT(*) AS ds FROM {$DP}site_order GROUP BY gid) AS D", 'G.id=D.gid');
								if ($so) {
									$site = $site->where('G.name', 'like', "%{$so}%")->whereOr('G.jie', 'like', "%{$so}%")->whereOr('A.name', 'like', "%{$so}%")->order('id desc');
								} else {
									$site = $site->order('id desc')->limit($bnums, $ENUMS);
								}
								$res = $site->select();
								// die(print_r($site));
								foreach ($res as $k => $v) {
									$rows = $res[$k];
									if (empty($rows['appname'])) continue;
								?>
									<tr>
										<td>
											<div class="custom-control custom-checkbox">
												<input type="checkbox" class="custom-control-input" name="ids[]" value="<?php echo $rows['id']; ?>" id="<?php echo 'check_' . $rows['id']; ?>">
												<label class="custom-control-label" for="<?php echo 'check_' . $rows['id']; ?>"></label>
											</div>
										</td>
										<td>
											<center><?php echo $rows['id']; ?></center>
										</td>
										<td>
											<?php echo $rows['name']; ?>
										</td>
										<td>
											<center><span class="badge badge-info-lighten"><?php echo mb_substr($rows['api'], 0, 50, 'utf-8') . "..."; ?></span></center>
										</td>
										<td>
											<center><?php if ($rows['searchable'] == '0') : ?><span class="badge badge-danger">关闭<?php else : ?><span class="badge badge-success">开启<?php endif; ?></span></center>
										</td>
										<td>
											<center><?php if ($rows['quicksearch'] == '0') : ?><span class="badge badge-danger">关闭<?php else : ?><span class="badge badge-success">开启<?php endif; ?></span></center>
										</td>
										<td>
											<center><?php if ($rows['filterable'] == '0') : ?><span class="badge badge-danger">关闭<?php else : ?><span class="badge badge-success">开启<?php endif; ?></span></center>
										</td>
										<td>
											<center><?php if ($rows['state'] == 'n') : ?><span class="badge badge-danger">停用<?php else : ?><span class="badge badge-success">启动<?php endif; ?></span></center>
										</td>
										<td>
											<center>
												<?php if (empty($rows['appname'])) : ?><span class="badge badge-danger"><i class="mdi mdi-close-circle"></i> 应用不存在
													<?php else : ?><span class="badge badge-primary"><i class="mdi mdi-cube-outline"></i> <?php echo $rows['appname']; ?>
														<?php endif; ?>
														</span></center>
										</td>
										<td>
											<center><a href="./?app_siteedit&id=<?php echo $rows['id']; ?>" class="action-icon"> <i class="mdi mdi-border-color"></i></a></center>
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
</div><!-- end row -->

<div id="add" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="add">添加站点</h4>
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			</div>
			<div class="modal-body">
				<?php if ($app_num > 0) : ?>
					<form class="pl-3 pr-3" method="post">
						<div class="form-group">
							<label class="col-form-label">站点名称</label>
							<input class="form-control" type="text" id="add_name" name="add_name" placeholder="站点名称" required>
						</div>
						<div class="form-row">
							<div class="form-group col-md-4">
								<label>接口类型</label>
								<select class="form-control" name="add_type" id="add_type" onchange="type_change()">
									<option value="XML">XML</option>
									<option value="JSON">JSON</option>
									<option value="Spider">自定义爬虫</option>
								</select>
							</div>
							<div class="form-group col-md-8">
								<label>api</label>
								<input type="text" id="add_api" name="add_api" class="form-control" placeholder="csp_AppYsV2" value="">
							</div>
						</div>
						<div class="form-group">
							<label>PlayUrl</label>
							<input type="text" id="add_parse" name="add_parse" class="form-control" placeholder="解析接口" value="">
						</div>
						<div class="form-group">
							<label id="add_extend_name" name="add_extend_name">扩展信息</label>
							<input type="text" id="add_extend" name="add_extend" class="form-control" placeholder="ext" value="">
						</div>
						<div class="form-group">
							<label>绑定应用</label>
							<select class="form-control" name="add_appid" id="add_appid">
								<?php
								$res = Db::table('app')->order('id desc')->select();
								foreach ($res as $k => $v) {
									$rows = $res[$k];
								?>
									<option value="<?php echo $rows['id']; ?>"><?php echo $rows['name']; ?></option>
								<?php } ?>
							</select>
						</div>
						<div class="form-row">
							<div class="eruyi-checkbox">
								<label class="eruyi-label">普通搜索</label>&nbsp;
								<input type="checkbox" id="searchable" <?php if ($res['searchable'] == 'y') : ?>checked<?php endif; ?> data-switch="success" onchange="reg_state_v(this.checked)" />
								<label for="searchable" data-on-label="开启" data-off-label="关闭"></label>
							</div>
							<div class="eruyi-checkbox">
								<label class="eruyi-label">快速搜索</label>&nbsp;
								<input type="checkbox" id="quicksearch" <?php if ($res['quicksearch'] == 'y') : ?>checked<?php endif; ?> data-switch="success" onchange="reg_state_v(this.checked)" />
								<label for="quicksearch" data-on-label="开启" data-off-label="关闭"></label>
							</div>
							<div class="eruyi-checkbox">
								<label class="eruyi-label">参与筛选</label>&nbsp;
								<input type="checkbox" id="filterable" <?php if ($res['filterable'] == 'y') : ?>checked<?php endif; ?> data-switch="success" onchange="reg_state_v(this.checked)" />
								<label for="filterable" data-on-label="开启" data-off-label="关闭"></label>
							</div>
							<br /><br /><br /><br />
						</div>
						<div class="form-group text-center">
							<button class="btn btn-primary" type="submit" name="add_submit" id="add_submit" value="确定">确认添加</button>
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
	function reg_state_v(i) {
		console.log(i);
		if (i == true) {
			$("#reg_state_y").css("display", "block");
			$("#reg_state_n").css("display", "none");
		} else {
			$("#reg_state_y").css("display", "none");
			$("#reg_state_n").css("display", "block");
		}
	}

	function type_change() {
		if ($('#add_type').val() == 'Spider') {
			// document.getElementById("add_extend_name").innerHTML = "扩展接口";
		} else {
			// document.getElementById("add_extend_name").innerHTML = "要显示的分类，留空全显";
		}
	}

	$('#add_submit').click(function() {
		let t = window.jQuery;
		var add_name = $("input[name='add_name']").val();
		var add_type = $("select[name='add_type']").val();
		var add_api = $("input[name='add_api']").val();
		var add_extend = $("input[name='add_extend']").val();
		var add_parse = $("input[name='add_parse']").val();
		var add_appid = $("select[name='add_appid']").val();
		var filterable = document.getElementById("filterable").checked;
		if (filterable) {
			filterable = 1;
		} else {
			filterable = 0;
		}
		var searchable = document.getElementById("searchable").checked;
		if (searchable) {
			searchable = 1;
		} else {
			searchable = 0;
		}
		var quicksearch = document.getElementById("quicksearch").checked;
		if (quicksearch) {
			quicksearch = 1;
		} else {
			quicksearch = 0;
		}
		document.getElementById('add_submit').innerHTML = "<span class=\"spinner-border spinner-border-sm mr-1\" role=\"status\" aria-hidden=\"true\"></span>正在添加";
		document.getElementById('add_submit').disabled = true;
		$.ajax({
			cache: false,
			type: "POST", //请求的方式
			url: "ajax.php?act=app_siteadd", //请求的文件名
			data: {
				name: add_name,
				type: add_type,
				api: add_api,
				extend: add_extend,
				parse: add_parse,
				appid: add_appid,
				filterable: filterable,
				searchable: searchable,
				quicksearch: quicksearch
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
			url: "ajax.php?act=app_sitedel", //请求的文件名
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