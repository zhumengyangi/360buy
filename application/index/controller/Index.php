<?php

namespace app\index\controller;

use think\Controller;
use think\Request;

class Index extends Controller
{


    public function index()
    {

        //  请求接口的域名地址
        $url = config('api_url');

        //  请求首页的分类接口
        $category = httpCurl($url."/api/home/category","post");

        //  获取首页banner图的广告
        $banner = httpCurl($url."/api/home/ad","post",['postion_id'=>1, 'nums'=>5]);
        //  获取顶部广告
        $homeTop = httpCurl($url."/api/home/ad","post",['postion_id'=>2, 'nums'=>1]);

        //  商品类型
        //  热卖
        $hot = httpCurl($url."/api/home/goods","post",['type'=>1, 'nums'=>5]);
        //  推荐
        $recommand = httpCurl($url."/api/home/goods","post",['type'=>2, 'nums'=>5]);
        //  新品
        $news = httpCurl($url."/api/home/goods","post",['type'=>3, 'nums'=>5]);

        //  品牌列表
        $brands = httpCurl($url."/api/home/brands","post",['nums'=>9]);
        //  最新文章列表
        $articles = httpCurl($url."/api/home/newsArticle","post",['nums'=>5]);

        //  获取顶部广告
        $homeBottom = httpCurl($url."/api/home/ad","post",['postion_id'=>5, 'nums'=>5]);

        //  模板赋值
        $this->assign([
            'category' => $category['data'],//  首页商品分类
            'banner' => $banner['data'],//  首页banner图列表
            'home_top' => $homeTop['data'],//  首页顶部广告
            'hot' => $hot['data'],//  首页热卖商品
            'recommand' => $recommand['data'],//  首页推荐商品
            'news' => $news['data'],//  首页最新商品
            'brands' => $brands['data'],//  首页品牌列表
            'articles' => $articles['data'],//  首页 最新文章列表
            'homt_bottom' => $homeBottom['data'],//  首页顶部广告
        ]);


        return $this->fetch('index');
    }


}
