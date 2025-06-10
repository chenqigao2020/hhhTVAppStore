<?php
/*
Name:用户操作API
Version:1.0
Author:易如意
Author QQ:51154393
Author Url:www.eruyi.cn
*/
	if(!isset($islogin))header("Location: /");//非法访问
	
	if($act == 'add'){
		$user = isset($_POST['user']) ? purge($_POST['user']) : '';
		$pwd = isset($_POST['pwd']) ? purge($_POST['pwd']) : '';
		$appid = isset($_POST['appid']) ? intval($_POST['appid']) : 0;
		$reg_time = time();
		if($user == '')json(201,'账号不能为空');
		if($pwd == '')json(201,'密码不能为空');
		if($appid == 0)json(201,'绑定应用不能为空');
		if(preg_match ("/^[\w]{5,11}$/",$user)==0)json(201,'账号长度5-11位数');
		if(preg_match ("/^[a-zA-Z\d.*_-]{6,18}$/",$pwd)==0)json(201,'密码长度需要满足6-18位数,不支持中文以及.-*_以外特殊字符');
		$app_res = Db::table('app')->where('id',$appid)->find();
		if(!$app_res)json(201,'应用不存在');
		$user_res = Db::table('user')->where(['appid'=>$appid,'user'=>$user])->find();
		if($user_res)json(201,'账号已存在');
		$add_res = Db::table('user')->add(['user'=>$user,'pwd'=>md5($pwd),'appid'=>$appid,'reg_time'=>$reg_time]);
		//die($res); 
		if($add_res){
			if(defined('ADM_LOG') && ADM_LOG == 1){Db::table('log')->add(['group'=>'adm','type'=>'user_add','status'=>200,'time'=>time(),'ip'=>getip(),'data'=>json_encode($_POST)]);}//记录日志
			json(200,'添加成功');
		}else{
			if(defined('ADM_LOG') && ADM_LOG == 1){Db::table('log')->add(['group'=>'adm','type'=>'user_add','status'=>201,'time'=>time(),'ip'=>getip(),'data'=>json_encode($_POST)]);}//记录日志
			json(201,'添加失败');
		}
	}
	if($act == 'muladd'){
	    //测试生成txt文件
	    $dirname = './muladd/';
        if(!is_dir($dirname)){
            mkdir($dirname,0775);
        }


	    $num = isset($_POST['num']) ? purge($_POST['num']) : '';
		$days = isset($_POST['days']) ? purge($_POST['days']) : '';
		$appid = isset($_POST['appid']) ? intval($_POST['appid']) : 0;
		$now = isset($_POST['now']) ? intval($_POST['now']) : 0;

		$reg_time = time();
		if($num <= 0)json(201,'账号个数不能为空');
		if($days <= 0)json(201,'会员时间不能为空');
		if($appid == 0)json(201,'绑定应用不能为空');
		if($num > 1000) {
		    json(201,'账号个数不能大于1000');
		} 
		$app_res = Db::table('app')->where('id',$appid)->find();
		if(!$app_res)json(201,'应用不存在');

		if(defined('ADM_LOG') && ADM_LOG == 1){Db::table('log')->add(['group'=>'adm','type'=>'user_add','status'=>200,'time'=>time(),'ip'=>getip(),'data'=>json_encode($_POST)]);}//记录日志
    
		$i = 0;
		$arr = array();
		
        $file_name = "{$reg_time}.txt";   //保存的文件名称
        
        $myfile = fopen($dirname . $file_name, "w");


		while($i < $num){
		    //账号名称 随机8位数字
		    $username = rand(10000000,99999999);
		    if(in_array($username,$arr)){
		        $username = rand(10000000,99999999);
		    }
		    $arr[] = $username;
		    //账号密码 随机6位数字
		    $password = rand(100000,999999);
		    if($days >= 999999){
		        $vip = $days;
		    }else{
		        $vip = time() + $days * 24 * 3600;
		    }
		    $add_res = Db::table('user')->add(['user'=>$username,'pwd'=>md5($password),'appid'=>$appid,'vip'=>$vip,'reg_time'=>$reg_time]);
            if($add_res){
                //保存数据到文件
        
                fwrite($myfile,"账号:".$username."\n密码:".$password."\n\n");
            }
		    $i++;
		}
	     fclose($myfile);
	     $res = array(
	         'code'=>200,
	         'msg'=>'添加成功',
	         
         );
         if($now){
             $res['data'] = file_get_contents($dirname.$file_name);
         }
		echo json_encode($res);exit;
	}
	if($act == 'edit'){
		$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
		$update['fen'] = isset($_POST['fen']) ? intval($_POST['fen']) : 0;
		$update['vip'] = isset($_POST['vip']) ? intval($_POST['vip']) : 0;
		$update['ban'] = isset($_POST['ban']) ? intval($_POST['ban']) : 0;
		$update['ban_notice'] = isset($_POST['ban_notice']) ? purge($_POST['ban_notice']) : '';
		$update['openid_qq'] = isset($_POST['openid_qq']) ? purge($_POST['openid_qq']) : '';
		$update['openid_wx'] = isset($_POST['openid_wx']) ? purge($_POST['openid_wx']) : '';
		$pwd = isset($_POST['pwd']) ? purge($_POST['pwd']) : '';
		if($pwd != ''){
			$pass = md5($pwd);
			$update['pwd'] = $pass;
		}
		
		$res = Db::table('user')->where('id',$id)->update($update);
		//die($res); 
		if($res){
			if(defined('ADM_LOG') && ADM_LOG == 1){Db::table('log')->add(['group'=>'adm','type'=>'user_edit','status'=>200,'time'=>time(),'ip'=>getip(),'data'=>json_encode($_POST)]);}//记录日志
			json(200,'编辑成功');
		}else{
			if(defined('ADM_LOG') && ADM_LOG == 1){Db::table('log')->add(['group'=>'adm','type'=>'user_edit','status'=>201,'time'=>time(),'ip'=>getip(),'data'=>json_encode($_POST)]);}//记录日志
			json(201,'编辑失败');
		}
	}
	
	if($act == 'del'){//删除用户
		$id = isset($_POST['id']) ? $_POST['id'] : '';
		if($id){
			$ids = '';
			foreach ($id as $value) {
				$ids .= intval($value).",";
			}
			$ids = rtrim($ids, ",");
			$res = Db::table('user')->where('id','in','('.$ids.')')->del();//false
			//die($res);
			if($res){
				if(defined('ADM_LOG') && ADM_LOG == 1){Db::table('log')->add(['group'=>'adm','type'=>'user_del','status'=>200,'time'=>time(),'ip'=>getip(),'data'=>json_encode($_POST)]);}//记录日志
				json(200,'删除成功');
			}else{
				if(defined('ADM_LOG') && ADM_LOG == 1){Db::table('log')->add(['group'=>'adm','type'=>'user_del','status'=>201,'time'=>time(),'ip'=>getip(),'data'=>json_encode($_POST)]);}//记录日志
				json(201,'删除失败');
			}
		}else{
			json(201,'没有需要删除的数据');
		}
	}
?>