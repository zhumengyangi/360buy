<?php

namespace app\index\controller;

use think\Controller;
use think\Request;

class User extends Controller
{

    //  存储用户信息
    protected $userInfo = null ;
    protected  $url = null;


    public function _initialize()
    {
        parent::_initialize(); // TODO: Change the autogenerated stub

        $this->assign([
            'current_url' => $_SERVER['REDIRECT_URL'],
        ]);

        //  请求接口的域名地址
        $this->url = config('api_url');

        //  验证登录
        if(empty($this->userInfo)){
            return $this->redirect('index/login/login');
        }

    }

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {

        //  首页
        $data = httpCurl($this->url.'/api/user/info/'.$this->userInfo['id'], "post");

        //  返回数据
        $this->assign([
            'user' => $data['data']
        ]);

        //  返回
        return $this->fetch('index');

    }


    /**
     * @desc  首页
     * @return mixed
     */
    public function index()
    {

        //  链接查到数据
        $data = httpCurl($this->url.'/api/user/info/'.$this->userInfo['id'],'post');

        //  返回数据
        $this->assign([
            'user' => $data['data']
        ]);

        //  返回
        return $this->fetch('index');

    }


    /**
     * @desc  保存用户的信息
     * @param Request $request
     */
    public function saveUser(Request $request)
    {

        //  获取全部参数
        $params = $request->param();

        //  想要修改的数据
        $res = httpCurl($this->url.'/api/user/modify','post',$params);

        //  不管修改成功与否返回
        return $this->redirect('/index/user/index');

    }

    /**
     * @desc  订单页面
     * @return mixed
     */
    public function order()
    {

        //  获取订单数据
        $data = httpCurl($this->url.'/api/user/order/'.$this->userInfo['id'],"post");

        //  返回数据
        $this->assign([
            'orders' => $data['data']
        ]);

        //  返回
        return $this->fetch('index');

    }


    /**
     * @desc  用户红包记录页面
     * @return mixed
     */
    public function bonus()
    {

        //  获取id
        $userId = $this->userInfo['id'];

        //  链接地址
        $bonus = httpCurl($this->url."/api/user/bonus/".$userId, "post");

        //  返回数据
        $this->assign([
            'user_bonus' => $bonus['data']
        ]);

        //  返回
        return $this->fetch('bonus');

    }


    /**
     * @dessc  资金流水
     * @return mixed
     */
    public function funHistory()
    {

        //  获取id
        $userId = $this->userInfo['id'];

        //  链接地址
        $fund = httpCurl($this->url."/api/user/fund/".$userId, "post");

        //  返回数据
        $this->assign([
            'fund_history' => $fund['data']
        ]);

        //  返回
        return $this->fetch('fund');

    }


    /**
     * @desc  收货地址列表
     * @return mixed
     */
    public function addressList()
    {

        //  获取id
        $userId = $this->userInfo['id'];

        //  链接地址
        $address = httpCurl($this->url."/api/user/address/list/".$userId, "post");

        //  返回数据
        $this->assign([
            'address' => $address['data']
        ]);

        //  返回
        return $this->fetch('addressList');

    }


    /**
     * @desc  收货地址添加
     * @return mixed
     */
    public function address()
    {

        //  获取id
        $userId = $this->userInfo['id'];

        //  链接地址
        $data = httpCurl($this->url."/api/user/info/".$userId, "post");

        //  返回数据
        $this->assign([
            'user' => $data['data']
        ]);

        //  返回
        return $this->fetch('address');

    }


    /**
     * @desc  保存地址信息
     * @param Request $request
     * @return mixed
     */
    public function saveAddress(Request $request)
    {

        //  获取传过来的参数
        $params = $request->param();

        //  链接地址
        $res = httpCurl($this->url."/api/user/address/add/", "post", $params);

        //  返回
        return $this->fetch('/index/user/addressList');

    }


    /**
     * @desc  设置默认地址
     * @param Request $request
     */
    public function setDefaultAddress(Request $request)
    {

        //  获取全部参数
        $params = $request->param();

        //  当前登录的用户id
        $params['user_id'] = $this->userInfo['id'];

        //  链接拿到数据
        httpCurl($this->url.'/api/set/default/address', 'post', $params);

        //  返回
        return $this->redirect('/index/user/addressList');

    }


}
