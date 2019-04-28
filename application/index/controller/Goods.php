<?php

namespace app\index\controller;

use think\Controller;
use think\Request;

class Goods extends Controller
{

    /**
     * @desc  加入购物车
     * @param Request $request
     */
    public function addCart(Request $request)
    {

        //  获取全部参数
        $params = $request->param();

        //  判断用户未登录
        if(empty($this->userInfo)){
            return $this->redirect('/index/login/login');
        }

        //  判断是否有sku
        if(!isset($params['attr_sku'])){
            return $this->redirect('/index/detail/index?id='.$params['goods_id']);
        }

        //  实例化redis对象
        $redis = new \Redis();
        //  链接redis
        $redis->connect('127.0.0.1',6379);

        //  存储用户购物车的key
        $cartKey = $this->userInfo['id']."_cart";

        //  获取购物车里面的内容
        $cartInfo = $redis->get($cartKey);
        //  分割数组
        $attr_sku = implode('-', $params['attr_sku']);

        //  拼接上
        $goodsKey = $params['goods_id']."-".$attr_sku;

        //  购物车内容为空
        if(empty($cartInfo)){

            //  添加购物车的内容
            $goodsCart[$goodsKey] = $params;

        }else{

            //  获取值并且转成数组
            $goodsCart = json_decode($cartInfo, true);

            //  商品已经存储在
            if(isset($goodsCart[$goodsKey])){

                $goodsCart[$goodsKey]['nums'] += $params['nums'];

            }else{

                //  不存在的话 添加到购物车的内容
                $goodsCart[$goodsKey] = $params;

            }

        }

        //  把数据存入redis中
        $redis->setex($cartKey, 7200, json_encode($goodsCart));

        //  返回
        return $this->redirect('/index/goods/cart');

    }


    /**
     * @desc  购物车列表页面
     * @return mixed|void
     */
    public function cart()
    {

        //  获取中间件
        $url = config('api_url');

        //  判断用户未登录
        if(empty($this->userInfo)){

            return $this->redirect('/index/login/login');

        }

        //  实例化redis对象
        $redis = new \Redis();
        //  链接redis
        $redis->connect('127.0.0.1', 6379);

        //  存储用户购物车的key
        $cartKey = $this->userInfo['id']."_cart";

        //  获取redis中的购物车
        $goodsCart = $redis->get($cartKey);

        //  数据为空
        if (empty($goodsCart)) {

            return $this->redirect('/');

        }

        //  json->array
        $goodsCart = json_decode($goodsCart,true);

        // 总金额初始化为0
        $totalAmount = 0;

        //  循环购物车的数据
        foreach ($goodsCart as $key => $value) {

            //  处理数组
            $skuIds = implode(',',$value['attr_sku']);

            //  格式化sku的属性值
            $goodsCart[$key]['attr_sku'] = httpCurl($url.'/api/cart/goods/attr',['sku_ids' => $skuIds]);

            //  总金额 = 商品价格 * 商品个数
            $totalAmount += $value['market_price'] * $value['nums'];

        }

        //  总金额
        $totalAmount = number_format($totalAmount,2);

        //  返回数据
        $this->assign([
            'goods_cart' => $goodsCart,
            'total_amount' => $totalAmount
        ]);

        //  返回
        return $this->fetch('cart');

    }


    /**
     * @desc  删除购物车
     * @param Request $request
     */
    public function delCart(Request $request)
    {

        //  获取全部参数
        $param = $request->param();

        //  获取其中的key
        $key = $param['key'];

        //  实例化redis对象
        $redis = new \Redis();
        $redis->connect('127.0.0.1',6379);

        //  存储用户购物车的key
        $cartKey = $this->userInfo['id']."_cart";

        //  根据key获取期中数据
        $goodsCart = $redis->get($cartKey);

        //  json->array
        $goodsCart = json_decode($goodsCart,true);

        //  删除该数据的key
        unset($goodsCart[$key]);

        //  把数据存放在redis
        $redis->setex($cartKey, 7200, json_encode($goodsCart));

        //  返回
        return $this->redirect('/index/goods/cart');

    }


    /**
     * @desc  订单页面
     * @return mixed|void
     */
    public function order()
    {

        //  判断用户未登录
        if(empty($this->userInfo)){
            return $this->redirect('/index/login');
        }

        $url = config('api_url');

        //  获取商品清单数据表数据
        //  实例化redis对象
        $redis = new \Redis();
        $redis->connect('127.0.0.1',6379);

        //  存储用户购物车的key
        $cartKey = $this->userInfo['id']."_cart";

        //  获取key值
        $goodsCart = $redis->get($cartKey);

        //  没有的话就重定向首页
        if (empty($goodsCart)) {
            return $this->redirect('/');
        }

        //  转换为redis对象
        $goodsCart = json_decode($goodsCart, true);

        //  总数量和总价格初始化为0
        $totalAmount = $totalNums = 0;

        //  循环商品key值
        foreach ($goodsCart as $key => $value) {

            //  处理字符串
            $skuIds = implode(',', $value['attr_sku']);

            //  格式化sku的属性值
            $goodsCart[$key]['attr_sku'] = httpCurl($url.'/api/cart/goods/attr','post',['sku_ids' => $skuIds]);

            //  总价格
            $totalAmount += $value['market_price'] * $value['nums'];

            //  总数量
            $totalNums += $value['nums'];

        }

        //  总金额
        $totalAmount = number_format($totalAmount,2);

        //  获取商品清单数据表数据
        //  收货人地址信息
        $userId = $this->userInfo['id'];

        $address = $redis->get($userId.'address');

        if (empty($address)) {
            $address = httpCurl($url."/api/user/address/list/".$userId,"post");

            $redis->setex($userId."_address",1800,json_encode($address));
        }else{

            $address = json_decode($address,true);

        }

        //  配送方式
        $shipping = $redis->get('shipping');

        if (empty($shipping)) {
            $shipping = httpCurl($url."/api/shipping","post");

            $redis->setex('shipping',1800,json_encode($shipping));
        }else{

            $shipping = json_decode($shipping,true);

        }

        //  支付方式
        $payment = httpCurl($url."/api/payment","post");

        //  组装数据
        $this->assign([
            'goods_cart' => $goodsCart,
            'total_amount' => $totalAmount,
            'total_nums' => $totalNums,
            'address' => $address['data'],
            'payment' => $payment,
            'shipping' => $shipping,
        ]);

        //  返回
        return $this->fetch('order');

    }


    //  表单提交
    public function submitOrder(Request $request)
    {

        //  获取全部数据
        $params = $request->param();

        $url = config('api_url');

        //  创建订单接口
        $res = httpCurl($url."/api/create/order","post",$params);

        if ($res['code'] == 2000) {

            //  实例化redis对象
            $redis = new \Redis();
            $redis->connect('127.0.0.1', 6379);
            $redis->del($this->userInfo['id']."_cart");

            $param = [
                'order_sn' => $res['data']['order_sn'],
                'pay_price' => $params['pay_price'],
                'subject' => $res['note']
            ];

            //  重定向地址
            return $this->redirect('/index/goods/orderSuccess?'.http_build_query($param));

        } else {
            echo $res['msg'];

            exit;
        }

    }


    /**
     * @desc  同步回调地址
     * @param Request $request
     * @return mixed
     */
    public function orderSuccess(Request $request)
    {

        //  获取全部参数
        $params = $request->param();

        $params['url'] = config('api_url');

        //  返回过去全把数据
        $this->assign($params);

        //  跳转
        return $this->fetch('orderSuccess');

    }


    /**
     * @desc  异步回调地址
     * @return mixed
     */
    public function returnUrl()
    {

        //  跳转
        return $this->fetch('paySuccess');

    }


}
