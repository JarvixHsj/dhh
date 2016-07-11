<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

/**
 * 系统配文件
 * 所有系统级别的配置
 */
$CommonConfig = include(APP_PATH . 'Common/Conf/config.php');

$Arr =  array(
    /* 数据库配置 */
//    'DB_TYPE'   => 'mysqli', // 数据库类型
//    'DB_HOST'   => 'jingkaigz.com', // 服务器地址
//    'DB_NAME'   => 'jingkai_dev_donghuahang', // 数据库名
//    'DB_USER'   => 'dev_donghuahang', // 用户名
//    'DB_PWD'    => 'donghuahang@mysql888',  // 密码
//    'DB_PORT'   => '3306', // 端口
//    'DB_PREFIX' => 'dhh_', // 数据库表前缀

    'DB_TYPE'   => 'mysqli', // 数据库类型
    'DB_HOST'   => '127.0.0.1', // 服务器地址
    'DB_NAME'   => 'dhh', // 数据库名
    'DB_USER'   => 'root', // 用户名
    'DB_PWD'    => 'root',  // 密码
    'DB_PORT'   => '3306', // 端口
    'DB_PREFIX' => 'dhh_', // 数据库表前缀
    'DB_FIELDS_CACHE'           =>  FALSE,

    'EXPIRATION_TIME' => 10,    //单位是分钟（请自行转换好在写）
);
return array_merge($CommonConfig,$Arr);

