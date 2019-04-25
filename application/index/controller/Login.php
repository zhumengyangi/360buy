<?php

namespace app\index\controller;

use think\Controller;
use think\Request;

class Login extends Controller
{

    /**
     * @desc  注册页面
     * @return mixed
     */
    public function register()
    {

        return $this->fetch('register');

    }


    /**
     * @desc   登录页面
     * @return [type] [description]
     */
    public function login()
    {

    	return $this->fetch('login');

    }


    /**
     * @desc  退出功能
     * @return [type] [description]
     */
    public function logout()
    {

    	//	请求token信息验证信息
    	$token = isset($_COOKIE['360_token']) ? $_COOKIE['360_token'] : "";

    	//	请求接口的域名地址
    	$url = config('api_url');

    	//	调用退出接口
    	httpCurl($url."/api/logout","post", ['token' => $token]);

    	return $this->redirect('index/login/login');
    
    }

}
