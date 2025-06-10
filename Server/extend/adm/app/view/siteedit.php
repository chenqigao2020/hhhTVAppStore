<?php
/*
Sort:1
Hidden:true
Name:编辑站点
Url:app_siteedit
Version:1.0
Author:易如意
Author QQ:51154393
Author Url:www.eruyi.cn
*/

if (!isset($islogin)) header("Location: /"); //非法访问
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$res = Db::table('site')->where(['id' => $id])->find();
?>
<div class="row">
	<div class="col-12">
		<div class="page-title-box">
			<div class="page-title-right">
				<ol class="breadcrumb m-0">
					<li class="breadcrumb-item"><a href="javascript: void(0);">首页</a></li>
					<li class="breadcrumb-item"><a href="site_adm.php">站点管理</a></li>
					<li class="breadcrumb-item active">编辑站点</li>
				</ol>
			</div>
			<h4 class="page-title"><?php echo $title; ?></h4>
		</div> <!-- end page-title-box -->
	</div> <!-- end col-->
</div>
<!-- end page title -->

<!-- Form row -->
<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-body">
				<form action="" method="post" id="addimg" name="addimg">
					<div class="eruyi-checkbox">
						<input type="checkbox" id="state" <?php if ($res['state'] == 'y') : ?>checked<?php endif; ?> data-switch="success" onchange="state_v(this.checked)" />
						<label for="state" data-on-label="正常" data-off-label="停售"></label>
						<label class="eruyi-label">站点状态</label>
					</div>

					<div class="view" name="state_y" id="state_y" <?php if ($res['state'] == 'y') : ?> style="display: block" <?php endif; ?>>
						<p class="text-muted">
							正常状态情况下，可以被 <code>站点列表接口</code> 正常输出
						</p>
					</div>

					<div class="view" name="state_n" id="state_n" <?php if ($res['state'] == 'n') : ?> style="display: block" <?php endif; ?>>
						<p class="text-muted">
							停售状态情况下，<code>站点列表接口</code> 不在输出此站点
						</p>
					</div>

					<div class="form-row">
						<div class="form-group col-md-12">
							<label for="f_user" class="col-form-label">站点名称 *</label>
							<input type="text" class="form-control" id="name" name="name" placeholder="站点名称" value="<?php echo $res['name']; ?>" required>
						</div>
					</div>

					<div class="form-row">
						<div class="form-group col-md-2">
							<label class="col-form-label">接口类型</label>
							<select class="form-control" name="type" id="type" onchange="type_change()">
								<option value="XML" <?php if ($res['type'] == 'XML') echo 'selected = "selected"'; ?>>XML</option>
								<option value="JSON" <?php if ($res['type'] == 'JSON') echo 'selected = "selected"'; ?>>JSON</option>
								<option value="Spider" <?php if ($res['type'] == 'Spider') echo 'selected = "selected"'; ?>>自定义爬虫</option>
							</select>
						</div>
						<div class="form-group col-md-10">
							<label class="col-form-label">API <a href="https://lvdoui.net/post/15.html" target="_biank">如何对接自己的苹果CMS</a></label>
							<input type="text" class="form-control" id="api" name="api" placeholder="站点名称" value="<?php echo $res['api']; ?>" required>
						</div>
					</div>

					<div class="form-row">
						<div class="form-group col-md-12">
							<label for="f_psw" class="col-form-label">PlayUrl</label>
							<input type="parse" class="form-control" id="parse" name="parse" placeholder="playUrl" value="<?php echo $res['parse']; ?>" required>
						</div>
					</div>

					<div class="form-row">
						<div class="form-group col-md-12">
							<label for="f_psw" class="col-form-label" id="extend_name" name="extend_name">扩展信息</label>
							<input type="extend" class="form-control" id="extend" name="extend" placeholder="ext" value="<?php echo $res['extend']; ?>" required>
						</div>
					</div>

					<div class="form-row">
						<div class="form-group col-md-12">
							<label for="f_vip" class="col-form-label">绑定应用 *</label>
							<select class="form-control" name="appid" id="appid">
								<?php
								$app_res = Db::table('app')->order('id desc')->select();
								foreach ($app_res as $k => $v) {
									$rows = $app_res[$k];
								?>
									<option value="<?php echo $rows['id']; ?>" <?php if ($res['appid'] == $rows['id']) echo 'selected = "selected"'; ?>><?php echo $rows['name']; ?></option>
								<?php } ?>

							</select>
						</div>
					</div>
					<div class="form-row">
						<div class="custom-control custom-checkbox">
							&nbsp;&nbsp;
							<input type="checkbox" class="custom-control-input" id="searchable" <?php if ($res['searchable'] == '1') : ?> checked <?php endif; ?> name="searchable" value="y" required>
							<label class="custom-control-label" for="searchable">普通搜索</label>
						</div>
						<div class="custom-control custom-checkbox">
							&nbsp;&nbsp;
							<input type="checkbox" class="custom-control-input" id="quicksearch" <?php if ($res['quicksearch'] == '1') : ?> checked <?php endif; ?> name="quicksearch" value="y" required>
							<label class="custom-control-label" for="quicksearch">快速搜索</label>
						</div>
						<div class="custom-control custom-checkbox">
							&nbsp;&nbsp;
							<input type="checkbox" class="custom-control-input" id="filterable" <?php if ($res['filterable'] == '1') : ?> checked <?php endif; ?> name="filterable" value="y" required>
							<label class="custom-control-label" for="filterable">参与筛选</label>
						</div>
						<br /><br />
					</div>
					<button type="submit" class="btn btn-block btn-primary" name="submit" id="submit" value="确认">确认编辑</button>
				</form>

			</div> <!-- end card-body -->
		</div> <!-- end card-->
	</div> <!-- end col -->
</div><!-- end row -->

<script>
	$("#site_adm").addClass("active");
	$('#submit').click(function() {
		let t = window.jQuery;
		var state = document.getElementById("state").checked;
		if (state) {
			state = 'y';
		} else {
			state = 'n';
		}
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
		var name = $("input[name='name']").val();
		var type = $("select[name='type']").val();
		var api = $("input[name='api']").val();
		var extend = $("input[name='extend']").val();
		var parse = $("input[name='parse']").val();
		var appid = $("select[name='appid']").val();
		document.getElementById('submit').innerHTML = "<span class=\"spinner-border spinner-border-sm mr-1\" role=\"status\" aria-hidden=\"true\"></span>正在编辑";
		document.getElementById('submit').disabled = true;
		$.ajax({
			cache: false,
			type: "POST", //请求的方式
			url: "ajax.php?act=app_siteedit", //请求的文件名
			data: {
				id: <?php echo $id; ?>,
				name: name,
				type: type,
				api: api,
				extend: extend,
				parse: parse,
				state: state,
				appid: appid,
				filterable: filterable,
				searchable: searchable,
				quicksearch: quicksearch
			},
			dataType: 'json',
			success: function(data) {
				console.log(data);
				document.getElementById('submit').disabled = false;
				document.getElementById('submit').innerHTML = "确认添加";
				if (data.code == 200) {
					t.NotificationApp.send("成功", data.msg, "top-center", "rgba(0,0,0,0.2)", "success")
					document.getElementById("ok").checked = false;
					//window.setTimeout("window.location='"+window.location.href+"'",1000);
				} else {
					t.NotificationApp.send("失败", data.msg, "top-center", "rgba(0,0,0,0.2)", "error")
				}
			}
		});
		return false; //重要语句：如果是像a链接那种有href属性注册的点击事件，可以阻止它跳转。
	});

	function type_change() {
		if ($('#type').val() == 'Spider') {
			// document.getElementById("extend_name").innerHTML = "扩展接口";
		} else {
			// document.getElementById("extend_name").innerHTML = "要显示的分类，留空全显";
		}
	}

	function state_v(i) {
		if (i == true) {
			$("#state_y").css("display", "block");
			$("#state_n").css("display", "none");
		} else {
			$("#state_y").css("display", "none");
			$("#state_n").css("display", "block");
		}
	}
</script>