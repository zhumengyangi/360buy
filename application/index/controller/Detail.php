<?php

namespace app\index\controller;

use think\Controller;
use think\Request;

class Detail extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index(Request $request)
    {
        
        $params = $request->param();

        //  请求接口的域名地址
        $url = config('api_url');

        //  首页分类接口
        $category = httpCurl($url."/api/home/category",'post');

        //  品牌列表
        $brands = httpCurl($url."/api/home/brands",'post',['nums' => 9]);

        //  商品详情信息接口
        $goods = httpCurl($url."/api/goods/detail".$params['id'],'post');

        $this->assign([
            'category' => $category['data'],
            'brands' => $brands['data'],
            'goods' => $goods['data']['goods'],
            'gallery' => $goods['data']['gallery'],
            'spu' => $goods['data']['spu'],
            'sku' => $goods['data']['sku'],
        ]);

        return $this->fetch('index');
    }

}
