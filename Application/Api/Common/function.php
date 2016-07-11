<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2016 jingkaigz.com
// +----------------------------------------------------------------------
// | Author: xiezh <xzh13692763394@gmail.com>
// +----------------------------------------------------------------------

/**
 * API接口公共文件
 * 主要定义API接口公共函数库
 */

function imgDomain($path)
{
    if($path){
        return 'http://'.$_SERVER['HTTP_HOST'].'/Public/'.$path;
    }else{
        return $path;
    }
}


/**
 * 判断两个值是否一样
 * @param $arr
 */
function Judge($item1,$item2)
{
    if($item1 == $item2){
        return true;
    }else{
        return false;
    }
}