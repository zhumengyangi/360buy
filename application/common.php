<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

    /**
     * @desc  设置curl的http请求
     * @param $url  请求地址
     * @param string $method  请求方式GET|POST
     * @param array $data   请求数据 post
     * @return mixed
     */
    function httpCurl($url, $method = "get", $data = [])
    {

        //  初始化curl
        $curl = curl_init();

        //  设置抓取数据的地址
        curl_setopt($curl, CURLOPT_URL,$url);

        //  设置文件流形式返回抓取的数据
        curl_setopt($curl, CURLOPT_RETURNRANSFER,1);

        if($method == "post"){

            //  设置post方式请求数据
            curl_setopt($curl, CURLOPT_POST, true);

            //  传递post请求的数据
            curl_setopt($curl, CURLOPT_POSTFILEDS, $data);

        }

        //  执行curl操作
        $output = curl_exec($curl);

        //  关闭释放curl资源
        curl_close($curl);

        //  返回
        return json_decode($output,true);

    }