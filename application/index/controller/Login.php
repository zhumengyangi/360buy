<?php

namespace app\index\controller;

use think\Controller;
use think\Request;

class Login extends Controller
{

    /**
     * @desc  登录
     * @return mixed
     */
    public function register()
    {

        return $this->fetch('register');

    }




}
